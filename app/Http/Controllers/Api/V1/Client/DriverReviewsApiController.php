<?php

namespace App\Http\Controllers\Api\V1\Client;

use App\Http\Controllers\Controller;
use App\Models\{DriverReview, Order};
use App\Traits\ApiTrait;
use App\Http\Requests\Api\V1\{DriverReviewsRequest};
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DriverReviewsApiController extends Controller
{
    use ApiTrait;

    public function store(DriverReviewsRequest $request)
    {
        try {
            $user = $request->user();

            $order = Order::where('id', $request->order_id)
                ->where('user_id', $user->id)
                ->first();

            if (!$order) {
                return $this->returnError(__('dashboard.ID_Not_Found'));
            }

            // ما يقدر يقيّم السائق قبل ما يتسلّم الطلب فعلياً
            if ($order->status_id != 5) {
                return $this->returnError(__('api.Order_Not_Delivered_Yet'));
            }

            // لازم يكون في سائق معيّن أصلاً للطلب
            if (!$order->driver_id) {
                return $this->returnError(__('api.No_Driver_Assigned_To_This_Order'));
            }

            // فحص إذا العميل قيّم سائق هذا الطلب مسبقاً
            $existingReview = DriverReview::where('order_id', $order->id)
                ->where('client_id', $user->id)
                ->first();

            if ($existingReview) {
                return $this->returnError(__('api.You_Already_Reviewed_This_Driver'));
            }

            DB::beginTransaction();
            $driverReview = DriverReview::create([
                'order_id'  => $order->id,
                'driver_id' => $order->driver_id, // ناخذ السائق من الطلب نفسه، مش من العميل
                'client_id' => $user->id,
                'rating'    => $request->rating,
                'comment'   => $request->comment,
            ]);
            DB::commit();

            return $this->returnSuccessMessage(__('api.Api_Save_success'));
        } catch (Exception $ex) {
            DB::rollback();
            Log::error('DriverReviewsApi store: ' . $ex->getMessage(), [
                'trace' => $ex->getTraceAsString(),
                'input' => $request->all(),
            ]);
            return $this->returnError(__('api.Api_Error_Contact_Programmer'));
        }
    }
}
