<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Slideshow;
use Illuminate\Http\Request;
use App\Http\Requests\Dashboard\SlideshowRequest;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Str;
use App\Services\ImageUploadService;

class SlideshowsController  extends Controller
{
    protected  $view = "slideshows";
    protected  $routeName = "slideshows";
    protected  $namesection =  'dashboard.Slideshow';
    protected  $folder = "slideshows";

    public function __construct()
    {
        $this->middleware('permission:Slideshow');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $items = Slideshow::latest()->paginate(PAGANATION_COUNT);

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
    public function store(SlideshowRequest $request, ImageUploadService $imageService)
    {
        try {
            DB::beginTransaction();

            $item_data = [
                'is_active' => $request->is_active
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




            // إنشاء السلايدر
            $item = Slideshow::create($item_data);
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


        $item = Slideshow::find($id);
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
    public function update(SlideshowRequest $request, $id, ImageUploadService $imageService)
    {

        try {

            $item = Slideshow::find($id);
            if (!$item)
                return redirect()->route($this->routeName)->with(['error' => __('dashboard.ID_Not_Found')]);



            DB::beginTransaction();

            $item_data = [

                'is_active' => $request->is_active
            ];


            if ($request->has('image')) {
                $imagename = $imageService->uploadAndProcess(
                    image: $request->file('image'),
                    folder: $this->folder,
                    thumbnailWidth: 270,
                    thumbnailHeight: 140,
                    oldFilename: $item->image
                );
                $item_data['image'] = $imagename;
            }


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
        $item = Slideshow::find($id);
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
            $ownedIds = Slideshow::whereIn('id', $ids)
                ->pluck('id')
                ->toArray();

            if (empty($ownedIds)) {
                return response()->json(['error' => __('dashboard.You do not have permission to delete')]);
            }
            Slideshow::whereIn('id', $ownedIds)->delete();
            return response()->json(['success' => __('dashboard.Delete_success')]);
        } catch (\Exception $ex) {
            DB::rollback();
            return response()->json(['error' => __('dashboard.Error_DB')]);
        }
    }
}
