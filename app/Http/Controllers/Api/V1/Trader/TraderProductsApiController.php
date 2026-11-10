<?php

namespace App\Http\Controllers\Api\V1\Trader;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Trader\TraderProductRequest;
use App\Models\{Product};
use App\Services\{SlaughterService, AdminNotifier};
use App\Traits\ApiTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * TraderProductsApiController — إدارة منتجات التاجر.
 *
 * القيود:
 *  - التاجر يرى فقط منتجاته.
 *  - كل عملية CRUD تتحقق من trader_id = auth()->id().
 *  - المنتج الجديد يُنشأ بحالة pending ويتطلب اعتماد الأدمن.
 *  - لا يمكن للتاجر تعديل سعر التكلفة (netPrice) لأي منتج معتمد إلا بإعادة اعتماد.
 */
class TraderProductsApiController extends Controller
{
    use ApiTrait;

    public function __construct(protected SlaughterService $slaughterService) {}

    /**
     * عرض منتجات التاجر (مع الفلترة).
     */
    public function index(Request $request)
    {
        try {
            $trader = $request->user();

            $query = Product::forTrader($trader->id)
                ->with(['category:id', 'translations'])
                ->when($request->filled('status'), fn($q) => $q->where('product_status', $request->status))
                ->when($request->filled('search_name'), function ($q) use ($request) {
                    $q->whereHas('translations', fn($t) =>
                        $t->where('name', 'like', '%' . $request->search_name . '%')
                    );
                })
                ->orderBy('id', 'desc');

            $products = $query->paginate(API_PAGANATION_COUNT)->withQueryString();

            $data = [
                'items' => $products->getCollection()->map(fn($p) => $this->formatProduct($p)),
                'meta' => [
                    'current_page' => $products->currentPage(),
                    'total_pages'   => $products->lastPage(),
                    'total_items'   => $products->total(),
                    'per_page'      => $products->perPage(),
                ],
            ];

            return $this->returnData('data', $data, __('api.Api_Return_Data'));

        } catch (\Exception $ex) {
            Log::error('Trader products index: ' . $ex->getMessage());
            return $this->returnError(__('api.Api_Error_Contact_Programmer'));
        }
    }

    /**
     * تفاصيل منتج واحد للتاجر.
     */
    public function show(Request $request, int $id)
    {
        try {
            $product = Product::forTrader($request->user()->id)->with('translations')->find($id);
            if (!$product) {
                return $this->returnError(__('api.Product_Not_Found'));
            }

            return $this->returnData('data', $this->formatProduct($product), __('api.Api_Return_Data'));

        } catch (\Exception $ex) {
            Log::error('Trader product show: ' . $ex->getMessage());
            return $this->returnError(__('api.Api_Error_Contact_Programmer'));
        }
    }

    /**
     * إضافة منتج جديد.
     * المنتج يُنشأ بحالة pending بانتظار اعتماد الأدمن.
     */
    public function store(TraderProductRequest $request)
    {
        try {
            $data = $request->validated();
            $trader = $request->user();

            DB::beginTransaction();

            // التحقق من سعر الذبح
            if (isset($data['slaughter_available']) && $data['slaughter_available']) {
                $validation = $this->slaughterService->validatePrice($data['slaughter_price'] ?? null);
                if (!$validation['valid']) {
                    return $this->returnError($validation['message']);
                }
            }

            $product = Product::create([
                'netPrice'            => $data['netPrice'],
                'salePrice'           => $data['salePrice'] ?? null,
                'salePrice_start'     => $data['salePrice_start'] ?? null,
                'salePrice_end'       => $data['salePrice_end'] ?? null,
                'category_id'         => $data['category_id'],
                'brand_id'            => $data['brand_id'] ?? null,
                'unit_id'             => $data['unit_id'] ?? null,
                'sku'                => $data['sku'] ?? null,
                'quantity'            => $data['quantity'],
                'track_quantity'      => $data['track_quantity'] ?? true,
                'image'              => $data['image'] ?? null,
                'trader_id'          => $trader->id,
                'product_status'     => 'pending',
                // ✅ الموقع الجغرافي يُورَّث من التاجر عبر العلاقة $product->trader->store_lat / store_lng / city_id / area_id / neighborhood_id
                // لا نحفظ هذه الحقول على المنتج نفسه.
                'slaughter_available' => $data['slaughter_available'] ?? false,
                'slaughter_required' => $data['slaughter_required'] ?? false,
                'slaughter_price'    => $data['slaughter_price'] ?? null,
            ]);

            // حفظ الترجمات (عربي/إنجليزي)
            foreach (['ar', 'en'] as $locale) {
                if (!empty($data['name'][$locale])) {
                    $product->translateOrNew($locale)->fill([
                        'name'        => $data['name'][$locale],
                        'description' => $data['description'][$locale] ?? null,
                        'details'     => $data['details'][$locale] ?? null,
                    ])->save();
                }
            }

            // إشعار الأدمن بوجود منتج جديد بانتظار الاعتماد
            AdminNotifier::newProductPendingApproval($product);

            DB::commit();

            return $this->returnData('data', [
                'id'            => $product->id,
                'product_status' => 'pending',
            ], __('api.Product_Created_Pending_Approval'));

        } catch (\Exception $ex) {
            DB::rollBack();
            Log::error('Trader product store: ' . $ex->getMessage());
            return $this->returnError(__('api.Api_Error_Contact_Programmer'));
        }
    }

