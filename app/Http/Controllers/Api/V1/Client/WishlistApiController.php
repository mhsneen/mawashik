<?php

namespace App\Http\Controllers\Api\V1\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Requests\Api\V1\WishListRequest;
use App\Traits\ApiTrait;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WishlistApiController extends Controller
{
    use ApiTrait;
    public function index(Request $request)
    {
        try {
            $user = $request->user();

            $items = $user->favoriteProducts()
                ->Active()

                ->select(
                    'products.id',
                    'products.image',
                    'products.netPrice',
                    'products.salePrice',
                    'products.salePrice_start',
                    'products.salePrice_end',
                    'products.category_id',
                    'products.brand_id',
                    'products.sku',
                    'products.quantity',
                    'products.calories'
                )
                ->with([
                    'category:id,parent_id,image',
                    'brand:id,image',
                    'productimages',
                    'attributeValues.attribute'
                ])
                ->latest('wish_lists.created_at')
                ->paginate(API_PAGANATION_COUNT);

            $items->getCollection()->transform(function ($product) {

                // تنظيم الخصائص
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

                // نفس Home
                $product->is_favorited = true;
                $product->makeHidden([
                    'pivot',
                    'is_active',
                    'new_arrivals',
                    'deleted_at',
                    'created_at',
                    'updated_at',
                ]);

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
        } catch (\Exception $ex) {
            Log::error('WishlistApi index: ' . $ex->getMessage());
            return $this->returnError(__('api.Api_Error_Contact_Programmer'));
        }
    }





    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(WishListRequest $request)
    {
        try {
            $user = $request->user();

            if (! $user->isFavorited($request->product_id)) {
                $user->favoriteProducts()->attach($request->product_id);
                return $this->returnSuccessMessage(__('api.Add_success'));
            }

            return $this->returnError(__('api.Api_Services_exist'));
        } catch (Exception $ex) {
            Log::error('WishlistApi store' . $ex->getMessage(), [
                'trace' => $ex->getTraceAsString(),
                'input' => $request->all()
            ]);
            return $this->returnError(__('api.Api_Error_Contact_Programmer'));
        }
    }


    /**
     * Destroy resources by the given id.
     *
     * @param string $productId
     * @return void
     */
    public function destroy(WishListRequest $request)
    {
        try {
            DB::beginTransaction();

            $user = $request->user();

            if ($user->isFavorited($request->product_id)) {
                $user->favoriteProducts()->detach($request->product_id);
                DB::commit();
                return $this->returnSuccessMessage(__('api.Delete_success'));
            }

            DB::rollback();
            return $this->returnError(__('api.Api_product_not_exist'));
        } catch (Exception $ex) {
            DB::rollback();
            Log::error('WishlistApi destroy' . $ex->getMessage(), [
                'trace' => $ex->getTraceAsString(),
                'input' => $request->all()
            ]);
            return $this->returnError(__('api.Api_Error_Contact_Programmer'));
        }
    }
}
