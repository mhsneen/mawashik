<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Product;
use App\Traits\ApiTrait;
use App\Http\Resources\Api\V1\BrandCardResource;
use App\Http\Resources\Api\V1\ProductCardResource;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BrandsApiController extends Controller
{
    use ApiTrait;

    // 1) قائمة جميع العلامات التجارية
    public function index()
    {
        try {
            $brands = Brand::active()
                ->select('id', 'image')
                ->get();

            return $this->returnData('data', BrandCardResource::collection($brands), __('api.Api_Return_Data'));

        } catch (Exception $ex) {
            Log::error('Brands index: ' . $ex->getMessage(), [
                'trace' => $ex->getTraceAsString(),
            ]);
            return $this->returnError(__('api.Api_Error_Contact_Programmer'));
        }
    }

    // 2) تفاصيل العلامة التجارية + منتجاتها
    public function show(Request $request)
    {
        try {
            $brand = Brand::active()
                ->select('id', 'image')
                ->findOrFail($request->brand_id);

            $products = Product::active()
                ->where('brand_id', $brand->id)
                ->select(
                    'id', 'image',
                    'netPrice', 'salePrice', 'salePrice_start', 'salePrice_end',
                    'category_id', 'calories', 'sku'
                )
                ->with(['category:id'])
                ->orderByDesc('id')
                ->paginate(API_PAGANATION_COUNT)
                ->withQueryString();

            $data = [
                'brand'    => new BrandCardResource($brand),
                'products' => ProductCardResource::collection($products->getCollection()),
                'meta'     => [
                    'current_page' => $products->currentPage(),
                    'total_pages'  => $products->lastPage(),
                    'total_items'  => $products->total(),
                    'per_page'     => $products->perPage(),
                ],
            ];

            return $this->returnData('data', $data, __('api.Api_Return_Data'));

        } catch (Exception $ex) {
            Log::error('Brand show: ' . $ex->getMessage(), [
                'trace' => $ex->getTraceAsString(),
                'input' => $request->all()
            ]);
            return $this->returnError(__('api.Api_Error_Contact_Programmer'));
        }
    }
}
