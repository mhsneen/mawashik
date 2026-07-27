<?php

namespace App\Http\Controllers\Api\V1\Client;

use App\Http\Controllers\Controller;
use App\Models\{Order, DiscountCode, Setting, Product, Address};
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

class CartApiController extends Controller
{
    use ApiTrait;

    public function store(AddCartRequest $request, ProductPriceCalculator $calculator, DeliveryPriceCalculator $deliveryCalculator, \App\Services\StoreStatusService $storeStatus, LoyaltyPointsService $loyaltyService)
    {
        try {
            DB::beginTransaction();

            $deliveryDate = $request->delivery_date; // 2026-01-06
            $deliveryTime = $request->delivery_time; // 12:43:57

            $deliveryType = $request->delivery_type ?? 'delivery';
            $deliveryPrice = 0;
            $address = null;

            // ✅ التوصيل: نحدد العنوان (محفوظ سابقاً أو مُرسل مباشرة) ونحسب سعر التوصيل
            // من نفس الإعدادات (ثابت أو حسب المسافة) عن طريق DeliveryPriceCalculator.
            if ($deliveryType === 'delivery') {
                $address = $this->resolveAddress($request);

                $delivery = $deliveryCalculator->calculate((float) $address['latitude'], (float) $address['longitude']);
                $deliveryPrice = $delivery['price'];
            } elseif ($deliveryType === 'pickup') {
                if (Setting::where('key', 'open_pickup')->value('plain_value') == '0') {
                    throw new InvalidArgumentException(__('api.Pickup_Not_Available'));
                }
            }

            // ✅ فحص أيام/ساعات العمل — لكل يوم جدوله الخاص (start_work/end_work
            // مخزّنين الآن كـ JSON لكل يوم على حدة، مو وقت واحد لكل الأسبوع).
            // نتحقق من وقت التوصيل/الاستلام المطلوب نفسه، مو الوقت الحالي.
         $hasScheduledDelivery = $deliveryDate && $deliveryTime;

$requestedDateTime = $hasScheduledDelivery
    ? Carbon::parse($deliveryDate . ' ' . $deliveryTime)
    : now();

// 🔹 منع اختيار وقت في الماضي (فقط لو محدد فعلياً)
if ($hasScheduledDelivery && $requestedDateTime->isPast()) {
    throw new InvalidArgumentException(__('api.No time in the past can be chosen'));
}

if (!$storeStatus->isOpenNow($requestedDateTime->toDateTimeString())) {
    throw new InvalidArgumentException($storeStatus->closingMessage());
}

            $totalItems = 0;
            $totalPrice = 0;
            $discountAmount = 0;
            $user = $request->user();
            $couponId = null;

            // ✅ إنشاء الطلب الأساسية
            $order = Order::create([
                'user_id' => $user->id,
                'address_id' => $address['id'] ?? null,
                'delivery_type' => $deliveryType,
                'payment_method' => $request->payment_method,
                'delivery_price' => $deliveryPrice,
                'delivery_date' => $request->delivery_date,
                'delivery_time' => $request->delivery_time,
                'total_items' => 0,
                'total_price' => 0,
                'discount_amount' => 0,
                'final_price' => 0,
                'status_id' => 1,
                'read' => 0,
                'transaction_id' => $request->transaction_id,
                'note' => $request->note,
                // Snapshot لبيانات العنوان وقت الطلب — تبقى ثابتة حتى لو
                // المستخدم عدّل أو حذف العنوان المحفوظ لاحقاً.
                'address' => $address['address'] ?? null,
                'latitude' => $address['latitude'] ?? null,
                'longitude' => $address['longitude'] ?? null,
               'added_tax' => (float) Setting::where('key', 'added_tax')->value('plain_value') ?? 0,
                // كود التسليم (4 أرقام) — يُنشأ فقط للطلبات التي تحتاج توصيل
                'delivery_pin' => $deliveryType === 'delivery' ? Order::generateDeliveryPin() : null,
            ]);

            // ✅ إضافة المنتجات داخل الطلب
            // السعر يُحسب دايماً من السيرفر (المنتج/الفيرنت/الإضافات) عبر
            // ProductPriceCalculator — ما نثق بأي unit_price يرسله العميل،
            // هذا أهم نقطة أمان بمنطق السلة بالكامل.
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

                $itemTotal = round($calc['unit_price'] * $item['quantity'], 2);

                // إنشاء OrderItem
                $orderItem = $order->items()->create([
                    'product_id'  => $product->id,
                    'product_variant_id' => $calc['variant']?->id,
                    'variant_label_snapshot' => $calc['variant']
                        ? $calc['variant']->attributeValues->pluck('value')->implode(' / ')
                        : null,
                    'quantity'    => $item['quantity'],
                    'unit_price'  => $calc['unit_price'],
                    'total_price' => $itemTotal,
                ]);

                // ✅ حفظ Snapshot للإضافات المختارة (الاسم والسعر وقت الطلب)
                foreach ($calc['modifier_lines'] as $line) {
                    $orderItem->attributes()->create($line);
                }

                // ✅ تخفيض المخزون
              // تخفيض المخزون - استخدم float
    // ✅ تخفيض المخزون (فقط لو المنتج يتبع المخزون)
           if ($product->track_quantity) {
            // تحديد من يمتلك المخزون (المنتج نفسه أم الفيرنت variant)
            $stockHolder = ($calc['variant'] && $calc['variant']->track_quantity)
                ? $calc['variant']
                : $product;

            if (!is_null($stockHolder->quantity)) {
                // قفل الصف في قاعدة البيانات لمنع الطلبات المتزامنة
                $lockedHolder = clone $stockHolder;
                $lockedHolder->lockForUpdate()->findOrFail($stockHolder->id);

                // التحقق من توفر الكمية المطلوبة
                if ($lockedHolder->quantity < $item['quantity']) {
                    throw new InvalidArgumentException(__('api.Insufficient_stock_for_product', ['name' => $product->name]));
                }

                // خصم المخزون
                $stockHolder->decrement('quantity', (float) $item['quantity']);
            }
        }

    // ✅ تخفيض مخزون الإضافات (Modifiers)
    foreach ($calc['modifier_lines'] as $line) {
        $pivot = DB::table('attribute_value_product')
            ->where('product_id', $product->id)
            ->where('attribute_value_id', $line['attribute_value_id'])
            ->first();

        // ننقص فقط لو الإضافة لها مخزون محدد (quantity ليس null)
        if ($pivot && !is_null($pivot->quantity)) {
            DB::table('attribute_value_product')
                ->where('product_id', $product->id)
                ->where('attribute_value_id', $line['attribute_value_id'])
                ->decrement('quantity', (float) $item['quantity']);
        }
    }

                $totalItems += $item['quantity'];
                $totalPrice += $itemTotal;
            }

            // ✅ تطبيق الكوبون (إذا موجود)
          if ($request->coupon_code) {
            [$discountAmount, $couponId] = $this->applyDiscount($order, $request->coupon_code, $totalPrice, $user);
        }

        $tax = $order->added_tax ?? 0;
        $added_taxAmount = ($totalPrice - $discountAmount) * ($tax / 100);
        $subtotalBeforePoints = ($totalPrice - $discountAmount) + $added_taxAmount + $deliveryPrice;

        // ✅ استبدال نقاط الولاء
        $redeemedPoints = (int) ($request->redeem_points ?? 0);
        $pointsDiscountValue = 0;

        if ($redeemedPoints > 0) {
            $pointsDiscountValue = $loyaltyService->validateAndCalculateRedeemDiscount(
                $user,
                $redeemedPoints,
                $subtotalBeforePoints
            );

            $loyaltyService->redeemPoints(
                $user,
                $redeemedPoints,
                $order,
                'استبدال نقاط بالطلب #' . $order->id
            );
        }

        $finalPrice = $subtotalBeforePoints - $pointsDiscountValue;

        $order->update([
            'total_items' => $totalItems,
            'total_price' => $totalPrice,
            'discount_amount' => $discountAmount,
            'final_price' => $finalPrice,
            'coupon_id' => $discountAmount > 0 ? $couponId : null,
             'coupon_code' => $discountAmount > 0 ? $request->coupon_code : null, // ✅ أضف هذا
            'redeemed_points' => $redeemedPoints,
            'redeemed_points_value' => $pointsDiscountValue,
        ]);
            return $this->returnSuccessMessage(__('api.order_received_success', ['order_id' => $order->id]));
        } catch (InvalidArgumentException $ex) {
            // أخطاء تحقق متوقعة (فيرنت غير صالح، عنوان خارج نطاق التوصيل،
            // نفاذ مخزون، إضافة غير متاحة..) — نعرض رسالتها مباشرة للمستخدم.
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

    /**
     * يحدد بيانات العنوان المستخدمة بهذا الطلب:
     * - لو أرسل address_id: يجيبه من عناوين المستخدم المحفوظة (بعد التحقق إنه يملكه).
     * - لو أرسل latitude/longitude/address مباشرة: يستخدمها كما هي (عنوان لمرة وحدة
     *   بدون حفظه بقائمة عناوينه — مفيد لعميل ما بعده حاب يحفظ العنوان).
     *
     * @return array{id: int|null, address: string, latitude: float, longitude: float}
     */
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

        // عنوان مُرسل مباشرة بدون حفظ (required عند delivery_type=delivery — تتحقق منه AddCartRequest)
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

        $discount = DiscountCode::where('code', $code)->first();

        if (!$discount || !$discount->isValid()) {
            return [0, null];
        }
        $discountAmount = ($totalPrice * $discount->discount_percentage) / 100;
        $discount->increment('usage_count');
        // تسجيل استخدام الخصم
        $discount->usages()->create([
            'order_id' => $order->id,
            'user_id' => $user->id,
            'discount_percentage' => $discount->discount_percentage,
            'discount_amount' => $discountAmount,
            'used_at' => now(),
        ]);

        return [$discountAmount, $discount->id];
    }

