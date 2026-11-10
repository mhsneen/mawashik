<?php

namespace App\Http\Controllers\Api\V1\Client;

use App\Http\Controllers\Controller;
use App\Models\{Order, DiscountCode, Setting, Product, Address, Wallet, WalletTransaction, AttributeValue};
use App\Traits\ApiTrait;
use App\Http\Requests\Api\V1\{AddCartRequest};
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Http\Requests\Api\V1\CalculateCartRequest;
use App\Services\DeliveryPriceCalculator;
use App\Services\ProductPriceCalculator;
use App\Services\LoyaltyPointsService;
use InvalidArgumentException;
use App\Http\Resources\Api\V1\CartItemResource;

class CartApiController extends Controller
{
    use ApiTrait;

    public function store(AddCartRequest $request, ProductPriceCalculator $calculator, DeliveryPriceCalculator $deliveryCalculator, \App\Services\StoreStatusService $storeStatus, LoyaltyPointsService $loyaltyService)
    {
        try {
            DB::beginTransaction();

            $deliveryDate = $request->delivery_date;
            $deliveryTime = $request->delivery_time;
            $deliveryType = $request->delivery_type ?? 'delivery';

            $paymentMethod = $request->payment_method;
            if (!in_array($paymentMethod, ['cash', 'wallet'])) {
                $paymentMethod = 'online';
            }

            $deliveryPrice = 0;
            $address = null;

            if ($deliveryType === 'delivery') {
                $address = $this->resolveAddress($request);

                // ✅ جلب التاجر من أول منتج في السلة
                $firstProduct = Product::with('trader')->find($request->cart[0]['product_id']);
                $trader = $firstProduct?->trader;

                if (!$trader || !$trader->store_lat || !$trader->store_lng) {
                    throw new InvalidArgumentException(__('api.Trader_Location_Not_Available'));
                }

                // ✅ حساب التوصيل بين التاجر والعميل
                $delivery = $deliveryCalculator->calculate(
                    (float) $trader->store_lat,
                    (float) $trader->store_lng,
                    (float) $address['latitude'],
                    (float) $address['longitude']
                );
                $deliveryPrice = $delivery['price'];
            } elseif ($deliveryType === 'pickup') {
                $openPickup = Setting::where('key', 'open_pickup')->value('plain_value');
                if ($openPickup === '0') {
                    throw new InvalidArgumentException(__('api.Pickup_Not_Available'));
                }
            }

            $hasScheduledDelivery = $deliveryDate && $deliveryTime;
            $requestedDateTime = $hasScheduledDelivery
                ? Carbon::parse($deliveryDate . ' ' . $deliveryTime)
                : now();

            if ($hasScheduledDelivery && $requestedDateTime->isPast()) {
                throw new InvalidArgumentException(__('api.No time in the past can be chosen'));
            }

            if (!$storeStatus->isOpenNow($requestedDateTime->toDateTimeString())) {
                throw new InvalidArgumentException($storeStatus->closingMessage());
            }

            $totalItems = 0;
            $totalPrice = 0;
            $totalSlaughter = 0; // ✅ إجمالي الذبح
            $discountAmount = 0;
            $user = $request->user();
            $couponId = null;

            $initialStatus = $paymentMethod === 'online' ? 7 : 1;

            $order = Order::create([
                'user_id' => $user->id,
                'address_id' => $address['id'] ?? null,
                'delivery_type' => $deliveryType,
                'payment_method' => $paymentMethod,
                'delivery_price' => $deliveryPrice,
                'delivery_date' => $request->delivery_date,
                'delivery_time' => $request->delivery_time,
                'total_items' => 0,
                'total_price' => 0,
                'discount_amount' => 0,
                'final_price' => 0,
                'status_id' =>  $initialStatus,
                'read' => 0,
                'transaction_id' => $request->transaction_id,
                'note' => $request->note,
                'address' => $address['address'] ?? null,
                'latitude' => $address['latitude'] ?? null,
                'longitude' => $address['longitude'] ?? null,
                'added_tax' => (float) Setting::where('key', 'added_tax')->value('plain_value') ?? 0,
                'delivery_pin' => $deliveryType === 'delivery' ? Order::generateDeliveryPin() : null,
            ]);

            foreach ($request->cart as $item) {
                $product = Product::with([
                    'variants' => fn($q) => $q->where('is_active', true)->with('attributeValues'),
                    'attributeValues.attribute',
                ])->find($item['product_id']);

                if (!$product) {
                    throw new InvalidArgumentException(__('api.ID_Not_Found'));
                }

                $calc = $calculator->calculate(
                    $product,
                    $item['variant_id'] ?? null,
                    $item['modifier_value_ids'] ?? []
                );

                // ✅ حساب الذبح للمنتج
                $isSlaughterSelected = filter_var($item['slaughter_selected'] ?? false, FILTER_VALIDATE_BOOLEAN);
                $slaughterPricePerItem = 0;
                $slaughterTotalForItem = 0;

                if ($isSlaughterSelected && $product->slaughter_available) {
                    $slaughterPricePerItem = $product->slaughter_price_effective;
                    $slaughterTotalForItem = $slaughterPricePerItem * $item['quantity'];
                }

                // ✅ المجموع يشمل المنتج + الذبح
                $productSubtotal = round($calc['unit_price'] * $item['quantity'], 2);
                $itemTotal = round($productSubtotal + $slaughterTotalForItem, 2);

                $orderItem = $order->items()->create([
                    'product_id'  => $product->id,
                    'product_variant_id' => $calc['variant']?->id,
                    'variant_label_snapshot' => $calc['variant']
                        ? $calc['variant']->attributeValues->pluck('value')->implode(' / ')
                        : null,
                    'quantity'    => $item['quantity'],
                    'unit_price'  => $calc['unit_price'],
                    'total_price' => $itemTotal,
                    // ✅ حقول الذبح
                    'slaughter_selected' => $isSlaughterSelected,
                    'slaughter_price'    => $slaughterPricePerItem,
                    'slaughter_total'    => $slaughterTotalForItem,
                ]);

                foreach ($calc['modifier_lines'] as $line) {
                    $orderItem->attributes()->create($line);
                }

                if (!empty($item['attribute_value_ids'])) {
                    $variantValueIds = $calc['variant']?->value_ids ?? [];
                    $plainAttributes = AttributeValue::whereIn('id', $item['attribute_value_ids'])
                        ->with('attribute')
                        ->get();

                    foreach ($plainAttributes as $attrValue) {
                        $alreadySavedAsModifier = collect($calc['modifier_lines'] ?? [])
                            ->contains('attribute_value_id', $attrValue->id);

                        $isPartOfVariant = in_array($attrValue->id, $variantValueIds);

                        if (!$alreadySavedAsModifier && !$isPartOfVariant) {
                            $attrName = 'خاصية';
                            if ($attrValue->attribute) {
                                $attrName = $attrValue->attribute->name ?? $attrValue->attribute->title ?? $attrValue->attribute->ar_name ?? 'خاصية';
                            }

                            $orderItem->attributes()->create([
                                'attribute_id'       => $attrValue->attribute_id,
                                'attribute_value_id' => $attrValue->id,
                                'attribute_name'     => $attrName,
                                'value_name'         => $attrValue->value,
                                'color_code'         => $attrValue->color_code ?? null,
                                'price'              => 0,
                            ]);
                        }
                    }
                }

                // ✅ خصم المخزون
                if ($product->track_quantity) {
                    $stockHolder = ($calc['variant'] && $calc['variant']->track_quantity)
                        ? $calc['variant']
                        : $product;

                    if (!is_null($stockHolder->quantity)) {
                        $lockedHolder = $stockHolder->newQuery()->lockForUpdate()->findOrFail($stockHolder->id);
                        if ($lockedHolder->quantity < $item['quantity']) {
                            throw new InvalidArgumentException(__('api.Insufficient_stock_for_product', ['name' => $product->name]));
                        }
                        $stockHolder->decrement('quantity', (float) $item['quantity']);
                    }
                }

                foreach ($calc['modifier_lines'] as $line) {
                    $pivotRow = DB::table('attribute_value_product')
                        ->where('product_id', $product->id)
                        ->where('attribute_value_id', $line['attribute_value_id'])
                        ->lockForUpdate()
                        ->first();

                    if ($pivotRow && !is_null($pivotRow->quantity)) {
                        if ($pivotRow->quantity < $item['quantity']) {
                            throw new InvalidArgumentException(__('api.Insufficient_stock_for_modifier', ['name' => $line['value_name']]));
                        }
                        DB::table('attribute_value_product')
                            ->where('product_id', $product->id)
                            ->where('attribute_value_id', $line['attribute_value_id'])
                            ->decrement('quantity', (float) $item['quantity']);
                    }
                }

                $totalItems += $item['quantity'];
                $totalPrice += $itemTotal; // ✅ يشمل الذبح
                $totalSlaughter += $slaughterTotalForItem; // ✅ إجمالي الذبح
            }

            // ✅ تطبيق كوبون الخصم
            if ($request->coupon_code) {
                [$discountAmount, $couponId] = $this->applyDiscount($order, $request->coupon_code, $totalPrice, $user);
            }

            // ✅ الحسابات المالية
            $tax = $order->added_tax ?? 0;
            $added_taxAmount = ($totalPrice - $discountAmount) * ($tax / 100);
            $subtotalBeforePoints = ($totalPrice - $discountAmount) + $added_taxAmount + $deliveryPrice;

            $redeemedPoints = (int) ($request->redeem_points ?? 0);
            $pointsDiscountValue = 0;

            if ($redeemedPoints > 0) {
                $pointsDiscountValue = $loyaltyService->validateAndCalculateRedeemDiscount(
                    $user,
                    $redeemedPoints,
                    $subtotalBeforePoints
                );

                if ($pointsDiscountValue > $subtotalBeforePoints) {
                    $pointsDiscountValue = $subtotalBeforePoints;
                }

                $loyaltyService->redeemPoints(
                    $user,
                    $redeemedPoints,
                    $order,
                    __('dashboard.loyalty_points_redeemed_description', [
                        'order_number' => $order->order_number,
                    ])
                );
            }

            $finalPrice = max(0, $subtotalBeforePoints - $pointsDiscountValue);

            $order->update([
                'total_items' => $totalItems,
                'total_price' => $totalPrice,
                'discount_amount' => $discountAmount,
                'final_price' => $finalPrice,
                'coupon_id' => $discountAmount > 0 ? $couponId : null,
                'coupon_code' => $discountAmount > 0 ? $request->coupon_code : null,
                'redeemed_points' => $redeemedPoints,
                'redeemed_points_value' => $pointsDiscountValue,
                // ✅ تخزين إجمالي الذبح على الطلب
                'slaughter_total' => $totalSlaughter,
            ]);

            // ✅ المنطق المحاسبي للدفع
            if ($finalPrice == 0) {
                $order->update([
                    'payment_status' => 'paid',
                    'paid_at' => now(),
                    'status_id' => 1,
                ]);
            } elseif ($paymentMethod === 'wallet') {
                $wallet = Wallet::where('user_id', $user->id)->lockForUpdate()->first();

                if (!$wallet || $wallet->balance < $finalPrice) {
                    throw new InvalidArgumentException(__('api.Insufficient_wallet_balance'));
                }

                $wallet->balance -= $finalPrice;
                $wallet->save();

                WalletTransaction::create([
                    'wallet_id'   => $wallet->id,
                    'order_id'    => $order->id,
                    'type'        => 'debit',
                    'amount'      => $finalPrice,
                    'description' => __('dashboard.order_purchase_description', [
                        'order_number' => $order->order_number,
                    ]),
                ]);

                $order->update([
                    'payment_status' => 'paid',
                    'paid_at' => now(),
                ]);
            }

            DB::commit();

            return $this->returnData('data', [
                'order_id'    => $order->id,
                'final_price' => $order->final_price,
                'message'     => __('api.order_received_success', ['order_id' => $order->id])
            ], __('api.order_received_success'));

        } catch (InvalidArgumentException $ex) {
            DB::rollback();
            return $this->returnError($ex->getMessage());
        } catch (Exception $ex) {
            DB::rollback();
            Log::error('Error in store order: ' . $ex->getMessage(), [
                'trace' => $ex->getTraceAsString(),
                'file' => $ex->getFile(),
                'line' => $ex->getLine(),
            ]);
            return $this->returnError(__('api.Api_Error_Contact_Programmer'));
        }
    }

