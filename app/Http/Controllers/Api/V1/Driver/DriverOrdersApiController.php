<?php

namespace App\Http\Controllers\Api\V1\Driver;

use App\Http\Controllers\Controller;
use App\Models\{Order, Wallet, WalletTransaction};
use App\Traits\ApiTrait;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class DriverOrdersApiController extends Controller
{
    use ApiTrait;

    /**
     * طلبيات السائق المعيّنة له (الحالة: مكتملة التجهيز = 3، أو قيد التوصيل = 4)
     * تُعرض الطلبيات التي عُيِّن لها السائق وتحتاج تدخله
     */
    public function my_Orders(Request $request)
    {
        try {
            $driver = $request->user();

            // طلبيات معيّنة للسائق بحالة 3 (جاهزة للاستلام) أو 4 (قيد التوصيل)
            $data = Order::where('driver_id', $driver->id)
                ->whereIn('status_id', [3, 4])
                ->with([
                    'user:id,name,mobile,image',
                    'status:id',
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

    /**
     * تفاصيل طلب معيّن (للسائق)
     * يُعرض عنوان العميل + كود التسليم عند حالة 4
     */
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

            $order = Order::where('id', $request->order_id)
                ->where('driver_id', $driver->id)
                ->with([
                    'user:id,name,mobile,image',
                    'status:id',
                    'address',
                    'items' => function ($query) {
                        $query->select('id', 'order_id', 'product_id', 'quantity', 'unit_price', 'total_price')
                            ->with([
                                'product:id,image,category_id',
                                'attributes' => function ($q) {
                                    $q->select('order_item_id', 'attribute_name', 'value_name', 'color_code');
                                }
                            ]);
                    }
                ])
                ->first();

            if (!$order) {
                return $this->returnError(__('api.ID_Not_Found'));
            }

            // كود التسليم يُعرض فقط عند حالة 4 (قيد التوصيل)
            $showPin = $order->status_id === 4;

            return $this->returnData('data', $this->formatOrder($order, withPin: $showPin), __('api.Api_Return_Data'));
        } catch (Exception $ex) {
            Log::error('driver order_Details: ' . $ex->getMessage(), ['trace' => $ex->getTraceAsString()]);
            return $this->returnError(__('api.Api_Error_Contact_Programmer'));
        }
    }

    /**
     * قبول الطلب من السائق: 3 → 4 (قيد التوصيل)
     */
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

/**
 * الطلبات المعروضة على السائق حالياً وبانتظار رده (لم تُقبل من أي سائق آخر بعد)
 */
public function pending_Offers(Request $request)
{
    try {
        $driver = $request->user();

        $offers = \App\Models\OrderDriverOffer::where('driver_id', $driver->id)
            ->where('status', 'pending')
            ->whereHas('order', fn($q) => $q->where('status_id', 3)->whereNull('driver_id'))
            ->with(['order' => function ($q) {
                $q->select('id', 'order_number', 'delivery_price', 'address', 'latitude', 'longitude', 'final_price', 'total_items', 'created_at')
                    ->with('user:id,name,mobile');
            }])
            ->latest()
            ->get();

        $data = $offers->map(fn($offer) => [
            'offer_id'       => $offer->id,
            'order_id'       => $offer->order->id,
            'order_number'   => $offer->order->order_number,
            'client_name'    => $offer->order->user->name ?? '',
            'address'        => $offer->order->address,
            'delivery_price' => $offer->order->delivery_price,
            'final_price'    => $offer->order->final_price,
            'total_items'    => $offer->order->total_items,
            'offered_at'     => $offer->offered_at?->diffForHumans(),
        ]);

        return $this->returnData('data', $data, __('api.Api_Return_Data'));
    } catch (Exception $ex) {
        Log::error('driver pending_Offers: ' . $ex->getMessage(), ['trace' => $ex->getTraceAsString()]);
        return $this->returnError(__('api.Api_Error_Contact_Programmer'));
    }
}

    /**
     * تأكيد التسليم: 4 → 5
     * العميل يعطي السائق كود التسليم (delivery_pin) لتأكيد الاستلام
     * السائق يُدخل الكود + طريقة الدفع (cash/online)
     */
    public function deliver_Order(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id'         => 'required|integer|exists:orders,id',
            'delivery_pin'     => 'required|string|size:4',
            'payment_method'   => 'required|in:cash,online',
            'driver_note'      => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return $this->returnError($validator->errors()->first());
        }

        try {
            $driver = $request->user();

            $order = Order::where('id', $request->order_id)
                ->where('driver_id', $driver->id)
                ->where('status_id', 4) // يجب أن يكون قيد التوصيل
                ->first();

            if (!$order) {
                return $this->returnError(__('api.ID_Not_Found'));
            }

            // التحقق من كود التسليم
            if ($order->delivery_pin !== $request->delivery_pin) {
                return $this->returnError(__('api.Invalid_delivery_pin'));
            }

            DB::beginTransaction();

            // تحديث حالة الطلب
            $order->update([
                'status_id'          => 5,
                'delivered_at'       => now(),
                'payment_collected'  => true,
                'driver_note'        => $request->driver_note,
                'payment_method'     => $request->payment_method,
            ]);

            // تسجيل في محفظة السائق إذا الدفع كاش
            if ($request->payment_method === 'cash') {
                $this->recordDriverEarning($driver->id, $order);
            }

            DB::commit();

            return $this->returnSuccessMessage(__('api.Order_delivered_successfully'));
        } catch (Exception $ex) {
            DB::rollback();
            Log::error('driver deliver_Order: ' . $ex->getMessage(), ['trace' => $ex->getTraceAsString()]);
            return $this->returnError(__('api.Api_Error_Contact_Programmer'));
        }
    }

    /**
     * سجل الطلبيات المنتهية للسائق (status=5)
     */
    public function order_History(Request $request)
    {
        try {
            $driver = $request->user();

            $data = Order::where('driver_id', $driver->id)
                ->where('status_id', 5)
                ->with([
                    'user:id,name,mobile,image',
                    'status:id',
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

    /**
     * الطلبيات قيد التوصيل (status=4) للسائق الحالي
     */
    public function in_Delivery_Orders(Request $request)
    {
        try {
            $driver = $request->user();

            $data = Order::where('driver_id', $driver->id)
                ->where('status_id', 4)
                ->with([
                    'user:id,name,mobile,image',
                    'status:id',
                ])
                ->orderBy('id', 'DESC')
                ->get()
                ->map(function ($order) {
                    // كود التسليم يُعرض في طلبيات قيد التوصيل
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
     * تنسيق بيانات الطلب للسائق
     */
    private function formatOrder(Order $order, bool $withPin = false): array
    {
        $result = [
            'id'              => $order->id,
            'status_id'       => $order->status_id,
            'status_name'     => $order->status?->name,
            'delivery_type'   => $order->delivery_type,
            'payment_method'  => $order->payment_method,
            'final_price'     => $order->final_price,
            'delivery_price'  => $order->delivery_price,
            'total_items'     => $order->total_items,
            'note'            => $order->note,
            'address'         => $order->address,
            'latitude'        => $order->latitude,
            'longitude'       => $order->longitude,
            'payment_collected' => $order->payment_collected,
            'created_at'      => $order->created_at?->format('Y-m-d H:i'),
            'out_for_delivery_at' => $order->out_for_delivery_at?->format('Y-m-d H:i'),
            'delivered_at'    => $order->delivered_at?->format('Y-m-d H:i'),
            'client' => $order->user ? [
                'id'        => $order->user->id,
                'name'      => $order->user->name,
                'mobile'    => $order->user->mobile,
                'image_url' => $order->user->image_url,
            ] : null,
            'items' => $order->relationLoaded('items')
                ? $order->items->map(fn($item) => [
                    'product_id'    => $item->product_id,
                    'product_name'  => $item->product?->name,
                    'product_image' => $item->product?->image_url ?? null,
                    'quantity'      => $item->quantity,
                    'unit_price'    => $item->unit_price,
                    'total_price'   => $item->total_price,
                ])
                : [],
        ];

        // كود التسليم يُعرض فقط عند الحاجة (حالة 4)
        if ($withPin) {
            $result['delivery_pin'] = $order->delivery_pin;
        }

        return $result;
    }

    /**
     * تسجيل كسب السائق في محفظته (كاش)
     */
    private function recordDriverEarning(int $driverId, Order $order): void
    {
        // إيجاد أو إنشاء محفظة السائق
        $wallet = Wallet::firstOrCreate(
            ['user_id' => $driverId],
            ['balance' => 0, 'rolename' => 'driver']
        );

        // إضافة رسوم التوصيل لمحفظة السائق
        $amount = $order->delivery_price ?? 0;

        $wallet->increment('balance', $amount);

        WalletTransaction::create([
            'wallet_id'   => $wallet->id,
            'order_id'    => $order->id,
            'type'        => 'credit',
            'amount'      => $amount,
            'description' => 'كسب توصيل كاش - طلب #' . $order->id,
        ]);
    }
}
