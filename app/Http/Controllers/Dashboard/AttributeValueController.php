<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\{AttributeValue, Attribute};
use Illuminate\Http\Request;
use App\Http\Requests\Dashboard\AttributeValueRequest;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Str;

class AttributeValueController  extends Controller
{
    protected  $view = "attributevalues";
    protected  $routeName = "attributevalues";
    protected  $namesection =  'dashboard.Property values';
    protected  $folder = "attributevalues";


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Attribute $attribute)
    {

        $item_values = $attribute->values;
        $item = $attribute;

        $data = [
            'items' => $item_values,
            'item' => $item,
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
    public function create(Attribute $attribute)
    {

        $data = [
            'attribute' => $attribute,
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
    public function store(AttributeValueRequest $request, Attribute $attribute)
    {
        try {
            DB::beginTransaction();


            $attribute->values()->create([
                'ar' => ['value' => $request->value_ar],
                'en' => ['value' => $request->value_en],
                'color_code' => $request->color_code ?? null
            ]);

            DB::commit();
            return redirect()->route($this->routeName, $attribute)->with(['success' => __('dashboard.Add_success')]);
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


        $item = AttributeValue::find($id);
        if (!$item)
            return redirect()->route($this->routeName)->with(['error' => __('dashboard.ID_Not_Found')]);

        $attribute = Attribute::find($item->attribute_id);
        $data = [
            'item' => $item,
            'attribute' => $attribute,
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
    public function update(AttributeValueRequest $request, $id)
    {

        try {

            $item = AttributeValue::find($id);
            if (!$item)
                return redirect()->route($this->routeName)->with(['error' => __('dashboard.ID_Not_Found')]);



            DB::beginTransaction();

            $item_data = [
                'ar' => ['value' => $request->value_ar],
                'en' => ['value' => $request->value_en],
                'color_code' => $request->color_code ?? null
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
    public function destroy($id, Attribute $attribute)
    {
        $item = AttributeValue::find($id);

        // ✅ التحقق من وجود العنصر أولاً قبل محاولة الوصول لخصائصه
        if (!$item) {
            return redirect()->route($this->routeName, $attribute->id)->with(['error' => __('dashboard.ID_Not_Found')]);
        }

        // ✅ الآن يمكننا جلب الـ attribute_id بأمان
        $attribute_id = $item->attribute_id;

        $item->delete();

        return redirect()->route($this->routeName, $attribute_id)->with(['success' => __('dashboard.Delete_success')]);
    }

    public function deleteAll(Request $request)
    {
        try {
            $ids = array_map('intval', explode(",", $request->ids));

            if (empty($ids)) {
                return response()->json(['success' => __('dashboard.Delete_success')]);
            }

            // تحقق من ملكية السلايدز
            $ownedIds = AttributeValue::whereIn('id', $ids)
                ->pluck('id')
                ->toArray();

            if (empty($ownedIds)) {
                return response()->json(['error' => __('dashboard.You do not have permission to delete')]);
            }
            AttributeValue::whereIn('id', $ownedIds)->delete();
            return response()->json(['success' => __('dashboard.Delete_success')]);
        } catch (\Exception $ex) {
            DB::rollback();
            return response()->json(['error' => __('dashboard.Error_DB')]);
        }
    }
}