    protected function resolveAddress(AddCartRequest $request): array
    {
        if ($request->address_id) {
            $address = Address::where('user_id', $request->user()->id)
                ->find($request->address_id);

            if (!$address) {
                throw new InvalidArgumentException(__('api.Invalid_Address_Selected'));
            }

            return [
                'id' => $address->id,
                'address' => $address->address,
                'latitude' => $address->latitude,
                'longitude' => $address->longitude,
            ];
        }

        return [
            'id' => null,
            'address' => $request->address,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ];
    }

    private function applyDiscount($order, $code, $totalPrice, $user)
    {
        if (!$code) return [0, null];

        $discount = DiscountCode::where('code', $code)->lockForUpdate()->first();

        if (!$discount || !$discount->isValid()) {
            throw new InvalidArgumentException(__('api.Invalid_or_expired_coupon'));
        }

        $discountAmount = ($totalPrice * $discount->discount_percentage) / 100;
        $discount->increment('usage_count');

        $discount->usages()->create([
            'order_id' => $order->id,
            'user_id' => $user->id,
            'discount_percentage' => $discount->discount_percentage,
            'discount_amount' => $discountAmount,
            'used_at' => now(),
        ]);

        return [$discountAmount, $discount->id];
    }

    public function calculateCart(CalculateCartRequest $request, ProductPriceCalculator $calculator, DeliveryPriceCalculator $deliveryCalculator, LoyaltyPointsService $loyaltyService)
    {
        try {
            $totalItems = 0;
            $totalPrice = 0;
            $totalSlaughter = 0; // ✅ إجمالي الذبح
            $lines = collect();

            foreach ($request->cart as $item) {
                $product = Product::with([
                    'variants' => fn($q) => $q->where('is_active', true)->with('attributeValues'),
                    'attributeValues.attribute',
                ])->find($item['product_id']);

                if (!$product) {
                    throw new InvalidArgumentException(__('api.ID_Not_Found'));
                }

                $calc = $calculator->calculate(
                    $product,
                    $item['variant_id'] ?? null,
                    $item['modifier_value_ids'] ?? []
                );

                // ✅ حساب الذبح
                $isSlaughterSelected = filter_var($item['slaughter_selected'] ?? false, FILTER_VALIDATE_BOOLEAN);
                $slaughterPricePerItem = 0;
                $slaughterTotalForItem = 0;

                if ($isSlaughterSelected && $product->slaughter_available) {
                    $slaughterPricePerItem = $product->slaughter_price_effective;
                    $slaughterTotalForItem = $slaughterPricePerItem * $item['quantity'];
                }

                // ✅ المجموع يشمل المنتج + الذبح
                $productSubtotal = round($calc['unit_price'] * $item['quantity'], 2);
                $itemTotal = round($productSubtotal + $slaughterTotalForItem, 2);

                $dataForResource = [
                    'product'   => $product,
                    'calc'      => $calc,
                    'quantity'  => $item['quantity'],
                    'itemTotal' => $itemTotal,
                    // ✅ معلومات الذبح للتطبيق
                    'slaughter_selected' => $isSlaughterSelected,
                    'slaughter_price'     => $slaughterPricePerItem,
                    'slaughter_total'     => $slaughterTotalForItem,
                ];

                $lines->push(new CartItemResource($dataForResource));

                $totalItems += $item['quantity'];
                $totalPrice += $itemTotal;
                $totalSlaughter += $slaughterTotalForItem;
            }

            $deliveryPrice = 0;
            $distance = null;

            if ($request->address_id || ($request->latitude && $request->longitude)) {
                if ($request->address_id) {
                    if (!$request->user()) {
                        throw new InvalidArgumentException(__('api.Login_Required_For_Address'));
                    }

                    $address = Address::where('user_id', $request->user()->id)->find($request->address_id);
                    if (!$address) {
                        throw new InvalidArgumentException(__('api.Invalid_Address_Selected'));
                    }
                    $lat = $address->latitude;
                    $lng = $address->longitude;
                } else {
                    $lat = $request->latitude;
                    $lng = $request->longitude;
                }

                // ✅ جلب التاجر من أول منتج
                $firstProduct = Product::with('trader')->find($request->cart[0]['product_id']);
                $trader = $firstProduct?->trader;

                if (!$trader || !$trader->store_lat || !$trader->store_lng) {
                    throw new InvalidArgumentException(__('api.Trader_Location_Not_Available'));
                }

                $delivery = $deliveryCalculator->calculate(
                    (float) $trader->store_lat,
                    (float) $trader->store_lng,
                    (float) $lat,
                    (float) $lng
                );
                $deliveryPrice = $delivery['price'];
                $distance = $delivery['distance'];
            }

            $subtotalBeforePoints = round($totalPrice + $deliveryPrice, 2);

            $pointsData = null;
            $user = $request->user();

            if ($user) {
                $maxRedeemable = $loyaltyService->maxRedeemablePoints($user, $subtotalBeforePoints);

                $requestedPoints = (int) ($request->redeem_points ?? 0);
                $pointsDiscountValue = 0;

                if ($requestedPoints > 0) {
                    $pointsDiscountValue = $loyaltyService->validateAndCalculateRedeemDiscount(
                        $user,
                        $requestedPoints,
                        $subtotalBeforePoints
                    );
                }

                $pointsData = [
                    'available_balance' => $user->loyalty_points_balance,
                    'max_redeemable_for_this_order' => $maxRedeemable,
                    'requested_points' => $requestedPoints,
                    'discount_value' => $pointsDiscountValue,
                ];
            }

            return $this->returnData('data', [
                'items' => $lines,
                'total_items' => $totalItems,
                'total_price' => round($totalPrice, 2),
                'slaughter_total' => round($totalSlaughter, 2), // ✅ إجمالي الذبح
                'delivery_distance_km' => $distance,
                'delivery_price' => $deliveryPrice,
                'loyalty_points' => $pointsData,
                'grand_total' => round($subtotalBeforePoints - ($pointsData['discount_value'] ?? 0), 2),
            ], __('api.Api_Return_Data'));

        } catch (InvalidArgumentException $ex) {
            return $this->returnError($ex->getMessage());
        } catch (Exception $ex) {
            Log::error('calculateCart: ' . $ex->getMessage(), [
                'trace' => $ex->getTraceAsString(),
            ]);
            return $this->returnError(__('api.Api_Error_Contact_Programmer'));
        }
    }
}
