<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Socialmedialink;
use Illuminate\Http\Request;
use App\Http\Requests\Dashboard\SocialmedialinkRequest;
use Illuminate\Support\Facades\DB;
use Exception;
use App\Services\ImageUploadService;

class SocialmedialinksController  extends Controller
{
    protected  $view = "socialmedialinks";
    protected  $routeName = "socialmedialinks";
    protected  $namesection =  'dashboard.Social_Media';
    protected  $folder = "socialmedialinks";

    public function __construct()
    {
        $this->middleware('permission:Social_Media');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $items = Socialmedialink::orderBy('id', 'DESC')->paginate(PAGANATION_COUNT);

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
    public function store(SocialmedialinkRequest $request, ImageUploadService $imageService)
    {


        try {
            DB::beginTransaction();
            $item_data = [
                'name' => $request->name,
                'link' => $request->link,
            ];
            if ($request->has('icon')) {
                $item_data['icon'] = $imageService->uploadAndProcess(
                    image: $request->file('icon'),
                    folder: $this->folder,
                    thumbnailWidth: 50,
                    thumbnailHeight: 50
                );
            }


            $item = Socialmedialink::create($item_data);
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
        $item = Socialmedialink::find($id);
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
    public function update(SocialmedialinkRequest $request, $id, ImageUploadService $imageService)
    {
        try {
            $item = Socialmedialink::find($id);
            if (!$item)
                return redirect()->route($this->routeName)->with(['error' => __('dashboard.ID_Not_Found')]);

            DB::beginTransaction();

            $item_data = [
                'name' => $request->name,
                'link' => $request->link,
            ];

            if ($request->has('icon')) {
                $item_data['icon'] = $imageService->uploadAndProcess(
                    image: $request->file('icon'),
                    folder: $this->folder,
                    thumbnailWidth: 50,
                    thumbnailHeight: 50,
                    oldFilename: $item->icon
                );
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
    public function destroy($id, ImageUploadService $imageService)
    {

        $item = Socialmedialink::find($id);
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
            Socialmedialink::whereIn('id', $ids)->delete();
            return response()->json(['success' => __('dashboard.Delete_success')]);
        } catch (Exception $ex) {
            DB::rollback();
            return response()->json(['error' => __('dashboard.Error_DB')]);
        }
    }
}
