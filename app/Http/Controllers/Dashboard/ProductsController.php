<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\{Brand, Product, Category, Attribute,Unit,User};
use Illuminate\Http\Request;
use App\Http\Requests\Dashboard\{ProductRequest, ProductUpdateRequest, ProductsSearchRequest, ProductDescriptionRequest, ProductAttributeRequest, ProductVariantsRequest, ProductModifiersRequest};
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Str;
use App\Services\ImageUploadService;
use Carbon\Carbon;

class ProductsController  extends Controller
{
    protected  $view = "products";
    protected  $routeName = "products";
    protected  $namesection =  'dashboard.Products';
    protected  $folder = "products";

    public function __construct()
    {
        $this->middleware('permission:Products');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(ProductsSearchRequest $request)
    {

 $items = Product::FilterDashbord(request(['search_name', 'search_category_id', 'search_trader_id', 'search_active', 'search_diffrnetPrice', 'search_netPrice', 'search_diffrquantity', 'search_quantity']))
    ->with(['category' => fn($q) => $q->select('id'), 'unit','trader' => fn($q) => $q->select('id' , 'name')])
    ->select('id', 'netPrice', 'quantity', 'sku', 'category_id', 'is_active', 'image', 'salePrice', 'salePrice_start', 'salePrice_end', 'created_at', 'trader_id', 'product_status') // <-- أضف product_status هنا
    ->latest('id')
    ->paginate(PAGANATION_COUNT)
    ->withQueryString();


        $traders =  User::traders()->select('id','name')->orderBy('id', 'asc')->get();

        $categories = Category::active()->parent()->select('id')->orderBy('id', 'asc')->get();

$units = Unit::select('id')->get();
        $data = [
            'items' => $items,
            'traders' => $traders,
            'categories' => $categories,
            'units' => $units,
            'routeName' => $this->routeName,
            'folder' => $this->folder,
            'namesection' => $this->namesection
        ];
        return view('dashboard.' . $this->view . '.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $brands = Brand::active()->select('id')->orderBy('id', 'asc')->get();
        $categories = Category::active()->parent()->select('id')->orderBy('id', 'asc')->get();
        $units = Unit::select('id')->get();

        $data = [
            'routeName' => $this->routeName,
            'namesection' => $this->namesection,
            'brands' => $brands,
            'categories' => $categories,
            'units' => $units,

        ];
        return view('dashboard.' . $this->view . '.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ProductRequest $request, ImageUploadService $imageService)
    {
        try {
            DB::beginTransaction();
            $item_data = [
                'ar' => [
                    'name'        => $request->name_ar,
                    'slug'        => slug_ar($request->name_ar),
                    'description' => $request->description_ar,
                    'details'     => $request->details_ar,
                ],
                'en' => [
                    'name'        => $request->name_en,
                    'slug'        => Str::slug($request->name_en),
                    'description' => $request->description_en,
                    'details'     => $request->details_en,
                ],
                'is_active'    => $request->is_active,
                'new_arrivals' => $request->new_arrivals,
                'calories'     => $request->calories,
                'category_id'  => $request->category_id,
                'brand_id'     => $request->brand_id,
                'sku'          => $request->sku,
                'trader_id'    => $request->trader_id,
                'track_quantity' => $request->boolean('track_quantity'),
                'quantity'     => $request->boolean('track_quantity') ? $request->quantity : null,
                'netPrice'     => $request->netPrice,
                'salePrice_start' => $request->salePrice_start != null ? Carbon::parse($request->salePrice_start)->format('Y-m-d') : $request->salePrice_start,
                'salePrice_end'   => $request->salePrice_end   != null ? Carbon::parse($request->salePrice_end)->format('Y-m-d')   : $request->salePrice_end,
                'salePrice'    => $request->salePrice,
                'unit_id'      => $request->unit_id,
                // === مواشي وسيط (البند الثالث) ===
                'video_url'         => $request->video_url,
                'specifications'    => $request->specifications,
                'product_status'    => $request->product_status ?? 'pending',
                'slaughter_available' => $request->boolean('slaughter_available'),
                'slaughter_required'  => $request->boolean('slaughter_required'),
                'slaughter_price'     => $request->slaughter_price,
            ];

            if ($request->hasFile('image')) {
                $imagename = $imageService->uploadAndProcess(
                    image: $request->file('image'),
                    folder: $this->folder,
                    thumbnailWidth: 800,
                    thumbnailHeight: 800
                );
                $item_data['image'] = $imagename;
            }

            $item = Product::create($item_data);
            $item->save();
            DB::commit();
            return redirect()->route('products.edit', ['id' => $item->id])->with(['success' => __('dashboard.Add_success')]);
        } catch (Exception $ex) {
            DB::rollback();
            \Illuminate\Support\Facades\Log::error('Product store failed: ' . $ex->getMessage());
            return redirect()->back()->with(['error' => __('dashboard.Error_DB')])->withInput();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $item = Product::find($id);
        if (!$item)
            return redirect()->route($this->routeName)->with(['error' => __('dashboard.ID_Not_Found')]);
        $brands = Brand::active()->select('id')->orderBy('id', 'asc')->get();
        $categories = Category::active()->parent()->select('id')->orderBy('id', 'asc')->get();
$units = Unit::select('id')->get();


        $data = [
            'item' => $item,
            'routeName' => $this->routeName,
            'folder' => $this->folder,
            'namesection' => $this->namesection,
            'brands' => $brands,
            'categories' => $categories,
            'units' => $units,

        ];
        return view('dashboard.' . $this->view . '.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ProductUpdateRequest $request, $id, ImageUploadService $imageService)
    {
        try {
            $item = Product::find($id);
            if (!$item)
                return redirect()->route($this->routeName)->with(['error' => __('dashboard.ID_Not_Found')]);
            DB::beginTransaction();
            $item_data = [
                'ar' => [
                    'name'        => $request->name_ar,
                    'slug'        => slug_ar($request->name_ar),
                    'description' => $request->description_ar,
                    'details'     => $request->details_ar,
                ],
                'en' => [
                    'name'        => $request->name_en,
                    'slug'        => Str::slug($request->name_en),
                    'description' => $request->description_en,
                    'details'     => $request->details_en,
                ],
                'is_active'    => $request->is_active,
                'new_arrivals' => $request->new_arrivals,
                'calories'     => $request->calories,
                'category_id'  => $request->category_id,
                'brand_id'     => $request->brand_id,
                'sku'          => $request->sku,
                'trader_id'    => $request->trader_id ?? $item->trader_id,
                'track_quantity' => $request->boolean('track_quantity'),
                'quantity'     => $request->boolean('track_quantity') ? $request->quantity : null,
                'netPrice'     => $request->netPrice,
                'salePrice_start' => $request->salePrice_start != null ? Carbon::parse($request->salePrice_start)->format('Y-m-d') : $request->salePrice_start,
                'salePrice_end'   => $request->salePrice_end   != null ? Carbon::parse($request->salePrice_end)->format('Y-m-d')   : $request->salePrice_end,
                'salePrice'    => $request->salePrice,
                'unit_id'      => $request->unit_id,
                // === مواشي وسيط (البند الثالث) ===
                'video_url'         => $request->video_url,
                'specifications'    => $request->specifications,
                'product_status'    => $request->product_status ?? $item->product_status,
                'slaughter_available' => $request->boolean('slaughter_available'),
                'slaughter_required'  => $request->boolean('slaughter_required'),
                'slaughter_price'     => $request->slaughter_price,
            ];

            if ($request->hasFile('image')) {
                $imagename = $imageService->uploadAndProcess(
                    image: $request->file('image'),
                    folder: $this->folder,
                    thumbnailWidth: 800,
                    thumbnailHeight: 800,
                    oldFilename: $item->image
                );
                $item_data['image'] = $imagename;
            }

            $item->update($item_data);
            DB::commit();
            return redirect()->route($this->routeName . '.edit', $id)->with(['success' => __('dashboard.Edit_success')]);
        } catch (Exception $ex) {
            DB::rollback();
            \Illuminate\Support\Facades\Log::error('Product update failed: ' . $ex->getMessage());
            return redirect()->back()->with(['error' => __('dashboard.Error_DB')])->withInput();
        }
    }

    /**
     * تبديل حالة المنتج (تفعيل/إيقاف).
     * - لو is_active=0 → يوقف المنتج + إشعار التاجر (FCM).
     * - لو is_active=1 → يفعّله (إذا كان موقوف) + إشعار التاجر (FCM).
     */
    public function toggleActive(Request $request, $id)
    {
        try {
            $item = Product::with('trader')->find($id);
            if (!$item) {
                return redirect()->route($this->routeName)->with(['error' => __('dashboard.ID_Not_Found')]);
            }
            $request->validate([
                'is_active' => 'required|in:0,1',
            ], [], [
                'is_active' => __('dashboard.Efficiency_Status'),
            ]);
            DB::beginTransaction();
            $item->update([
                'is_active' => $request->is_active,
                // لو تم إيقاف المنتج، نحدّث product_status إلى suspended
                'product_status' => $request->is_active == 0 ? 'suspended' : $item->product_status,
            ]);

            // إشعار FCM للتاجر
            if ($item->trader && $item->trader->device_token) {
                try {
                    $productName = $item->translate(app()->getLocale())?->name ?? '';
                    $fcm = new \App\Services\FCMServices();
                    if ($request->is_active == 0) {
                        $fcm->sendMsg(
                            $item->trader->id,
                            $item->trader->device_token,
                            __('notifications.product_suspended_title'),
                            __('notifications.product_suspended_message', ['name' => $productName])
                        );
                    } else {
                        $fcm->sendMsg(
                            $item->trader->id,
                            $item->trader->device_token,
                            __('notifications.product_reactivated_title'),
                            __('notifications.product_reactivated_message', ['name' => $productName])
                        );
                    }
                } catch (\Throwable $e) {
                    \Illuminate\Support\Facades\Log::warning("FCM product toggle failed: " . $e->getMessage());
                }
            }

            DB::commit();
            return redirect()->route($this->routeName)->with(['success' => __('dashboard.Edit_success')]);
        } catch (Exception $ex) {
            DB::rollback();
            \Illuminate\Support\Facades\Log::error('Product toggleActive failed: ' . $ex->getMessage());
            return redirect()->back()->with(['error' => __('dashboard.Error_DB')]);
        }
    }

    /**
     * تحديث المخزون فقط (إدارة سريعة للكميات).
     */
    public function updateStock(Request $request, $id)
    {
        try {
            $item = Product::find($id);
            if (!$item) {
                return redirect()->route($this->routeName)->with(['error' => __('dashboard.ID_Not_Found')]);
            }
            $request->validate([
                'track_quantity' => 'required|boolean',
                'quantity'       => 'nullable|numeric|min:0',
            ], [], [
                'track_quantity' => __('dashboard.track_quantity'),
                'quantity'      => __('dashboard.Quantity'),
            ]);
            DB::beginTransaction();
            $item->update([
                'track_quantity' => $request->boolean('track_quantity'),
                'quantity'       => $request->boolean('track_quantity') ? $request->quantity : null,
            ]);
            DB::commit();
            return redirect()->route($this->routeName)->with(['success' => __('dashboard.Stock_Updated_Success')]);
        } catch (Exception $ex) {
            DB::rollback();
            return redirect()->back()->with(['error' => __('dashboard.Error_DB')]);
        }
    }



    public function editdescription($id)
    {

        $item = Product::find($id, ['id']);
        if (!$item)
            return redirect()->route('products')->with(['error' => __('dashboard.ID_Not_Found')]);
        $data = [
            'item' => $item,
            'routeName' => $this->routeName,
            'folder' => $this->folder,
            'namesection' => $this->namesection,
        ];
        return view('dashboard.' . $this->view . '.descriptions', $data);
    }

    public function updatedescription(ProductDescriptionRequest $request)
    {
        try {
            $item = Product::find($request->id);
            if (!$item)
                return redirect()->route('products')->with(['error' => __('dashboard.ID_Not_Found')]);
            DB::beginTransaction();
            $product_data = [
                'ar' => ['description' => $request->description_ar, 'details' => $request->details_ar],
                'en' => ['description' => $request->description_en, 'details' => $request->details_en],
            ];
            $item->update($product_data);
            DB::commit();
            return redirect()->route('products.editdescription', $item->id)->with(['success' => __('dashboard.Edit_success')]);
        } catch (Exception $ex) {
            DB::rollback();
            return redirect()->back()->with(['error' => __('dashboard.Error_DB')]);
        }
    }

    public function productImages($id)
    {

        $item = Product::find($id, ['id']);
        if (!$item)
            return redirect()->route('products')->with(['error' => __('dashboard.ID_Not_Found')]);
        $data = [
            'item' => $item,
            'routeName' => $this->routeName,
            'folder' => $this->folder,
            'namesection' => $this->namesection,
        ];
        return view('dashboard.' . $this->view . '.images', $data);
    }


    // صفحة تعديل خصائص المنتج
    public function editattribute($id)
    {



        $item = Product::find($id, ['id']);
        if (!$item)
            return redirect()->route('products')->with(['error' => __('dashboard.ID_Not_Found')]);

        $attributes = Attribute::with('values')->get();
        $selectedValues = $item->attributeValues
            ->groupBy('attribute_id')               // تجميع حسب الخاصية
            ->map(fn($group) => $group->pluck('id')->toArray()) // فقط قيم كل خاصية
            ->toArray();

        $data = [
            'item' => $item,
            'attributes' => $attributes,
            'selectedValues' => $selectedValues,
            'routeName' => $this->routeName,
            'folder' => $this->folder,
            'namesection' => $this->namesection,
        ];

        return view('dashboard.' . $this->view . '.attribute', $data);
    }

    public function updateattribute(ProductAttributeRequest $request, Product $product)
    {
        try {
            DB::transaction(function () use ($product, $request) {

                // نجمع كل الـ IDs المختارة في مصفوفة واحدة
                $attributeValues = collect($request->input('attribute_values', []))
                    ->flatten()
                    ->filter() // تتجنب أي null أو قيم فارغة
                    ->toArray();

                // sync مع الـ Product
                $product->attributeValues()->sync($attributeValues);
            });

            return redirect()->route('products.editattribute', $product->id)
                ->with(['success' => __('dashboard.Edit_success')]);
        } catch (Exception $ex) {
            return redirect()->back()->with(['error' => __('dashboard.Error_DB')]);
        }
    }






    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $item = Product::find($id);
        if (!$item)
            return redirect()->route($this->routeName)->with(['error' => __('dashboard.ID_Not_Found')]);
        $item->delete();
        return redirect()->route($this->routeName)->with(['success' => __('dashboard.Delete_success')]);
    }

    public function deleteAll(Request $request)
    {
        try {
            $ids = array_map('intval', explode(",", $request->ids));

            if (empty($ids)) {
                return response()->json(['success' => __('dashboard.Delete_success')]);
            }

            // تحقق من ملكية السلايدز
            $ownedIds = Product::whereIn('id', $ids)
                ->pluck('id')
                ->toArray();

            if (empty($ownedIds)) {
                return response()->json(['error' => __('dashboard.You do not have permission to delete')]);
            }
            Product::whereIn('id', $ownedIds)->delete();
            return response()->json(['success' => __('dashboard.Delete_success')]);
        } catch (\Exception $ex) {
            DB::rollback();
            return response()->json(['error' => __('dashboard.Error_DB')]);
        }
    }

    public function getProductAttributes($id)
    {
        $product = Product::with('attributeValues.attribute')->find($id);

        if (!$product) {
            return response()->json([
                'status' => false,
                'message' => __('dashboard.ID_Not_Found')
            ], 404);
        }

        $attributes = [];

        foreach ($product->attributeValues as $value) {
            $attributeId = $value->attribute->id;

            if (!isset($attributes[$attributeId])) {
                $attributes[$attributeId] = [
                    'attribute_id' => $attributeId,
                    'attribute_name' => $value->attribute->name,
                    'attribute_type' => $value->attribute->type,
                    'values' => []
                ];
            }

            if ($value->attribute->type === 'color') {
                $attributes[$attributeId]['values'][] = [
                    'id' => $value->id,
                    'name' => $value->value,      // اسم اللون
                    'color_code' => $value->color_code  // كود اللون
                ];
            } else { // نوع select
                $attributes[$attributeId]['values'][] = [
                    'id' => $value->id,
                    'name' => $value->value
                ];
            }
        }

        return response()->json([
            'status' => true,
            'product_id' => $product->id,
            'attributes' => array_values($attributes)
        ]);
    }






    /**
     * صفحة الفيرنتس (خطوة 5): التركيبات تُبنى تلقائياً من القيم اللي
     * تم اختيارها مسبقاً بخطوة "الخصائص" (attribute_value_product) والتابعة
     * لخصائص is_variation = true. ما فيه إعادة اختيار بهذي الصفحة —
     * فقط تحديد سعر/كمية/SKU لكل تركيبة.
     */
    public function editVariants($id)
    {
        $item = Product::with(['variants.attributeValues', 'attributeValues.attribute'])->find($id);
        if (!$item)
            return redirect()->route('products')->with(['error' => __('dashboard.ID_Not_Found')]);

        $variationGroups = $item->variationValues()->groupBy('attribute_id');

        if ($variationGroups->isEmpty()) {
            return redirect()->route('products.editattribute', $item->id)
                ->with(['error' => __('dashboard.No_Variation_Values_Selected')]);
        }

        $valueGroups = $variationGroups->map(fn($values) => $values->pluck('id')->all())->values()->all();
        $combinations = $this->cartesianProduct($valueGroups);

        $existingVariants = $item->variants->keyBy('combination_key');
        $allValues = $item->attributeValues;

        $rows = collect($combinations)->map(function ($combo) use ($existingVariants, $allValues) {
            $key = \App\Models\ProductVariant::makeCombinationKey($combo);
            $variant = $existingVariants->get($key);

            $label = collect($combo)
                ->map(fn($id) => optional($allValues->firstWhere('id', $id))->value)
                ->filter()
                ->implode(' / ');

            return [
                'key' => $key,
                'value_ids' => $combo,
                'label' => $label,
                'sku' => $variant->sku ?? '',
                'price' => $variant->price ?? '',
                'quantity' => $variant->quantity ?? '',
            ];
        });

        return view('dashboard.' . $this->view . '.variants', [
            'item' => $item,
            'rows' => $rows,
            'routeName' => $this->routeName,
        ]);
    }

    public function updateVariants(ProductVariantsRequest $request, Product $product)
    {
        try {
            DB::transaction(function () use ($request, $product) {
                $incomingKeys = [];

                foreach ($request->input('variants', []) as $row) {
                    $valueIds = array_filter(array_map('intval', explode(',', $row['value_ids'])));
                    if (empty($valueIds)) {
                        continue;
                    }

                    $key = \App\Models\ProductVariant::makeCombinationKey($valueIds);
                    $incomingKeys[] = $key;

                    $variant = $product->variants()->updateOrCreate(
                        ['combination_key' => $key],
                        [
                            'sku' => $row['sku'] !== '' ? $row['sku'] : null,
                            'price' => $row['price'] !== '' ? $row['price'] : null,
                            'quantity' => $row['quantity'] !== '' ? $row['quantity'] : null,
                        ]
                    );

                    $variant->attributeValues()->sync($valueIds);
                }

                // احذف أي فيرنت قديم ما عاد ضمن التركيبات الحالية (soft delete)
                $product->variants()
                    ->whereNotIn('combination_key', $incomingKeys)
                    ->get()
                    ->each(fn($variant) => $variant->delete());
            });

            return redirect()->route('products.editVariants', $product->id)
                ->with(['success' => __('dashboard.Edit_success')]);
        } catch (Exception $ex) {
            return redirect()->back()->with(['error' => __('dashboard.Error_DB')]);
        }
    }

    /**
     * صفحة الإضافات (خطوة 6): سعر/كمية اختياريين لكل قيمة من القيم
     * المختارة بخطوة "الخصائص" والتابعة لخصائص is_variation = false.
     * السعر هنا "إضافي" دايماً — يُجمع فوق سعر المنتج (أو الفيرنت).
     */
    public function editModifiers($id)
    {
        $item = Product::with(['attributeValues.attribute'])->find($id);
        if (!$item)
            return redirect()->route('products')->with(['error' => __('dashboard.ID_Not_Found')]);

        $modifierGroups = $item->modifierValues()->groupBy('attribute_id');

        if ($modifierGroups->isEmpty()) {
            return redirect()->route('products.editattribute', $item->id)
                ->with(['error' => __('dashboard.No_Modifier_Values_Selected')]);
        }

        return view('dashboard.' . $this->view . '.modifiers', [
            'item' => $item,
            'modifierGroups' => $modifierGroups,
            'routeName' => $this->routeName,
        ]);
    }

    public function updateModifiers(ProductModifiersRequest $request, Product $product)
    {
        try {
            DB::transaction(function () use ($request, $product) {
                foreach ($request->input('modifiers', []) as $valueId => $data) {
                    // تأكد إن القيمة فعلاً مرتبطة بهذا المنتج قبل التحديث
                    if (!$product->attributeValues->contains('id', (int) $valueId)) {
                        continue;
                    }

                    $product->attributeValues()->updateExistingPivot((int) $valueId, [
                        'price' => $data['price'] !== '' ? $data['price'] : 0,
                        'quantity' => $data['quantity'] !== '' ? $data['quantity'] : null,
                        'is_default' => $data['is_default'] ?? false,
                    ]);
                }
            });

            return redirect()->route('products.editModifiers', $product->id)
                ->with(['success' => __('dashboard.Edit_success')]);
        } catch (Exception $ex) {
            return redirect()->back()->with(['error' => __('dashboard.Error_DB')]);
        }
    }

    /**
     * كل التركيبات الممكنة من مصفوفات القيم (Cartesian Product).
     * مثال: [[1,2], [5,6]] => [[1,5],[1,6],[2,5],[2,6]]
     */
    private function cartesianProduct(array $arrays): array
    {
        $result = [[]];
        foreach ($arrays as $values) {
            $tmp = [];
            foreach ($result as $combo) {
                foreach ($values as $value) {
                    $tmp[] = array_merge($combo, [$value]);
                }
            }
            $result = $tmp;
        }
        return $result;
    }
}
