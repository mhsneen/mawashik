<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\{Category, Product};
use App\Traits\ApiTrait;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\Api\V1\{ProductsByCategoryRequest, ProductDetailsRequest};
use App\Http\Resources\ProductResource;
use App\Http\Resources\Api\V1\ProductCardResource; // أضف هذا في أعلى الملف
use App\Http\Resources\Api\V1\ProductDetailsResource; // بدل ProductResource

class HomeApiController extends Controller
{
    use ApiTrait;

    /**
     * تفاصيل منتج واحد كاملة: السعر، الفيرنتس (لو فيه)،
     * ومجموعات الإضافات (Modifiers) — تُستخدم لبناء شاشة اختيار
     * المنتج قبل الإضافة للسلة.
     */
 public function getProductDetails(ProductDetailsRequest $request)
{
    try {
        $product = Product::Active()
            ->with([
                'category:id,parent_id,image',
                'brand:id,image',
                'productimages',
                'variants' => fn($q) => $q->where('is_active', true)->with('attributeValues'),
                'attributeValues.attribute',
                'unit'
            ])
            ->find($request->product_id);

        if (!$product) {
            return $this->returnError(__('dashboard.ID_Not_Found'));
        }

        // فقط تغيير هنا 👇
        return $this->returnData('data', new ProductDetailsResource($product), __('api.Api_Return_Data'));

    } catch (Exception $ex) {
        Log::error('getProductDetails: ' . $ex->getMessage(), [
            'trace' => $ex->getTraceAsString(),
        ]);
        return $this->returnError(__('api.Api_Error_Contact_Programmer'));
    }
}




 public function get_search_products(Request $request)
{
    try {
       $items = Product::Active()
            ->FilterApi(request(['search_name']))
            // 1. أضف الحقول الجديدة هنا في الـ select
            ->select(
                'id', 'image',
                'netPrice', 'salePrice', 'salePrice_start', 'salePrice_end',
                'category_id', 'calories', 'sku'
            )
          ->withExists('variationAttributeValues as has_variants')
            ->with(['category:id'])
               ->latest()
            ->paginate(API_PAGANATION_COUNT)
            ->withQueryString();
        $data = [
            // نمرر الكولكشن فقط للـ Resource عشان ما يتكررش الـ data
            'items' => ProductCardResource::collection($items->getCollection()),
            'meta'  => [
                'current_page' => $items->currentPage(),
                'total_pages'  => $items->lastPage(),
                'total_items'  => $items->total(),
                'per_page'     => $items->perPage(),
            ],
            'links' => [
                'self' => $items->url($items->currentPage()),
                'next' => $items->nextPageUrl(),
                'prev' => $items->previousPageUrl(),
            ]
        ];

        return $this->returnData('data', $data, __('api.Api_Return_Data'));

    } catch (Exception $ex) {
        Log::error('get_search_products: ' . $ex->getMessage(), [
            'trace' => $ex->getTraceAsString(),
            'input' => $request->all()
        ]);
        return $this->returnError(__('api.Api_Error_Contact_Programmer'));
    }
}



    public function getHomeProduct()
    {
        try {


                $items = Product::HomeApi()->Active()
            ->FilterApi(request(['search_name']))
            // 1. أضف الحقول الجديدة هنا في الـ select
            ->select(
                'id', 'image',
                'netPrice', 'salePrice', 'salePrice_start', 'salePrice_end',
                'category_id', 'calories', 'sku'
            )
            ->withExists('variationAttributeValues as has_variants')
            ->with(['category:id'])
              ->latest()
            ->paginate(API_PAGANATION_COUNT)
            ->withQueryString();



            $data = [
            'items' => ProductCardResource::collection($items->getCollection()),
                'meta' => [
                    'current_page' => $items->currentPage(),
                    'total_pages' => $items->lastPage(),
                    'total_items' => $items->total(),
                    'per_page' => $items->perPage(),
                ],
                'links' => [
                    'self' => $items->url($items->currentPage()),
                    'next' => $items->nextPageUrl(),
                    'prev' => $items->previousPageUrl(),
                ]
            ];

            return $this->returnData('data', $data, __('api.Api_Return_Data'));
        } catch (Exception $ex) {
            DB::rollback();
            Log::error('getHomeProduct: ' . $ex->getMessage(), [
                'trace' => $ex->getTraceAsString(),
            ]);
            return $this->returnError(__('api.Api_Error_Contact_Programmer'));
        }
    }

    public function getProductsByCategory(ProductsByCategoryRequest $request)
    {
        try {
            $category = Category::active()
                ->with('subCategories:id,parent_id')
                ->findOrFail($request->category_id);

            $categoryIds = [];

            if (is_null($category->parent_id)) {
                // أب
                $categoryIds = $category->subCategories->pluck('id')->toArray();
                $categoryIds[] = $category->id;
            } else {
                // ابن
                $categoryIds[] = $category->id;
            }

            $items = Product::Active()
                ->FilterApi(request(['search_name']))
                ->whereIn('category_id', $categoryIds)
                ->select(
                    'id',
                    'image',
                    'netPrice',
                    'salePrice',
                    'salePrice_start',
                    'salePrice_end',
                    'category_id',
                    'brand_id',
                    'sku',
                    'quantity',
                    'calories'
                )
                ->with([
                    'category:id,parent_id,image',
                    'brand:id,image',
                    'productimages',
                    'attributeValues.attribute'
                ])
                ->paginate(API_PAGANATION_COUNT)
                ->withQueryString();

            $items->getCollection()->transform(function ($product) {

                $attributes = $product->attributeValues
                    ->groupBy('attribute_id')
                    ->map(function ($values, $attribute_id) {
                        return [
                            'attribute_id' => $attribute_id,
                            'attribute_name' => $values->first()->attribute->name,
                            'values' => $values->map(function ($value) {
                                return [
                                    'id' => $value->id,
                                    'value' => $value->value,
                                    'color_code' => $value->color_code,
                                ];
                            })->values(),
                        ];
                    })->values();

                $product->attributes = $attributes;
                unset($product->attributeValues);

                return $product;
            });

            return $this->returnData('data', [
                'items' => $items->items(),
                'meta' => [
                    'current_page' => $items->currentPage(),
                    'total_pages' => $items->lastPage(),
                    'total_items' => $items->total(),
                    'per_page' => $items->perPage(),
                ],
            ], __('api.Api_Return_Data'));
        } catch (\Exception $e) {
            Log::error('getProductsByCategory: ' . $e->getMessage());
            return $this->returnError(__('api.Api_Error_Contact_Programmer'));
        }
    }
}
