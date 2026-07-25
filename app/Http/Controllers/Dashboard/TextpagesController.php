<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Dashboard\TextpageRequest;
use App\Models\Textpage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Exception;

class TextpagesController extends Controller
{



    public function __construct()
    {
        $this->middleware('permission:TextPages');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $textpages = Textpage::orderBy('id', 'DESC')->paginate(PAGANATION_COUNT);
        return view('dashboard.textpages.index', compact('textpages'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('dashboard.textpages.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(TextpageRequest $request)
    {
        DB::beginTransaction();
        $textpage_data = [
            'ar' => ['name' => $request->name_ar, 'slug' => slug_ar($request->name_ar), 'details' => $request->details_ar],
            'en' => ['name' => $request->name_en, 'slug' => Str::slug($request->name_en), 'details' => $request->details_en],
        ];

        $textpage = Textpage::create($textpage_data);
        $textpage->save();
        DB::commit();
        return redirect()->route('textpages')->with(['success' => __('dashboard.Add_success')]);
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
        $textpage = Textpage::find($id);
        if (!$textpage)
            return redirect()->route('textpages')->with(['error' => __('dashboard.ID_Not_Found')]);
        return view('dashboard.textpages.edit', compact(['textpage']));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(TextpageRequest $request, $id)
    {
        try {
            $textpage = Textpage::find($id);
            if (!$textpage)
                return redirect()->route('textpages')->with(['error' => __('dashboard.ID_Not_Found')]);

            DB::beginTransaction();
            $textpage_data = [
                'ar' => ['name' => $request->name_ar, 'slug' => slug_ar($request->name_ar), 'details' => $request->details_ar],
                'en' => ['name' => $request->name_en, 'slug' => Str::slug($request->name_en), 'details' => $request->details_en],
            ];

            $textpage->update($textpage_data);

            DB::commit();
            return redirect()->route('textpages.edit', $id)->with(['success' => __('dashboard.Edit_success')]);
        } catch (Exception $ex) {
            DB::rollback();
            return redirect()->back()->with(['error' => __('dashboard.Error_DB')]);
        }
    }



    public function edittext($slug)
    {
        $textpage = Textpage::WhereTranslation('slug', $slug)->first();

        if (!$textpage)
            return redirect()->route('admin.dashboard')->with(['error' => __('dashboard.ID_Not_Found')]);
        return view('dashboard.textpages.edittext', compact(['textpage']));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updatetext(Request $request, $id)
    {
        try {
            $textpage = Textpage::find($id);
            if (!$textpage)
                return redirect()->route('textpages')->with(['error' => __('dashboard.ID_Not_Found')]);
            DB::beginTransaction();



            $textpage_data = [
                'ar' => ['details' => $request->details_ar],
                'en' => ['details' => $request->details_en],
            ];

            $textpage->update($textpage_data);

            DB::commit();
            return redirect()->route('textpages.edittext', $textpage->slug)->with(['success' => __('dashboard.Edit_success')]);
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
            $textpage = Textpage::find($id);
            if (!$textpage)
                return redirect()->route('textpages')->with(['error' => __('dashboard.ID_Not_Found')]);
            DB::beginTransaction();
            $textpage->delete();
            DB::commit();
            return redirect()->route('textpages')->with(['success' => __('dashboard.Delete_success')]);
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
            Textpage::find($ids)->each(function ($textpage, $key) {
                $textpage->delete();
            });
            DB::commit();
            return response()->json(['success' => __('dashboard.Delete_success')]);
        } catch (Exception $ex) {
            DB::rollback();
            return response()->json(['error' => __('dashboard.Error_DB')]);
        }
    }
}
