<?php

namespace App\Http\Controllers\Api\V1\Client;

use App\Http\Controllers\Controller;
use App\Models\{Order, OrderReview}; // ✅ إضافة موديل Order
use App\Traits\ApiTrait;
use App\Http\Requests\Api\V1\{OrdersReviewsRequest};
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderreviewsApiController extends Controller
{
    use ApiTrait;

    public function store(OrdersReviewsRequest $request)
    {
        try {
            $user = $request->user();

            // ✅ سد ثغرة IDOR: التأكد أن الطلب موجود ويخص العميل الحالي فقط
            $order = Order::where('id', $request->order_id)
                ->where('user_id', $user->id)
                ->first();

            if (!$order) {
                return $this->returnError(__('api.Order_Not_Found'));
            }

            // فحص إذا المستخدم قيم مسبقاً
            $existingReview = OrderReview::where('order_id', $request->order_id)
                ->where('client_id', $user->id)
                ->first();

            if ($existingReview) {
                return $this->returnError(__('api.You_Already_Reviewed_This_Order'));
            }

            $item_data = [
                'order_id' => $request->order_id,
                'rating' => $request->rating,
                'client_id' => $user->id,
                'comment' => $request->comment,
            ];

            // ✅ create تقوم بالحفظ مباشرة، لا حاجة لاستدعاء save() مرة أخرى
            OrderReview::create($item_data);

            return $this->returnSuccessMessage(__('api.Api_Save_success'));
        } catch (Exception $ex) {
            Log::error('OrderreviewsApi store: ' . $ex->getMessage(), [
                'trace' => $ex->getTraceAsString(),
                'input' => $request->all()
            ]);
            return $this->returnError(__('api.Api_Error_Contact_Programmer'));
        }
    }
}
