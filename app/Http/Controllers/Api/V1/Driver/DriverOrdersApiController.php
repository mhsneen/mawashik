<?php

namespace App\Http\Controllers\Api\V1\Driver;

use App\Http\Controllers\Controller;
use App\Models\{Order, Wallet, WalletTransaction};
use App\Traits\ApiTrait;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class DriverOrdersApiController extends Controller
{
    use ApiTrait;

    public function my_Orders(Request $request)
    {
        try {
            $driver = $request->user();

            $data = Order::where('driver_id', $driver->id)
                            ->where('status_id', 3)
                ->with([
                    'user:id,name,mobile,image',
                    'status:id',
                    'address:id,address,latitude,longitude',
                    'items' => function ($query) {
                        $query->select('id', 'order_id', 'product_id', 'quantity', 'unit_price', 'total_price')
                            ->with(['product:id,image,category_id']);
                    }
                ])
                ->orderBy('id', 'DESC')
                ->get()
                ->map(function ($order) {
                    return $this->formatOrder($order, withPin: false);
                });

            return $this->returnData('data', $data, __('api.Api_Return_Data'));
        } catch (Exception $ex) {
            Log::error('driver my_Orders: ' . $ex->getMessage(), ['trace' => $ex->getTraceAsString()]);
            return $this->returnError(__('api.Api_Error_Contact_Programmer'));
        }
    }

    public function order_Details(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required|integer|exists:orders,id',
        ]);

        if ($validator->fails()) {
            return $this->returnError($validator->errors()->first());
        }

        try {
            $driver = $request->user();

            // ✅ المندوب يرى الطلب فقط إذا كان معيّناً له، أو معروضاً عليه (عرض معلّق)
            $order = Order::where('id', $request->order_id)
                ->where(function ($q) use ($driver) {
                    $q->where('driver_id', $driver->id)
                      ->orWhereHas('driverOffers', function ($o) use ($driver) {
                          $o->where('driver_id', $driver->id)->where('status', 'pending');
                      });
                })
                ->with([
                    'user:id,name,mobile,image',
                    'status:id',
                    'address:id,address,latitude,longitude',
                    'items' => function ($query) {
                        $query->select('id', 'order_id', 'product_id', 'product_variant_id', 'variant_label_snapshot', 'quantity', 'unit_price', 'total_price')
                            ->with([
                                'product:id,image,category_id,unit_id',
                                'attributes:id,order_item_id,attribute_id,attribute_value_id,attribute_name,value_name,color_code,price',
                            ]);
                    }
                ])
                ->first();

            if (!$order) {
                return $this->returnError(__('api.ID_Not_Found'));
            }

            $showPin = $order->status_id === 4;

            return $this->returnData('data', $this->formatOrder($order, withPin: $showPin), __('api.Api_Return_Data'));
        } catch (Exception $ex) {
            Log::error('driver order_Details: ' . $ex->getMessage(), ['trace' => $ex->getTraceAsString()]);
            return $this->returnError(__('api.Api_Error_Contact_Programmer'));
        }
    }

    public function accept_Order(Request $request, \App\Services\DriverAssignmentService $assignmentService)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required|integer|exists:orders,id',
        ]);

        if ($validator->fails()) {
            return $this->returnError($validator->errors()->first());
        }

        try {
            $driver = $request->user();
            $order = Order::find($request->order_id);

            if (!$order) {
                return $this->returnError(__('api.ID_Not_Found'));
            }

            $assignmentService->acceptOffer($order, $driver);

            return $this->returnSuccessMessage(__('api.Order_accepted_successfully'));
        } catch (\InvalidArgumentException $ex) {
            return $this->returnError($ex->getMessage());
        } catch (Exception $ex) {
            Log::error('driver accept_Order: ' . $ex->getMessage(), ['trace' => $ex->getTraceAsString()]);
            return $this->returnError(__('api.Api_Error_Contact_Programmer'));
        }
    }

    public function reject_Order(Request $request, \App\Services\DriverAssignmentService $assignmentService)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required|integer|exists:orders,id',
        ]);

        if ($validator->fails()) {
            return $this->returnError($validator->errors()->first());
        }

        try {
            $driver = $request->user();
            $order = Order::find($request->order_id);

            if (!$order) {
                return $this->returnError(__('api.ID_Not_Found'));
            }

            $assignmentService->rejectOffer($order, $driver);

            return $this->returnSuccessMessage(__('api.Order_rejected_successfully'));
        } catch (\InvalidArgumentException $ex) {
            return $this->returnError($ex->getMessage());
        } catch (Exception $ex) {
            Log::error('driver reject_Order: ' . $ex->getMessage(), ['trace' => $ex->getTraceAsString()]);
            return $this->returnError(__('api.Api_Error_Contact_Programmer'));
        }
    }

    public function pending_Offers(Request $request)
    {
        try {
            $driver = $request->user();

            $offers = \App\Models\OrderDriverOffer::where('driver_id', $driver->id)
                ->where('status', 'pending')
                ->whereHas('order', fn($q) => $q->where('status_id', 3)->whereNull('driver_id'))
                ->with(['order' => function ($q) {
                    $q->select('id', 'user_id', 'address_id', 'order_number', 'delivery_price', 'address', 'latitude', 'longitude', 'final_price', 'total_items', 'payment_method', 'created_at')
                        ->with('user:id,name,mobile,image', 'address:id,address,latitude,longitude');
                }])
                ->latest()
                ->get();

            $data = $offers->map(function($offer) {
                $order = $offer->order;
                
                $latitude = $order->latitude ?: ($order->address->latitude ?? null);
                $longitude = $order->longitude ?: ($order->address->longitude ?? null);
                $addressText = $order->address ?: ($order->address->address ?? '');

                return [
                    'offer_id'       => $offer->id,
                    'order_id'       => $order->id,
                    'order_number'   => $order->order_number,
                    'client_name'    => $order->user->name ?? '',
                    'client_mobile'  => $order->user->mobile ?? '',
                    'client_image'   => $order->user->image_url ?? '',
                    'address'        => $addressText,
                    'latitude'       => $latitude,
                    'longitude'      => $longitude,
                    'delivery_price' => $order->delivery_price,
                    'final_price'    => $order->final_price,
                    'total_items'    => $order->total_items,
                    'payment_method' => $order->payment_method,
                    'offered_at'     => $offer->offered_at ? Carbon::parse($offer->offered_at)->diffForHumans() : null,
                ];
            });

            return $this->returnData('data', $data, __('api.Api_Return_Data'));
        } catch (Exception $ex) {
            Log::error('driver pending_Offers: ' . $ex->getMessage(), ['trace' => $ex->getTraceAsString()]);
            return $this->returnError(__('api.Api_Error_Contact_Programmer'));
        }
    }

