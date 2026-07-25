<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Unit;
use Illuminate\Http\Request;
use App\Http\Requests\Dashboard\UnitsRequest;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Str;

class UnitsController  extends Controller
{
    protected  $view = "units";
    protected  $routeName = "units";
    protected  $namesection =  'dashboard.Units';
    protected  $folder = "units";

    public function __construct()
    {
        $this->middleware('permission:Units');
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $items = Unit::latest()->paginate(PAGANATION_COUNT);

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
    public function store(UnitsRequest $request)
    {
        try {
            DB::beginTransaction();

            $item_data = [
                'ar' => ['name' => $request->name_ar, 'slug' => slug_ar($request->name_ar)],
                'en' => ['name' => $request->name_en, 'slug' => Str::slug($request->name_en)],
            ];






            $item = Unit::create($item_data);
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


        $item = Unit::find($id);
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
    public function update(UnitsRequest $request, $id)
    {

        try {

            $item = Unit::find($id);
            if (!$item)
                return redirect()->route($this->routeName)->with(['error' => __('dashboard.ID_Not_Found')]);



            DB::beginTransaction();

            $item_data = [
                'ar' => ['name' => $request->name_ar, 'slug' => slug_ar($request->name_ar)],
                'en' => ['name' => $request->name_en, 'slug' => Str::slug($request->name_en)],
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
        $item = Unit::find($id);
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
            $ownedIds = Unit::whereIn('id', $ids)
                ->pluck('id')
                ->toArray();

            if (empty($ownedIds)) {
                return response()->json(['error' => __('dashboard.You do not have permission to delete')]);
            }
            Unit::whereIn('id', $ownedIds)->delete();
            return response()->json(['success' => __('dashboard.Delete_success')]);
        } catch (\Exception $ex) {
            DB::rollback();
            return response()->json(['error' => __('dashboard.Error_DB')]);
        }
    }
}
