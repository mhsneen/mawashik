<?php

namespace App\Http\Controllers\Api\V1\Client;

use App\Http\Controllers\Controller;
use App\Models\{OrderCancelReason};
use App\Traits\ApiTrait;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderCancelReasonsApiController extends Controller
{
    use ApiTrait;

    public function index()
    {
        try {
            $data = OrderCancelReason::Active()->select('id')->get();
            return $this->returnData('data', $data, __('api.Api_Return_Data'));
        } catch (Exception $ex) {
            DB::rollback();
            Log::error('OrderCancelReasonsApi index: ' . $ex->getMessage(), [
                'trace' => $ex->getTraceAsString(),

            ]);
            return $this->returnError(__('api.Api_Error_Contact_Programmer'));
        }
    }
}
