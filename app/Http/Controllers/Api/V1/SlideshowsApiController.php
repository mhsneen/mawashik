<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Slideshow;
use App\Traits\ApiTrait;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SlideshowsApiController extends Controller
{
    use ApiTrait;

    public function get_Slideshows()
    {
        try {
            $data = Slideshow::Active()->select('id', 'image')->latest()->get();
            return $this->returnData('data', $data, __('api.Api_Return_Data'));
        } catch (Exception $ex) {
            DB::rollback();
            Log::error('get_Slideshows: ' . $ex->getMessage(), [
                'trace' => $ex->getTraceAsString(),

            ]);
            return $this->returnError(__('api.Api_Error_Contact_Programmer'));
        }
    }
}
