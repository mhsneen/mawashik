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
use App\Http\Resources\Api\V1\ProductCardResource;
use App\Http\Resources\Api\V1\ProductDetailsResource;

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
            // ✅ تعديل هنا: استخدام availableForClients بدلاً من Active
            $product = Product::availableForClients()
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
            // ✅ تعديل هنا: استخدام availableForClients بدلاً من Active
            $items = Product::availableForClients()
                ->FilterApi(request(['search_name']))
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
            // ✅ تعديل هنا: استخدام availableForClients بدلاً من Active
            $items = Product::HomeApi()->availableForClients()
                ->FilterApi(request(['search_name']))
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
            // ✅ تعديل هنا: استخدام availableForClients بدلاً من Active
            $items = Product::availableForClients()
                ->where('category_id', $request->category_id)
                ->with(['translations', 'category:id', 'unit:id'])
                ->select(
                    'id', 'image', 'netPrice', 'salePrice', 'salePrice_start', 'salePrice_end',
                    'category_id', 'calories', 'sku', 'unit_id'
                )
                ->withExists('variationAttributeValues as has_variants')
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
            Log::error('getProductsByCategory: ' . $ex->getMessage());
            return $this->returnError(__('api.Api_Error_Contact_Programmer'));
        }
    }

    /**
     * البحث عن المنتجات الأقرب جغرافياً للعميل.
     * (هذه الدالة كانت تستخدم availableForClients بالفعل ولم تتغير)
     */
    public function searchNearestProducts(Request $request)
    {
        try {
            $request->validate([
                'lat'              => 'required|numeric|between:-90,90',
                'lng'              => 'required|numeric|between:-180,180',
                'radius_km'        => 'nullable|numeric|min:1|max:500',
                'city_id'          => 'nullable|exists:cities,id',
                'area_id'          => 'nullable|exists:areas,id',
                'neighborhood_id'  => 'nullable|exists:neighborhoods,id',
                'category_id'      => 'nullable|exists:categories,id',
                'search_name'      => 'nullable|string|max:200',
                'sort_by'          => 'nullable|in:nearest,price_low,rating,newest',
            ]);

            $radius = $request->radius_km ?? 50;
            $sortBy = $request->sort_by ?? 'nearest';

            $query = Product::availableForClients()
                ->with([
                    'translations',
                    'category:id',
                    'trader:id,name,store_lat,store_lng,store_address,city_id,area_id,neighborhood_id',
                ])
                ->whereHas('trader', function ($q) use ($request) {
                    $q->whereNotNull('store_lat')
                      ->whereNotNull('store_lng')
                      ->when($request->filled('city_id'),         fn($q2) => $q2->where('city_id',         $request->city_id))
                      ->when($request->filled('area_id'),         fn($q2) => $q2->where('area_id',         $request->area_id))
                      ->when($request->filled('neighborhood_id'), fn($q2) => $q2->where('neighborhood_id', $request->neighborhood_id));
                })
                ->when($request->filled('search_name'), fn($q) =>
                    $q->whereHas('translations', fn($t) =>
                        $t->where('name', 'like', '%' . $request->search_name . '%'))
                )
                ->when($request->filled('category_id'), fn($q) =>
                    $q->where('category_id', $request->category_id)
                );

            $items = $query->paginate(API_PAGANATION_COUNT);

            $userLat = (float) $request->lat;
            $userLng = (float) $request->lng;
            $earthRadiusKm = 6371;

            $products = $items->getCollection()->map(function ($p) use ($userLat, $userLng, $earthRadiusKm, $radius) {
                $storeLat = $p->trader?->store_lat ? (float) $p->trader->store_lat : null;
                $storeLng = $p->trader?->store_lng ? (float) $p->trader->store_lng : null;

                if ($storeLat === null || $storeLng === null) {
                    return null;
                }

                $dLat = deg2rad($storeLat - $userLat);
                $dLng = deg2rad($storeLng - $userLng);

                $a = sin($dLat / 2) * sin($dLat / 2) +
                     cos(deg2rad($userLat)) * cos(deg2rad($storeLat)) *
                     sin($dLng / 2) * sin($dLng / 2);

                $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
                $distance = $earthRadiusKm * $c;

                if ($distance > $radius) {
                    return null;
                }

                return [
                    'id'                  => $p->id,
                    'name'                => $p->translate(app()->getLocale())?->name,
                    'image'               => $p->image_url,
                    'price'               => $p->final_price ?? $p->netPrice,
                    'category_name'       => $p->category?->translate(app()->getLocale())?->name,
                    'slaughter_available' => $p->slaughter_available,
                    'trader'              => [
                        'id'            => $p->trader?->id,
                        'name'          => $p->trader?->name,
                        'store_address' => $p->trader?->store_address,
                        'store_lat'     => $storeLat,
                        'store_lng'     => $storeLng,
                    ],
                    'distance_km'         => round($distance, 2),
                ];
            })->filter()->values();

            $products = match ($sortBy) {
                'nearest'    => $products->sortBy('distance_km')->values(),
                'price_low'  => $products->sortBy('price')->values(),
                'newest'     => $products->sortByDesc('id')->values(),
                'rating'     => $products,
                default      => $products,
            };

            return $this->returnData('data', [
                'items'    => $products->values(),
                'meta'     => [
                    'total_items'   => $products->count(),
                    'radius_km'     => $radius,
                    'sort_by'       => $sortBy,
                    'filters'       => $request->only(['city_id', 'area_id', 'neighborhood_id', 'category_id', 'search_name']),
                ],
            ], __('api.Api_Return_Data'));
        } catch (Exception $ex) {
            Log::error('searchNearestProducts: ' . $ex->getMessage(), [
                'input' => $request->all(),
            ]);
            return $this->returnError(__('api.Api_Error_Contact_Programmer'));
        }
    }
}
