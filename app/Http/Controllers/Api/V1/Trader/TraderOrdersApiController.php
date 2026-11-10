<?php

namespace App\Http\Controllers\Api\V1\Trader;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Trader\TraderUpdateOrderStatusRequest;
use App\Models\{Order, OrderItem};
use App\Services\{FCMServices, AdminNotifier};
use App\Traits\ApiTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * TraderOrdersApiController — إدارة الطلبات من منظور التاجر.
 *
 * العمليات:
 *  - index: الطلبات الواردة للتاجر (مع فلترة بالحالة).
 *  - show: تفاصيل طلب.
 *  - confirm: تأكيد الطلب (1 → 2).
 *  - markReady: تم التجهيز (2 → 3).
 *  - reject: رفض/إلغاء الطلب (1 → 6).
 *
 * القيود:
 *  - التاجر لا يمكنه تعديل أو حذف طلب غير خاص به.
 *  - يحترم state machine في Order::canChangeStatusTo().
 */
class TraderOrdersApiController extends Controller
{
    use ApiTrait;

    public function index(Request $request)
    {
        try {
            $trader = $request->user();

            $query = Order::where('trader_id', $trader->id)
                ->with([
                    'user:id,name,mobile',
                    'status:id',
                    'items' => function ($q) {
                        $q->select('id', 'order_id', 'product_id', 'product_variant_id', 'quantity', 'unit_price', 'total_price',
                            'slaughter_selected', 'slaughter_price', 'slaughter_total')
                            ->with(['product:id,image', 'product.translations']);
                    },
                ]);

            if ($request->filled('status_id')) {
                $query->where('status_id', $request->status_id);
            } else {
                // افتراضياً: الطلبات النشطة (1,2,3) — لا المكتملة أو الملغية
                $query->whereIn('status_id', [1, 2, 3]);
            }

            $orders = $query->orderBy('id', 'desc')
                ->paginate(API_PAGANATION_COUNT)
                ->withQueryString();

            $data = [
                'items' => $orders->getCollection()->map(fn($o) => $this->formatOrder($o)),
                'meta' => [
                    'current_page' => $orders->currentPage(),
                    'total_pages'   => $orders->lastPage(),
                    'total_items'   => $orders->total(),
                ],
            ];

            return $this->returnData('data', $data, __('api.Api_Return_Data'));

        } catch (\Exception $ex) {
            Log::error('Trader orders index: ' . $ex->getMessage());
            return $this->returnError(__('api.Api_Error_Contact_Programmer'));
        }
    }

    public function show(Request $request, int $id)
    {
        try {
            $order = Order::where('trader_id', $request->user()->id)
                ->with([
                    'user:id,name,mobile,image', 'user.addresses',
                    'status:id',
                    'items.product.translations',
                    'items.attributes',
                ])
                ->find($id);

            if (!$order) {
                return $this->returnError(__('api.Order_Not_Found'));
            }

            return $this->returnData('data', $this->formatOrder($order, true), __('api.Api_Return_Data'));

        } catch (\Exception $ex) {
            Log::error('Trader order show: ' . $ex->getMessage());
            return $this->returnError(__('api.Api_Error_Contact_Programmer'));
        }
    }

    /**
     * تأكيد الطلب (status 1 → 2) وبدء التجهيز.
     */
    public function confirm(TraderUpdateOrderStatusRequest $request)
    {
        return $this->updateStatus($request, targetStatus: 2);
    }

    /**
     * تم التجهيز (status 2 → 3).
     */
    public function markReady(TraderUpdateOrderStatusRequest $request)
    {
        return $this->updateStatus($request, targetStatus: 3);
    }

    /**
     * رفض/إلغاء الطلب (status 1 → 6) من قبل التاجر.
     */
    public function reject(TraderUpdateOrderStatusRequest $request)
    {
        return $this->updateStatus($request, targetStatus: 6);
    }

    private function updateStatus(TraderUpdateOrderStatusRequest $request, int $targetStatus)
    {
        try {
            $trader = $request->user();
            $order = Order::where('trader_id', $trader->id)->find($request->order_id);

            if (!$order) {
                return $this->returnError(__('api.Order_Not_Found'));
            }

            if (!$order->canChangeStatusTo($targetStatus)) {
                return $this->returnError(__('api.Order_Status_Transition_Not_Allowed'));
            }

            DB::transaction(function () use ($order, $targetStatus, $request) {
                $order->status_id = $targetStatus;
                if ($targetStatus == 6) {
                    $order->cancel_reason_id = null;
                    $order->cancel_other_reason = $request->reason;
                }
                $order->save();
            });

            return $this->returnData('data', [
                'order_id'  => $order->id,
                'status_id' => $order->status_id,
            ], __('api.Api_Update_Success'));

        } catch (\Exception $ex) {
            Log::error('Trader order update status: ' . $ex->getMessage());
            return $this->returnError(__('api.Api_Error_Contact_Programmer'));
        }
    }

    private function formatOrder(Order $o, bool $detailed = false): array
    {
        $data = [
            'id'             => $o->id,
            'order_number'   => $o->order_number,
            'status_id'      => $o->status_id,
            'status_name'    => $o->status?->translate(app()->getLocale())?->name,
            'client_name'    => $o->user?->name,
            'client_mobile'  => $o->user?->mobile,
            'pickup_type'    => $o->pickup_type,
            'delivery_type'  => $o->delivery_type,
            'total_price'    => $o->total_price,
            'discount_amount' => $o->discount_amount,
            'delivery_price' => $o->delivery_price,
            'added_tax'      => $o->added_tax,
            'final_price'    => $o->final_price,
            'deposit_amount'  => $o->deposit_amount,
            'remaining_amount' => $o->remaining_amount,
            'slaughter_total' => $o->slaughter_total,
            'commission_trader' => $o->commission_trader,
            'net_amount'     => $o->trader_net_amount,
            'payment_method' => $o->payment_method,
            'payment_status' => $o->payment_status,
            'created_at'     => $o->created_at?->format('Y-m-d H:i'),
            'items_count'    => $o->items->count(),
        ];

        if ($detailed) {
            $data['items'] = $o->items->map(fn($item) => [
                'id'                => $item->id,
                'product_id'        => $item->product_id,
                'product_name'      => $item->product?->translate(app()->getLocale())?->name,
                'product_image'     => $item->product?->image_url,
                'quantity'          => $item->quantity,
                'unit_price'        => $item->unit_price,
                'total_price'       => $item->total_price,
                'slaughter_selected' => $item->slaughter_selected,
                'slaughter_price'   => $item->slaughter_price,
                'slaughter_total'   => $item->slaughter_total,
            ]);
            $data['client_address'] = $o->address;
            $data['client_lat']     = $o->latitude;
            $data['client_lng']     = $o->longitude;
            $data['note']           = $o->note;
        }

        return $data;
    }
}
