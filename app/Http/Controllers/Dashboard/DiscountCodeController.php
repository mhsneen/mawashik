<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\{DiscountCode, User, DiscountCodeUsage};
use Illuminate\Http\Request;
use App\Http\Requests\Dashboard\{DiscountCodeRequest, DiscountCodeUpdateRequest};
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Str;

class DiscountCodeController  extends Controller
{
    protected  $view = "discountcodes";
    protected  $routeName = "discountcodes";
    protected  $namesection =  'dashboard.Offers and discounts';
    protected  $folder = "discountcodes";

    public function __construct()
    {
        $this->middleware('permission:Offers and discounts');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $items = DiscountCode::FilterDashbordDiscountCodes(request(['search_status']))->orderBy('id', 'DESC')->paginate(PAGANATION_COUNT)->withQueryString();
        $data = [
            'items' => $items,
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
        do {
            $code = strtoupper(Str::random(5));
        } while (DiscountCode::where('code', $code)->exists());

        $data = [
            'routeName' => $this->routeName,
            'code' => $code,
            'namesection' => $this->namesection
        ];
        return view('dashboard.' . $this->view . '.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(DiscountCodeRequest $request)
    {



        DB::beginTransaction();
        $max_usage = null;
        if ($request->usage_type == 'once') {
            $max_usage = 1;
        }
        if ($request->usage_type == 'limited') {
            $max_usage = $request->max_usage;
        }

        $item_data = [
            'code' => $request->code,
            'discount_percentage' => $request->discount_percentage,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'usage_type' => $request->usage_type,
            'max_usage' => $max_usage,
            // 'usage_count' عادة صفر عند الإنشاء
            'usage_count' => 0,
            'is_active' => $request->is_active,
        ];

        $item = DiscountCode::create($item_data);
        $item->save();
        DB::commit();
        return redirect()->route($this->routeName)->with(['success' => __('dashboard.Add_success')]);
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
        $item = DiscountCode::find($id);
        if (!$item)
            return redirect()->route($this->routeName)->with(['error' => __('dashboard.ID_Not_Found')]);

        $data = [
            'item' => $item,
            'routeName' => $this->routeName,
            'folder' => $this->folder,
            'namesection' => $this->namesection,
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
    public function update(DiscountCodeUpdateRequest $request, $id)
    {
        try {
            $item = DiscountCode::find($id);
            if (!$item)
                return redirect()->route($this->routeName)->with(['error' => __('dashboard.ID_Not_Found')]);

            DB::beginTransaction();

            $max_usage = null;
            if ($request->usage_type == 'once') {
                $max_usage = 1;
            }
            if ($request->usage_type == 'limited') {
                $max_usage = $request->max_usage;
            }


            $item_data = [
                'code' => $request->code,
                'discount_percentage' => $request->discount_percentage,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'usage_type' => $request->usage_type,
                'max_usage' => $max_usage,
                'is_active' => $request->is_active,
            ];

            $item->update($item_data);

            DB::commit();
            return redirect()->route($this->routeName . '.edit', $id)->with(['success' => __('dashboard.Edit_success')]);
        } catch (Exception $ex) {
            DB::rollback();
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
        try {
            $item = DiscountCode::find($id);
            if (!$item)
                return redirect()->route($this->routeName)->with(['error' => __('dashboard.ID_Not_Found')]);
            DB::beginTransaction();
            $item->delete();
            DB::commit();
            return redirect()->route($this->routeName)->with(['success' => __('dashboard.Delete_success')]);
        } catch (Exception $ex) {
            DB::rollback();
            return redirect()->back()->with(['error' => __('dashboard.Error_DB')]);
        }
    }

    public function deleteAll(Request $request)
    {

        try {
            $ids = explode(",", $request->ids);
            DB::beginTransaction();
            DiscountCode::find($ids)->each(function ($item, $key) {
                $item->delete();
            });
            DB::commit();
            return response()->json(['success' => __('dashboard.Delete_success')]);
        } catch (Exception $ex) {
            DB::rollback();
            return response()->json(['error' => __('dashboard.Error_DB')]);
        }
    }

    public function user_usage($id)
    {
        $discountcode = DiscountCode::find($id);
        if (!$discountcode) {
            return redirect()->route($this->routeName)->with(['error' => __('dashboard.ID_Not_Found')]);
        }

        // ✅ حساب عدد الاستخدامات للطلبات الموجودة فعلياً (الغير محذوفة)
        $valid_usage_count = DiscountCodeUsage::where('discount_code_id', $id)
            ->whereHas('order', function ($query) {
                $query->whereNull('deleted_at'); // استبعاد الطلبات المحذوفة
            })
            ->count();

        $items = DiscountCodeUsage::where('discount_code_id', $id)
            ->with([
                'user:id,name,mobile',
                // نستخدم withTrashed لعرض السجل في الجدول، لكن سنميزه بصرياً
                'order' => function ($q) {
                    $q->withTrashed()->select('id', 'order_number', 'total_price', 'discount_amount', 'added_tax', 'price_after_discount', 'tax_amount', 'price_after_tax', 'deleted_at');
                },
            ])
            ->orderBy('id', 'DESC')
            ->paginate(PAGANATION_COUNT);

        $data = [
            'items' => $items,
            'discountcode' => $discountcode,
            'valid_usage_count' => $valid_usage_count, // ✅ تمرير العدد للواجهة
            'routeName' => $this->routeName,
            'folder' => $this->folder,
            'namesection' => $this->namesection
        ];
        return view('dashboard.' . $this->view . '.user_usage', $data);
    }
}
