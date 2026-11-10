<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\OrderCancelReason;
use Illuminate\Http\Request;
use App\Http\Requests\Dashboard\OrderCancelReasonRequest;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Str;

class OrderCancelReasonsController  extends Controller
{
    protected  $view = "ordercancelreasons";
    protected  $routeName = "ordercancelreasons";
    protected  $namesection =  'dashboard.Cancel_Reason_Order';
    protected  $folder = "ordercancelreasons";

    public function __construct()
    {
        $this->middleware('permission:Cancel_Reason_Order');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $items = OrderCancelReason::paginate(PAGANATION_COUNT);
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

        $data = [
            'routeName' => $this->routeName,
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
    public function store(OrderCancelReasonRequest $request)
    {

        DB::beginTransaction();
        $item_data = [
            'ar' => ['name' => $request->name_ar, 'slug' => slug_ar($request->name_ar),],
            'en' => ['name' => $request->name_en, 'slug' => Str::slug($request->name_en)],
            'is_active' => $request->is_active
        ];

        $item = OrderCancelReason::create($item_data);
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
        $item = OrderCancelReason::find($id);
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
    public function update(OrderCancelReasonRequest $request, $id)
    {
        try {
            $item = OrderCancelReason::find($id);
            if (!$item)
                return redirect()->route($this->routeName)->with(['error' => __('dashboard.ID_Not_Found')]);

            DB::beginTransaction();
            $item_data = [
                'ar' => ['name' => $request->name_ar, 'slug' => slug_ar($request->name_ar),],
                'en' => ['name' => $request->name_en, 'slug' => Str::slug($request->name_en)],
                'is_active' => $request->is_active
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
        $item = OrderCancelReason::find($id);
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
            OrderCancelReason::whereIn('id', $ids)->delete();
            return response()->json(['success' => __('dashboard.Delete_success')]);
        } catch (Exception $ex) {
            DB::rollback();
            return response()->json(['error' => __('dashboard.Error_DB')]);
        }
    }
}
