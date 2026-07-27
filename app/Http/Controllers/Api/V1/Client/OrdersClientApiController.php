<?php

namespace App\Http\Controllers\Api\V1\Client;

use App\Http\Controllers\Controller;
use App\Models\{Order};
use App\Traits\ApiTrait;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Requests\Api\V1\{ClientCancellOrderRequest};
use Illuminate\Support\Facades\Log;


class OrdersClientApiController extends Controller
{
    use ApiTrait;


    public function client_Current_Orders(Request $request)
    {
        try {
            DB::beginTransaction();
            $user = $request->user();
            $data = Order::StatusCurrent()->where('user_id', $user->id)
                ->with([
                    'user:id,name,mobile,image', // بيانات العميل
                    'status:id', // حالة الطلب
                    'cancelReason:id',
                    'coupon:id,code,discount_percentage', // كوبون الخصم
                    'items' => function ($query) {
                        $query->select('id', 'order_id', 'product_id', 'quantity', 'unit_price', 'total_price')
                            ->with([
                                'product:id,image,category_id', // معلومات المنتج
                                'product.category:id,image',   // ← هنا
                                'attributes' => function ($query) {
                                    $query->select(
                                        'order_item_id',
                                        'attribute_id',
                                        'attribute_value_id',
                                        'attribute_name',
                                        'value_name',
                                        'color_code',
                                    );
                                }
                            ]);
                    }
                ])
                ->orderBy('id', 'DESC')->get();
            $data->setHidden(['created_at', 'updated_at', 'deleted_at']);

            $data->transform(function ($item) {
                $item->created_at_human = $item->created_at
                    ? \Carbon\Carbon::parse($item->created_at)->format('Y-m-d')
                    : null;


                $item->delivery_date_formated = $item->delivery_date
                    ? \Carbon\Carbon::parse($item->delivery_date)->format('Y-m-d')
                    : null;
                return $item;
            });
            DB::commit();
            return $this->returnData('data', $data, __('api.Api_Return_Data'));
        } catch (Exception $ex) {
            DB::rollback();
            Log::error('client_Current_Orders' . $ex->getMessage(), [
                'trace' => $ex->getTraceAsString(),
                'input' => $request->all()
            ]);
            return $this->returnError(__('api.Api_Error_Contact_Programmer'));
        }
    }

    public function client_End_Orders(Request $request)
    {
        try {
            DB::beginTransaction();
            $user = $request->user();
            $data = Order::StatusEnd()->where('user_id', $user->id)
                ->with([
                    'user:id,name,mobile,image', // بيانات العميل
                    'status:id', // حالة الطلب
                    'cancelReason:id',
                    'coupon:id,code,discount_percentage', // كوبون الخصم
                    'items' => function ($query) {
                        $query->select('id', 'order_id', 'product_id', 'quantity', 'unit_price', 'total_price')
                            ->with([
                                'product:id,image,category_id', // معلومات المنتج
                                'product.category:id,image',   // ← هنا
                                'attributes' => function ($query) {
                                    $query->select(
                                        'order_item_id',
                                        'attribute_id',
                                        'attribute_value_id',
                                        'attribute_name',
                                        'value_name',
                                        'color_code',
                                    );
                                }
                            ]);
                    }
                ])
                ->orderBy('id', 'DESC')->get();
            $data->setHidden(['created_at', 'updated_at', 'deleted_at']);

            $data->transform(function ($item) {
                $item->created_at_human = $item->created_at
                    ? \Carbon\Carbon::parse($item->created_at)->format('Y-m-d')
                    : null;


                $item->delivery_date_formated = $item->delivery_date
                    ? \Carbon\Carbon::parse($item->delivery_date)->format('Y-m-d')
                    : null;
                return $item;
            });
            DB::commit();
            return $this->returnData('data', $data, __('api.Api_Return_Data'));
        } catch (Exception $ex) {
            DB::rollback();
            Log::error('client_End_Orders' . $ex->getMessage(), [
                'trace' => $ex->getTraceAsString(),
                'input' => $request->all()
            ]);
            return $this->returnError(__('api.Api_Error_Contact_Programmer'));
        }
    }


    public function client_Cancelled_Orders(Request $request)
    {
        try {
            DB::beginTransaction();
            $user = $request->user();
            $data = Order::StatusCancelled()->where('user_id', $user->id)
                ->with([
                    'user:id,name,mobile,image', // بيانات العميل
                    'status:id', // حالة الطلب
                    'cancelReason:id',
                    'coupon:id,code,discount_percentage', // كوبون الخصم
                    'items' => function ($query) {
                        $query->select('id', 'order_id', 'product_id', 'quantity', 'unit_price', 'total_price')
                            ->with([
                                'product:id,image,category_id', // معلومات المنتج
                                'product.category:id,image',   // ← هنا
                                'attributes' => function ($query) {
                                    $query->select(
                                        'order_item_id',
                                        'attribute_id',
                                        'attribute_value_id',
                                        'attribute_name',
                                        'value_name',
                                        'color_code',
                                    );
                                }
                            ]);
                    }
                ])
                ->orderBy('id', 'DESC')->get();
            $data->setHidden(['created_at', 'updated_at', 'deleted_at']);

            $data->transform(function ($item) {
                $item->created_at_human = $item->created_at
                    ? \Carbon\Carbon::parse($item->created_at)->format('Y-m-d')
                    : null;


                $item->delivery_date_formated = $item->delivery_date
                    ? \Carbon\Carbon::parse($item->delivery_date)->format('Y-m-d')
                    : null;
                return $item;
            });
            DB::commit();
            return $this->returnData('data', $data, __('api.Api_Return_Data'));
        } catch (Exception $ex) {
            DB::rollback();
            Log::error('client_Cancelled_Orders' . $ex->getMessage(), [
                'trace' => $ex->getTraceAsString(),
                'input' => $request->all()
            ]);
            return $this->returnError(__('api.Api_Error_Contact_Programmer'));
        }
    }



       public function client_Cancell_Order(ClientCancellOrderRequest $request)
    {
        try {
            $user = $request->user();

            // جلب الطلب مع التأكد من أنه يخص نفس العميل
            $order = Order::where('id', $request->order_id)
            ->with('items.product', 'items.attributes')
                ->where('user_id', $user->id)
                ->first();

            // التحقق من وجود الطلب
            if (!$order) {
                return $this->returnError(__('api.ID_Not_Found'));
            }

            // التحقق من إمكانية تغيير الحالة إلى "ملغي" (6)
            if (!$order->canChangeStatusTo(6)) {
                return $this->returnError(__('dashboard.The order_status_cannot_be_changed_to_this_state'));
            }

            DB::beginTransaction();
            $order->update([
                'status_id' => 6, // 6 = ملغي
                'cancel_reason_id' => $request->cancel_reason_id,
                'cancel_other_reason' => $request->cancel_other_reason,
            ]);


             // ✅ إرجاع الكميات إلى المخزون
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

        // ✅ إرجاع مخزون الإضافات (Modifiers) إن وجدت
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
}
