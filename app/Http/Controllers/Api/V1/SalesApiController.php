<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Traits\ApiTrait;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\Api\V1\CategoryCardResource;
use App\Http\Resources\Api\V1\CategorySliderResource;
use App\Http\Resources\Api\V1\ProductCardResource;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SalesApiController extends Controller
{
    use ApiTrait;

    // الأقسام الرئيسية + منتجات العروض (على مستوى المتجر كامل)
    public function index()
    {
        try {
            $categories = Category::active()
                ->parent()
                ->select('id', 'parent_id', 'image')
                ->get();

            $products = Product::active()
                ->onSale()
                ->select(
                    'id', 'image',
                    'netPrice', 'salePrice', 'salePrice_start', 'salePrice_end',
                    'category_id', 'calories', 'sku'
                )
                ->withExists('variationAttributeValues as has_variants')
                ->with(['category:id'])
                ->orderByDesc('id')
                ->paginate(API_PAGANATION_COUNT)
                ->withQueryString();

            $data = [
                'categories' => CategoryCardResource::collection($categories),
                'products'   => ProductCardResource::collection($products->getCollection()),
                'meta'       => [
                    'current_page' => $products->currentPage(),
                    'total_pages'  => $products->lastPage(),
                    'total_items'  => $products->total(),
                    'per_page'     => $products->perPage(),
                ],
            ];

            return $this->returnData('data', $data, __('api.Api_Return_Data'));
        } catch (Exception $ex) {
            Log::error('Sales index: ' . $ex->getMessage(), [
                'trace' => $ex->getTraceAsString(),
            ]);
            return $this->returnError(__('api.Api_Error_Contact_Programmer'));
        }
    }

    // عند الضغط على أي قسم (رئيسي أو فرعي): يرجع أقسامه الفرعية + منتجات العروض ضمن شجرته
    public function show(Request $request)
    {
        try {
            $category = Category::active()
                ->with([
                    'sliders:id,category_id,image',
                    'subCategories' => function ($query) {
                        $query->active()->select('id', 'parent_id', 'image');
                    }
                ])
                ->select('id', 'parent_id', 'image')
                ->findOrFail($request->category_id);

            // نجمع القسم الحالي + أقسامه الفرعية المباشرة، عشان نلحق منتجات العروض
            // المرتبطة بأي مستوى منهم (نفس منطق getProductsByCategory تماماً)
            $categoryIds = $category->subCategories->pluck('id')
                ->push($category->id)
                ->toArray();

            $products = Product::active()
                ->onSale()
                ->whereIn('category_id', $categoryIds)
                ->select(
                    'id', 'image',
                    'netPrice', 'salePrice', 'salePrice_start', 'salePrice_end',
                    'category_id', 'calories', 'sku'
                )
                ->withExists('variationAttributeValues as has_variants')
                ->with(['category:id'])
                ->orderByDesc('id')
                ->paginate(API_PAGANATION_COUNT)
                ->withQueryString();

            $data = [
                'category'       => new CategoryCardResource($category),
                'sub_categories' => CategoryCardResource::collection($category->subCategories),
                'products'       => ProductCardResource::collection($products->getCollection()),
                'meta'           => [
                    'current_page' => $products->currentPage(),
                    'total_pages'  => $products->lastPage(),
                    'total_items'  => $products->total(),
                    'per_page'     => $products->perPage(),
                ],
            ];

            return $this->returnData('data', $data, __('api.Api_Return_Data'));

        } catch (Exception $ex) {
            Log::error('Sales show: ' . $ex->getMessage(), [
                'trace' => $ex->getTraceAsString(),
                'input' => $request->all()
            ]);
            return $this->returnError(__('api.Api_Error_Contact_Programmer'));
        }
    }
}