    /**
     * تعديل منتج (لا يمكن للممنوح تعديل المنتج المعتمد إلا بإعادة اعتماد).
     */
    public function update(TraderProductRequest $request, int $id)
    {
        try {
            $product = Product::forTrader($request->user()->id)->find($id);
            if (!$product) {
                return $this->returnError(__('api.Product_Not_Found'));
            }

            $data = $request->validated();

            DB::beginTransaction();

            $product->update([
                'netPrice'           => $data['netPrice'] ?? $product->netPrice,
                'salePrice'          => $data['salePrice'] ?? null,
                'salePrice_start'    => $data['salePrice_start'] ?? null,
                'salePrice_end'      => $data['salePrice_end'] ?? null,
                'category_id'        => $data['category_id'] ?? $product->category_id,
                'quantity'           => $data['quantity'] ?? $product->quantity,
                'track_quantity'     => $data['track_quantity'] ?? $product->track_quantity,
                'image'              => $data['image'] ?? $product->image,
                'slaughter_available' => $data['slaughter_available'] ?? $product->slaughter_available,
                'slaughter_required' => $data['slaughter_required'] ?? $product->slaughter_required,
                'slaughter_price'    => $data['slaughter_price'] ?? $product->slaughter_price,
                // ✅ المنطقة الجغرافية تُورَّث من التاجر، لا حقل lat/lng/city_id/area_id/neighborhood_id على المنتج.
            ]);

            foreach (['ar', 'en'] as $locale) {
                if (!empty($data['name'][$locale])) {
                    $product->translateOrNew($locale)->fill([
                        'name'        => $data['name'][$locale],
                        'description' => $data['description'][$locale] ?? null,
                        'details'     => $data['details'][$locale] ?? null,
                    ])->save();
                }
            }

            // إعادة الاعتماد عند التعديل الجوهري
            if ($product->product_status === 'approved') {
                $product->update(['product_status' => 'pending', 'approved_at' => null]);
                AdminNotifier::newProductPendingApproval($product);
            }

            DB::commit();

            return $this->returnData('data', [
                'id' => $product->id,
            ], __('api.Api_Update_Success'));

        } catch (\Exception $ex) {
            DB::rollBack();
            Log::error('Trader product update: ' . $ex->getMessage());
            return $this->returnError(__('api.Api_Error_Contact_Programmer'));
        }
    }

    /**
     * إيقاف مؤقت/تفعيل منتج (بدون حذف).
     */
    public function toggleActive(Request $request, int $id)
    {
        try {
            $product = Product::forTrader($request->user()->id)->find($id);
            if (!$product) {
                return $this->returnError(__('api.Product_Not_Found'));
            }
            $product->update(['is_active' => !$product->is_active]);

            return $this->returnData('data', [
                'id' => $product->id,
                'is_active' => $product->is_active,
            ], __('api.Api_Update_Success'));

        } catch (\Exception $ex) {
            Log::error('Trader product toggle: ' . $ex->getMessage());
            return $this->returnError(__('api.Api_Error_Contact_Programmer'));
        }
    }

    /**
     * حذف منتج (ناعم SoftDelete).
     */
    public function destroy(Request $request, int $id)
    {
        try {
            $product = Product::forTrader($request->user()->id)->find($id);
            if (!$product) {
                return $this->returnError(__('api.Product_Not_Found'));
            }

            // منع الحذف إن كان ضمن طلب نشط
            $hasActiveOrder = $product->orderItems()
                ->whereHas('order', fn($q) => $q->whereIn('status_id', [1, 2, 3, 4]))
                ->exists();

            if ($hasActiveOrder) {
                return $this->returnError(__('api.Product_Has_Active_Orders'));
            }

            $product->delete();

            return $this->returnSuccessMessage(__('api.Api_Delete_Success'));

        } catch (\Exception $ex) {
            Log::error('Trader product destroy: ' . $ex->getMessage());
            return $this->returnError(__('api.Api_Error_Contact_Programmer'));
        }
    }

    private function formatProduct(Product $p): array
    {
        return [
            'id'                  => $p->id,
            'name'                => $p->translate(app()->getLocale())?->name,
            'description'         => $p->translate(app()->getLocale())?->description,
            'image'               => $p->image_url,
            'netPrice'            => $p->netPrice,
            'salePrice'           => $p->salePrice,
            'final_price'         => $p->final_price ?? $p->netPrice,
            'quantity'            => $p->quantity,
            'track_quantity'      => $p->track_quantity,
            'is_active'           => $p->is_active,
            'product_status'      => $p->product_status,
            'category_id'         => $p->category_id,
            'slaughter_available' => $p->slaughter_available,
            'slaughter_required'  => $p->slaughter_required,
            'slaughter_price'     => $p->slaughter_price,
            // ✅ الموقع الجغرافي مُورَّث من التاجر
            'city_id'             => $p->trader?->city_id,
            'area_id'             => $p->trader?->area_id,
            'neighborhood_id'     => $p->trader?->neighborhood_id,
            'lat'                 => $p->trader?->store_lat,
            'lng'                 => $p->trader?->store_lng,
            'store_address'       => $p->trader?->store_address,
            'rejection_reason'    => $p->rejection_reason,
            'created_at'          => $p->created_at?->format('Y-m-d H:i'),
        ];
    }
}
