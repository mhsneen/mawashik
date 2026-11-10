<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Textpage;
use App\Models\Socialmedialink;
use App\Traits\ApiTrait;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TextpagesApiController extends Controller
{
    use ApiTrait;

    public function get_AboutApp()
    {
        $slug = "about-app";
        try {
            DB::beginTransaction();
            $textpage = Textpage::WhereTranslation('slug', $slug)->select('id')->first();
            $textpage['details'] = str_replace('"', "'", $textpage['details']);

            $socielLinks = Socialmedialink::select('id', 'icon', 'link')->get();


            $data = [
                'textpage' => $textpage,
                'socielLinks' => $socielLinks,
            ];
            DB::commit();
            return $this->returnData('data', $data, __('api.Api_Return_Data'));
        } catch (Exception $ex) {
            DB::rollback();
            Log::error('get_AboutApp: ' . $ex->getMessage(), [
                'trace' => $ex->getTraceAsString(),

            ]);
            return $this->returnError(__('api.Api_Error_Contact_Programmer'));
        }
    }



    public function get_TermsConditions()
    {
        $slug = "terms-and-conditions";
        try {
            DB::beginTransaction();
            $textpage = Textpage::WhereTranslation('slug', $slug)->select('id')->first();
            $textpage['details'] = str_replace('"', "'", $textpage['details']);

            $data = [
                'textpage' => $textpage
            ];
            DB::commit();
            return $this->returnData('data', $data, __('api.Api_Return_Data'));
        } catch (Exception $ex) {
            DB::rollback();
            Log::error('get_TermsConditions: ' . $ex->getMessage(), [
                'trace' => $ex->getTraceAsString(),

            ]);
            return $this->returnError(__('api.Api_Error_Contact_Programmer'));
        }
    }
}
