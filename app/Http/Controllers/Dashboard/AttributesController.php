<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Attribute;
use Illuminate\Http\Request;
use App\Http\Requests\Dashboard\AttributeRequest;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Str;

class AttributesController  extends Controller
{
    protected  $view = "attributes";
    protected  $routeName = "attributes";
    protected  $namesection =  'dashboard.Product_Features';
    protected  $folder = "attributes";

    public function __construct()
    {
        $this->middleware('permission:Product_Features');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $items = Attribute::latest()->withCount('values')->paginate(PAGANATION_COUNT);

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
    public function store(AttributeRequest $request)
    {
        try {
            DB::beginTransaction();

            $item_data = [
                'ar' => ['name' => $request->name_ar, 'slug' => slug_ar($request->name_ar)],
                'en' => ['name' => $request->name_en, 'slug' => Str::slug($request->name_en)],
                'type' => $request->type,
                'is_variation' => $request->is_variation,
            ];
            $item = Attribute::create($item_data);
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


        $item = Attribute::find($id);
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
    public function update(AttributeRequest $request, $id)
    {

        try {

            $item = Attribute::find($id);
            if (!$item)
                return redirect()->route($this->routeName)->with(['error' => __('dashboard.ID_Not_Found')]);



            DB::beginTransaction();

            $item_data = [
                'ar' => ['name' => $request->name_ar, 'slug' => slug_ar($request->name_ar)],
                'en' => ['name' => $request->name_en, 'slug' => Str::slug($request->name_en)],
                'type' => $request->type,
                'is_variation' => $request->is_variation,
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
        $item = Attribute::find($id);
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
            $ownedIds = Attribute::whereIn('id', $ids)
                ->pluck('id')
                ->toArray();

            if (empty($ownedIds)) {
                return response()->json(['error' => __('dashboard.You do not have permission to delete')]);
            }
            Attribute::whereIn('id', $ownedIds)->delete();
            return response()->json(['success' => __('dashboard.Delete_success')]);
        } catch (\Exception $ex) {
            DB::rollback();
            return response()->json(['error' => __('dashboard.Error_DB')]);
        }
    }
}
