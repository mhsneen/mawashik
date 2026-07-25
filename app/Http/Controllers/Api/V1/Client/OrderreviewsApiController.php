<?php

namespace App\Http\Controllers\Api\V1\Client;

use App\Http\Controllers\Controller;
use App\Models\{OrderReview};
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
            DB::beginTransaction();
            $user = $request->user();

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
            $orderreview = OrderReview::create($item_data);
            $orderreview->save();
            DB::commit();
            return $this->returnSuccessMessage(__('api.Api_Save_success'));
        } catch (Exception $ex) {
            DB::rollback();
            Log::error('OrderreviewsApi index' . $ex->getMessage(), [
                'trace' => $ex->getTraceAsString(),
                'input' => $request->all()
            ]);
            return $this->returnError(__('api.Api_Error_Contact_Programmer'));
        }
    }
}
