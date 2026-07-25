<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Traits\ApiTrait;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CategoriesApiController extends Controller
{
    use ApiTrait;

    public function index()
    {
        try {

            $data = Category::active()
                ->parent()
                ->select('id', 'parent_id', 'image')
                ->with([
                    'sliders:id,category_id,image',
                    'subCategoriesapi:id,parent_id,image'
                ])
                ->get();


            return $this->returnData('data', $data, __('api.Api_Return_Data'));
        } catch (Exception $ex) {
            DB::rollback();
            Log::error('Categories index: ' . $ex->getMessage(), [
                'trace' => $ex->getTraceAsString(),

            ]);
            return $this->returnError(__('api.Api_Error_Contact_Programmer'));
        }
    }
}