    /**
     * معاينة السلة وحساب سعرها بدون إنشاء طلب فعلي — تُستخدم بشاشة
     * السلة بالتطبيق لعرض السعر الحي (Live) قبل ما يضغط المستخدم
     * "تأكيد الطلب"، وتستخدم نفس منطق الحساب المستخدم فعلياً بالحفظ.
     * لو أُرسل address_id أو latitude/longitude، بيرجع سعر التوصيل ضمن
     * الإجمالي أيضاً (اختياري تماماً).
     */
    public function calculateCart(CalculateCartRequest $request, ProductPriceCalculator $calculator, DeliveryPriceCalculator $deliveryCalculator, LoyaltyPointsService $loyaltyService)
    {
        try {
            $totalItems = 0;
            $totalPrice = 0;
            $lines = [];

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

                $itemTotal = round($calc['unit_price'] * $item['quantity'], 2);

                $lines[] = [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'variant_id' => $calc['variant']?->id,
                    'variant_label' => $calc['variant']
                        ? $calc['variant']->attributeValues->pluck('value')->implode(' / ')
                        : null,
                    'modifiers' => collect($calc['modifier_lines'])->map(fn($line) => [
                        'attribute_value_id' => $line['attribute_value_id'],
                        'name' => $line['value_name'],
                        'price' => $line['price'],
                    ]),
                    'quantity' => $item['quantity'],
                    'unit_price' => $calc['unit_price'],
                    'total_price' => $itemTotal,
                ];

                $totalItems += $item['quantity'];
                $totalPrice += $itemTotal;
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

                $delivery = $deliveryCalculator->calculate((float) $lat, (float) $lng);
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
