<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\DiscountCode;
use App\Traits\ApiTrait;
use Exception;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\Api\V1\DiscountCodeCheckRequest;
use Illuminate\Support\Facades\Log;

class DiscountCodeApiController extends Controller
{
    use ApiTrait;




    public function checkdiscountcode(DiscountCodeCheckRequest $request)
    {
        try {
            DB::beginTransaction();

            $code = $request->input('code');

            $discount_percentage = 0;
            $coupon = DiscountCode::where('code', $code)->first();

            if ($coupon && $coupon->isValid()) {
                $discount_percentage =  $coupon->discount_percentage;
            }

            $data = [
                'is_valid' => $discount_percentage > 0,
                'discount_percentage' => $discount_percentage,
                'coupon_code' =>  $coupon->code
            ];

            DB::commit();

            return $this->returnData('data', $data, __('api.Api_Return_Data'));
        } catch (Exception $ex) {
            DB::rollback();
            Log::error('checkdiscountcode: ' . $ex->getMessage(), [
                'trace' => $ex->getTraceAsString(),
                'input' => $request->all()
            ]);
            return $this->returnError(__('api.Api_Error_Contact_Programmer'));
        }
    }
}
