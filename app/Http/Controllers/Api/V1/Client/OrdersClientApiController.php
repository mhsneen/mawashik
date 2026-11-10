<?php

namespace App\Http\Controllers\Api\V1\Client;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Traits\ApiTrait;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Requests\Api\V1\ClientCancellOrderRequest;
use Illuminate\Support\Facades\Log;
use App\Http\Resources\Api\V1\Client\OrderResource;

class OrdersClientApiController extends Controller
{
    use ApiTrait;

    protected function baseOrdersQuery(Request $request)
    {
        return Order::where('user_id', $request->user()->id)
            ->with([
                'status:id',
                'cancelReason:id',
                'coupon:id,code,discount_percentage',
                'driver:id,name,mobile,image',
                 'review', // ✅ أضف هذا السطر هنا
                'items' => function ($q) {
                    $q->select('id', 'order_id', 'product_id', 'product_variant_id', 'variant_label_snapshot', 'quantity', 'unit_price', 'total_price')
                        ->with([
                            'product:id,image,category_id,unit_id',
                            'product.category:id',
                            'product.unit',
                            'attributes:id,order_item_id,attribute_id,attribute_value_id,attribute_name,value_name,color_code,price',
                        ]);
                },
            ])
            ->orderBy('id', 'DESC');
    }

    protected function listOrders(Request $request, callable $scope)
    {
        try {
            $query = $this->baseOrdersQuery($request);
            $scope($query);

            $orders = $query->paginate(API_PAGANATION_COUNT)->withQueryString();

            $data = [
                'items' => OrderResource::collection($orders->getCollection()),
                'meta' => [
                    'current_page' => $orders->currentPage(),
                    'total_pages'  => $orders->lastPage(),
                    'total_items'  => $orders->total(),
                    'per_page'     => $orders->perPage(),
                ],
            ];

            return $this->returnData('data', $data, __('api.Api_Return_Data'));
        } catch (Exception $ex) {
            Log::error('listOrders: ' . $ex->getMessage(), ['trace' => $ex->getTraceAsString()]);
            return $this->returnError(__('api.Api_Error_Contact_Programmer'));
        }
    }

    // 1,2,3,4 - الطلبات النشطة حالياً
    public function client_Current_Orders(Request $request)
    {
        return $this->listOrders($request, fn ($q) => $q->StatusCurrent());
    }

    // 5 - المكتملة
    public function client_End_Orders(Request $request)
    {
        return $this->listOrders($request, fn ($q) => $q->StatusEnd());
    }

    // 6 - الملغية
    public function client_Cancelled_Orders(Request $request)
    {
        return $this->listOrders($request, fn ($q) => $q->StatusCancelled());
    }

    // كل الطلبات (تدعم فلتر status_id اختياري)
    public function client_All_Orders(Request $request)
    {
        return $this->listOrders($request, function ($q) use ($request) {
            if ($request->filled('status_id')) {
                $q->where('status_id', $request->status_id);
            }
        });
    }

    // تفاصيل طلب واحد كاملة
    public function order_Details(Request $request)
    {
        $request->validate(['order_id' => 'required|integer|exists:orders,id']);

        $order = $this->baseOrdersQuery($request)
            ->with(['review', 'address'])
            ->where('id', $request->order_id)
            ->first();

        if (!$order) {
            return $this->returnError(__('api.Order_Not_Found'));
        }

        if ($order->read == 0) {
            DB::transaction(fn () => $order->update(['read' => 1]));
        }

        return $this->returnData('data', new OrderResource($order), __('api.Api_Return_Data'));
    }

    // إلغاء الطلب (نفس منطقك الحالي: يرجّع المخزون والإضافات، ويستخدم canChangeStatusTo)
    public function client_Cancell_Order(ClientCancellOrderRequest $request)
    {
        try {
            $user = $request->user();

            $order = Order::where('id', $request->order_id)
                ->with('items.product', 'items.attributes')
                ->where('user_id', $user->id)
                ->first();

            if (!$order) {
                return $this->returnError(__('api.ID_Not_Found'));
            }

            if (!$order->canChangeStatusTo(6)) {
                return $this->returnError(__('dashboard.The order_status_cannot_be_changed_to_this_state'));
            }

            DB::beginTransaction();
            $order->update([
                'status_id' => 6,
                'cancel_reason_id' => $request->cancel_reason_id,
                'cancel_other_reason' => $request->cancel_other_reason,
            ]);

            foreach ($order->items as $item) {
                $product = $item->product;
                if ($product && $product->track_quantity) {
                    $variant = $item->product_variant_id ? $product->variants()->find($item->product_variant_id) : null;

                    if ($variant && $variant->track_quantity && !is_null($variant->quantity)) {
                        $variant->increment('quantity', (float) $item->quantity);
                    } elseif (!is_null($product->quantity)) {
                        $product->increment('quantity', (float) $item->quantity);
                    }
                }

                foreach ($item->attributes as $attr) {
                    $pivot = DB::table('attribute_value_product')
                        ->where('product_id', $product->id)
                        ->where('attribute_value_id', $attr->attribute_value_id)
                        ->first();
                    if ($pivot && !is_null($pivot->quantity)) {
                        DB::table('attribute_value_product')
                            ->where('product_id', $product->id)
                            ->where('attribute_value_id', $attr->attribute_value_id)
                            ->increment('quantity', (float) $item->quantity);
                    }
                }
            }

            DB::commit();
            return $this->returnSuccessMessage(__('api.The order was cancelled'));
        } catch (Exception $ex) {
            DB::rollback();
            Log::error('client_Cancell_Order: ' . $ex->getMessage(), [
                'trace' => $ex->getTraceAsString(),
                'input' => $request->all()
            ]);
            return $this->returnError(__('api.Api_Error_Contact_Programmer'));
        }
    }

    // تأكيد الاستلام - فقط لطلبات "استلام من المتجر" (pickup) بعد أن تصبح جاهزة (status 3)
    // ملاحظة: طلبات "توصيل" لا يؤكدها العميل، بل السائق عبر delivery_pin (deliver_Order)
    public function client_Confirm_Receipt(Request $request)
    {
        $request->validate(['order_id' => 'required|integer|exists:orders,id']);

        try {
            $order = Order::where('id', $request->order_id)
                ->where('user_id', $request->user()->id)
                ->first();

            if (!$order) {
                return $this->returnError(__('api.Order_Not_Found'));
            }

            if ($order->delivery_type !== 'pickup' || !$order->canChangeStatusTo(5)) {
                return $this->returnError(__('dashboard.The order status cannot be changed to this state'));
            }

            DB::beginTransaction();
            $order->update([
                'status_id' => 5,
                'delivered_at' => now(),
            ]);
            DB::commit();

            return $this->returnSuccessMessage(__('dashboard.Status_modified_successfully'));
        } catch (Exception $ex) {
            DB::rollback();
            Log::error('client_Confirm_Receipt: ' . $ex->getMessage(), ['trace' => $ex->getTraceAsString()]);
            return $this->returnError(__('api.Api_Error_Contact_Programmer'));
        }
    }
}
