<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Onboarding;
use Illuminate\Http\Request;
use App\Http\Requests\Dashboard\OnboardingRequest;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Str;
use App\Services\ImageUploadService;

class OnboardingsController  extends Controller
{
    protected  $view = "onboardings";
    protected  $routeName = "onboardings";
    protected  $namesection =  'dashboard.Onboardings';
    protected  $folder = "onboardings";


    public function __construct()
    {
        $this->middleware('permission:Onboardings');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $items = Onboarding::paginate(PAGANATION_COUNT);

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
    public function store(OnboardingRequest $request, ImageUploadService $imageService)
    {
        try {
            DB::beginTransaction();
            $item_data = [
                'ar' => ['name' => $request->name_ar, 'slug' => slug_ar($request->name_ar), 'details' => $request->details_ar],
                'en' => ['name' => $request->name_en, 'slug' => Str::slug($request->name_en), 'details' => $request->details_en],
            ];

            if ($request->hasFile('image')) {
                $imagename = $imageService->uploadAndProcess(
                    image: $request->file('image'),
                    folder: $this->folder,
                    thumbnailWidth: 250,
                    thumbnailHeight: 250
                );
                $item_data['image'] = $imagename;
            }

            $item = Onboarding::create($item_data);
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
        $item = Onboarding::find($id);
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
    public function update(OnboardingRequest $request, $id, ImageUploadService $imageService)
    {
        try {
            $item = Onboarding::find($id);
            if (!$item)
                return redirect()->route($this->routeName)->with(['error' => __('dashboard.ID_Not_Found')]);
            DB::beginTransaction();
            $item_data = [
                'ar' => ['name' => $request->name_ar, 'slug' => slug_ar($request->name_ar), 'details' => $request->details_ar],
                'en' => ['name' => $request->name_en, 'slug' => Str::slug($request->name_en), 'details' => $request->details_en],
            ];

            if ($request->has('image')) {
                $imagename = $imageService->uploadAndProcess(
                    image: $request->file('image'),
                    folder: $this->folder,
                    thumbnailWidth: 250,
                    thumbnailHeight: 250,
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
        $item = Onboarding::find($id);
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
            Onboarding::whereIn('id', $ids)->delete();
            return response()->json(['success' => __('dashboard.Delete_success')]);
        } catch (\Exception $ex) {
            DB::rollback();
            return response()->json(['error' => __('dashboard.Error_DB')]);
        }
    }
}
