<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Onboarding;
use App\Traits\ApiTrait;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OnboardingsApiController extends Controller
{
    use ApiTrait;

    public function get_Onboardings()
    {
        try {
            $data = Onboarding::select('id', 'image')->get();
            return $this->returnData('data', $data, __('api.Api_Return_Data'));
        } catch (Exception $ex) {
            DB::rollback();
            Log::error('get_Onboardings: ' . $ex->getMessage(), [
                'trace' => $ex->getTraceAsString(),

            ]);
            return $this->returnError(__('api.Api_Error_Contact_Programmer'));
        }
    }
}
