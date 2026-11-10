<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\{CategorySlider, Category};
use Illuminate\Http\Request;
use App\Http\Requests\Dashboard\CategorySliderRequest;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Str;
use App\Services\ImageUploadService;

class CategorySlidersController  extends Controller
{
    protected  $view = "categorysliders";
    protected  $routeName = "categorysliders";
    protected  $namesection =  'dashboard.Slideshow';
    protected  $folder = "categorysliders";


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Category $category)
    {

        $item_sliders = $category->sliders;
        $item = $category;

        $data = [
            'items' => $item_sliders,
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
    public function create(Category $category)
    {

        $data = [
            'category' => $category,
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
    public function store(CategorySliderRequest $request, Category $category, ImageUploadService $imageService)
    {
        try {
            DB::beginTransaction();



            if ($request->hasFile('image')) {
                $imagename = $imageService->uploadAndProcess(
                    image: $request->file('image'),
                    folder: $this->folder,
                    thumbnailWidth: 800,
                    thumbnailHeight: 800
                );
                $item_data['image'] = $imagename;
                $category->sliders()->create($item_data);
            }

            DB::commit();
            return redirect()->route($this->routeName, $category)->with(['success' => __('dashboard.Add_success')]);
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


        $item = CategorySlider::find($id);
        if (!$item)
            return redirect()->route($this->routeName)->with(['error' => __('dashboard.ID_Not_Found')]);

        $category = Category::find($item->category_id);
        $data = [
            'item' => $item,
            'category' => $category,
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
    public function update(CategorySliderRequest $request, $id, ImageUploadService $imageService)
    {

        try {

            $item = CategorySlider::find($id);
            if (!$item)
                return redirect()->route($this->routeName)->with(['error' => __('dashboard.ID_Not_Found')]);



            DB::beginTransaction();


            if ($request->has('image')) {
                $imagename = $imageService->uploadAndProcess(
                    image: $request->file('image'),
                    folder: $this->folder,
                    thumbnailWidth: 800,
                    thumbnailHeight: 800,
                    oldFilename: $item->image
                );
                $item_data['image'] = $imagename;
                $item->update($item_data);
            }



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
    public function destroy($id, Category $category)
    {
        $item = CategorySlider::find($id);

        // ✅ التحقق من وجود العنصر أولاً قبل محاولة الوصول لخصائصه
        if (!$item) {
            return redirect()->route($this->routeName, $category->id)->with(['error' => __('dashboard.ID_Not_Found')]);
        }

        // ✅ الآن يمكننا جلب الـ category_id بأمان
        $category_id = $item->category_id;

        $item->delete();

        return redirect()->route($this->routeName, $category_id)->with(['success' => __('dashboard.Delete_success')]);
    }

       public function deleteAll(Request $request)
    {
        // ✅ إضافة beginTransaction لأن هناك rollback في الـ catch
        DB::beginTransaction();
        try {
            $ids = array_map('intval', explode(",", $request->ids));

            if (empty($ids)) {
                DB::commit();
                return response()->json(['success' => __('dashboard.Delete_success')]);
            }

            $sliders = CategorySlider::whereIn('id', $ids)->get();

            if ($sliders->isEmpty()) {
                DB::commit();
                return response()->json(['error' => __('dashboard.You do not have permission to delete')]);
            }

            foreach ($sliders as $slider) {
                $slider->delete(); // سيتم حذف الصورة هنا
            }

            DB::commit();
            return response()->json(['success' => __('dashboard.Delete_success')]);
        } catch (\Exception $ex) {
            DB::rollback();
            return response()->json(['error' => __('dashboard.Error_DB')]);
        }
    }
}