public function deliver_Order(Request $request)
{
    $validator = Validator::make($request->all(), [
        'order_id'         => 'required|integer|exists:orders,id',
        'delivery_pin'     => 'required|string|size:4',
        'driver_note'      => 'nullable|string|max:500',
    ]);

    if ($validator->fails()) {
        return $this->returnError($validator->errors()->first());
    }

    try {
        $driver = $request->user();

        $order = Order::where('id', $request->order_id)
            ->where('driver_id', $driver->id)
            ->where('status_id', 4)
            ->first();

        if (!$order) {
            return $this->returnError(__('api.ID_Not_Found'));
        }

        if ($order->delivery_pin !== $request->delivery_pin) {
            return $this->returnError(__('api.Invalid_delivery_pin'));
        }

        DB::beginTransaction();

        // ✅ نستخدم طريقة الدفع الأصلية للطلب بدلاً من إرسالها من الفلاتر
        $order->update([
            'status_id'          => 5,
            'delivered_at'       => now(),
            'driver_note'        => $request->driver_note,
        ]);

        $this->recordDriverEarning($driver->id, $order, $order->payment_method);

        DB::commit();

        return $this->returnSuccessMessage(__('api.Order_delivered_successfully'));
    } catch (Exception $ex) {
        DB::rollback();
        Log::error('driver deliver_Order: ' . $ex->getMessage(), ['trace' => $ex->getTraceAsString()]);
        return $this->returnError(__('api.Api_Error_Contact_Programmer'));
    }
}

    public function order_History(Request $request)
    {
        try {
            $driver = $request->user();

            $data = Order::where('driver_id', $driver->id)
                ->where('status_id', 5)
                ->with([
                    'user:id,name,mobile,image',
                    'status:id',
                    'address:id,address,latitude,longitude',
                ])
                ->orderBy('id', 'DESC')
                ->paginate(15);

            $data->getCollection()->transform(function ($order) {
                return $this->formatOrder($order, withPin: false);
            });

            return $this->returnData('data', $data, __('api.Api_Return_Data'));
        } catch (Exception $ex) {
            Log::error('driver order_History: ' . $ex->getMessage(), ['trace' => $ex->getTraceAsString()]);
            return $this->returnError(__('api.Api_Error_Contact_Programmer'));
        }
    }

    public function in_Delivery_Orders(Request $request)
    {
        try {
            $driver = $request->user();

            $data = Order::where('driver_id', $driver->id)
                ->where('status_id', 4)
                ->with([
                    'user:id,name,mobile,image',
                    'status:id',
                    'address:id,address,latitude,longitude',
                ])
                ->orderBy('id', 'DESC')
                ->get()
                ->map(function ($order) {
                    return $this->formatOrder($order, withPin: true);
                });

            return $this->returnData('data', $data, __('api.Api_Return_Data'));
        } catch (Exception $ex) {
            Log::error('driver in_Delivery_Orders: ' . $ex->getMessage(), ['trace' => $ex->getTraceAsString()]);
            return $this->returnError(__('api.Api_Error_Contact_Programmer'));
        }
    }

    // ─────────────────────────── Helpers ───────────────────────────

    /**
     * تنسيق بيانات الطلب للسائق (مطابق لـ OrderResource الخاص بالعميل)
     */
      private function formatOrder(Order $order, bool $withPin = false): array
    {
        // ✅ منطق الفالباك (Fallback) للوكيشن والعنوان
        $latitude = $order->latitude;
        $longitude = $order->longitude;
        $addressText = $order->address;

        if ((!$latitude || !$longitude) && $order->address_id && $order->address) {
            $latitude = $order->address->latitude;
            $longitude = $order->address->longitude;
            $addressText = $order->address->address ?? $addressText;
        }

        $result = [
            'id'              => $order->id,
            'order_number'    => $order->order_number, // ✅ أضف هذا السطر هنا
            'status_id'       => $order->status_id,
            'status_name'     => $order->status?->name,
            'delivery_type'   => $order->delivery_type,
            'payment_method'  => $order->payment_method,
            'final_price'     => $order->final_price,
            'delivery_price'  => $order->delivery_price,
            'total_items'     => $order->total_items,
            'note'            => $order->note,
            'address'         => $addressText, 
            'latitude'        => $latitude,    
            'longitude'       => $longitude,   
            'payment_collected' => $order->payment_collected,
            
            // ✅ التعديل هنا باستخدام Carbon::parse لتفادي خطأ format() on string
            'created_at'          => $order->created_at ? Carbon::parse($order->created_at)->format('Y-m-d H:i') : null,
            'out_for_delivery_at' => $order->out_for_delivery_at ? Carbon::parse($order->out_for_delivery_at)->format('Y-m-d H:i') : null,
            'delivered_at'        => $order->delivered_at ? Carbon::parse($order->delivered_at)->format('Y-m-d H:i') : null,

            // Mawashik Waseet - financial fields for the driver (commission + deposit + remaining + slaughter)
            'deposit_amount'      => (float) ($order->deposit_amount ?? 0),
            'remaining_amount'    => (float) ($order->remaining_amount ?? 0),
            'slaughter_total'     => (float) ($order->slaughter_total ?? 0),
            'commission_driver'   => (float) ($order->commission_driver ?? 0),
            'commission_trader'   => (float) ($order->commission_trader ?? 0),

            // Cash amount the driver must collect from the client (cash on delivery):
            // final price minus any deposit already paid up-front.
            'cash_to_collect'     => (float) $order->cash_to_collect,

            'client' => $order->user ? [
                'id'        => $order->user->id,
                'name'      => $order->user->name,
                'mobile'    => $order->user->mobile,
                'image_url' => $order->user->image_url,
            ] : null,
            
            // ✅ الحقول المالية (مطابقة لـ OrderResource)
            'total_price'     => (float) $order->total_price,
            'discount_amount' => (float) $order->discount_amount,
            'coupon_code'     => $order->coupon_code ?? '',
            'added_tax'       => (float) $order->added_tax,
            'tax_amount'      => (float) $order->tax_amount,
              // ✅ إضافة حقول النقاط لإرسالها للتطبيق
            'redeemed_points'       => (int) $order->redeemed_points,
            'redeemed_points_value' => (float) $order->redeemed_points_value,
            
            'items' => $order->relationLoaded('items')
                ? $order->items->map(fn($item) => [
                    'id'             => $item->id,
                    'product_id'     => $item->product_id,
                    'product_name'   => $item->product?->name ?? '',
                    'product_image'  => $item->product?->base_image_url ?? '', // ✅ استخدام base_image_url
                    'variant_label'  => $item->variant_label_snapshot ?? '',
                    'quantity'       => (float) $item->quantity,
                    'unit_price'     => (float) $item->unit_price,
                    'total_price'    => (float) $item->total_price,
                    // ✅ إضافة الإضافات (Attributes) مطابقة لـ OrderItemAttributeResource
                    'attributes' => $item->relationLoaded('attributes') ? $item->attributes->map(fn($attr) => [
                        'attribute_name' => $attr->attribute_name ?? '',
                        'value_name'     => $attr->value_name ?? '',
                        'color_code'     => $attr->color_code ?? '',
                        'price'          => (float) $attr->price,
                    ]) : [],
                ])
                : [],
        ];

        // تم إزالة PIN نهائياً لأن السائق يجب أن يستلمه من العميل ولا يظهر له
        // if ($withPin) {
        //     $result['delivery_pin'] = $order->delivery_pin;
        // }

        return $result;
    }

    /**
     * تسجيل كسب السائق في محفظته (كاش)
     */
      /**
     * تسجيل المعاملات المالية للسائق في محفظته
     */
    /**
     * تسجيل المعاملات المالية للسائق في محفظته
     */
    private function recordDriverEarning(int $driverId, Order $order, string $collectionMethod = 'cash'): void
    {
        $wallet = Wallet::firstOrCreate(
            ['user_id' => $driverId],
            ['balance' => 0, 'rolename' => 'driver']
        );

        $deliveryFee = $order->delivery_price ?? 0;

        // 1. إذا كان الدفع كاش، السائق استلم من العميل المبلغ المطلوب تحصيله فقط
        //    (الإجمالي ناقص أي عربون دُفع مسبقاً) — وليس final_price كاملاً دائماً.
        if ($collectionMethod === 'cash') {
            $cashCollected = (float) $order->cash_to_collect;

            if ($cashCollected > 0) {
                // خصم المبلغ المحصَّل من محفظة السائق (استلمه نقداً ويجب تسليمه)
                $wallet->decrement('balance', $cashCollected);

                WalletTransaction::create([
                    'wallet_id'   => $wallet->id,
                    'order_id'    => $order->id,
                    'type'        => 'debit',
                    'amount'      => $cashCollected,
                    'description' => __('api.driver_cash_collected', ['order_id' => $order->order_number]),
                ]);
            }
        }

        // 2. في كل الحالات (كاش أو أونلاين)، السائق يستحق أجر التوصيل (delivery_price)
        if ($deliveryFee > 0) {
            // إضافة أجر التوصيل لمحفظة السائق
            $wallet->increment('balance', $deliveryFee);

            WalletTransaction::create([
                'wallet_id'   => $wallet->id,
                'order_id'    => $order->id,
                'type'        => 'credit',
                'amount'      => $deliveryFee,
                'description' => __('api.driver_delivery_fee', ['order_id' => $order->order_number]),
            ]);
        }
    }
}