<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Orderstatus;
use Illuminate\Http\Request;
use App\Http\Requests\Dashboard\OrderstatusRequest;
use Illuminate\Support\Facades\DB;
use Exception;
class OrderstatusesController extends Controller
{
    protected  $view = "orderstatuses";
    protected  $routeName = "orderstatuses";
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $items = Orderstatus::orderBy('id', 'ASC')->paginate(PAGANATION_COUNT);
        $data=[
            'items'=>$items,
            'routeName'=>$this->routeName
        ];
        return view('dashboard.'.$this->view.'.index',$data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data=[
            'routeName'=>$this->routeName
        ];
        return view('dashboard.'.$this->view.'.create',$data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(OrderstatusRequest $request)
    {
        try {
            DB::beginTransaction();



            $item_data = [
                'ar' => ['name' => $request->name_ar],
                'en' => ['name' => $request->name_en],
            ];


            $item = Orderstatus::create($item_data);
            $item->save();
            DB::commit();
            return redirect()->route($this->routeName)->with(['success' => __('dashboard.Add_success')]);
        } catch (Exception $ex) {
            DB::rollback();
            return redirect()->back()->with(['error' => __('dashboard.Error_DB')]);
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
        $item = Orderstatus::find($id);
        if (!$item)
            return redirect()->route($this->routeName)->with(['error' => __('dashboard.ID_Not_Found')]);
            $data=[
                'item'=>$item,
                'routeName'=>$this->routeName
            ];
            return view('dashboard.'.$this->view.'.edit', $data);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(OrderstatusRequest $request, $id)
    {
        try {
            $item = Orderstatus::find($id);
            if (!$item)
                return redirect()->route($this->routeName)->with(['error' => __('dashboard.ID_Not_Found')]);

            DB::beginTransaction();



            $item_data = [
                'ar' => ['name' => $request->name_ar],
                'en' => ['name' => $request->name_en],
            ];

            $item->update($item_data);

            DB::commit();
            return redirect()->route($this->routeName.'.edit', $id)->with(['success' => __('dashboard.Edit_success')]);
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
            $item = Orderstatus::find($id);
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
            Orderstatus::find($ids)->each(function ($item, $key) {
                $item->delete();
            });
            DB::commit();
            return response()->json(['success' => __('dashboard.Delete_success')]);
        } catch (Exception $ex) {
            DB::rollback();
            return response()->json(['error' => __('dashboard.Error_DB')]);
        }
    }
}
