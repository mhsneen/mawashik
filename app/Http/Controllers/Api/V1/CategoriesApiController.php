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
class CategoriesApiController extends Controller
{
    use ApiTrait;

    public function index()
    {
        try {

            $data = Category::active()
                ->parent()
                ->select('id', 'parent_id', 'image')
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



      public function show(Request $request)
    {
        try {
            // 1. جلب القسم مع سلاديراته وأقسامه الفرعية (بدون سلاديرات الأقسام الفرعية عشان ما نثقل)
            $category = Category::active()
                ->with([
                    'sliders:id,category_id,image',
                    'subCategories' => function ($query) {
                        $query->active()->select('id', 'parent_id', 'image');
                    }
                ])
                ->select('id', 'parent_id', 'image')
                ->findOrFail($request->category_id);

            // 2. تجميع معرفات القسم وأقسامه الفرعية لجلب المنتجات
            $categoryIds = $category->subCategories->pluck('id')
                ->push($category->id)
                ->toArray();

            // 3. جلب منتجات القسم وكل أقسامه الفرعية
            $products = Product::active()
                ->whereIn('category_id', $categoryIds)
                ->select(
                    'id', 'image',
                    'netPrice', 'salePrice', 'salePrice_start', 'salePrice_end',
                    'category_id', 'calories', 'sku'
                )
                ->with(['category:id'])
                ->orderByDesc('id')
                ->paginate(API_PAGANATION_COUNT)
                ->withQueryString();

            // 4. ترجيع البيانات منظمة
            $data = [
                'category'       => new CategoryCardResource($category),
                'sliders'        => CategorySliderResource::collection($category->sliders),
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
            Log::error('Category show: ' . $ex->getMessage(), [
                'trace' => $ex->getTraceAsString(),
                'input' => $request->all()
            ]);
            return $this->returnError(__('api.Api_Error_Contact_Programmer'));
        }
    }
}
