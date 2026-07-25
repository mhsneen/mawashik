<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\File;
use App\Http\Requests\Dashboard\EmployeeRequest;
use App\Http\Requests\Dashboard\ChangePasswordClientRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ClientsExport;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use App\Services\ImageUploadService;
use Spatie\Permission\Models\Permission;

class EmployeesController extends Controller
{

    protected  $folder = "users";
    protected  $view = "employees";
    protected  $routeName = "employees";
    protected  $namesection =  'dashboard.Employees';

    public function __construct()
    {
        $this->middleware('role:admin');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $items = User::Employees()->select('id', 'name', 'mobile', 'is_active', 'created_at', 'email')->latest('id')->paginate(PAGANATION_COUNT);

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
        $permissions = Permission::where('guard_name', 'admin')->get();
        $data = [
            'routeName' => $this->routeName,
            'folder' => $this->folder,
            'namesection' => $this->namesection,
            'permissions' => $permissions
        ];
        return view('dashboard.' . $this->view . '.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(EmployeeRequest $request, ImageUploadService $imageService)
    {


        DB::beginTransaction();

        $item_data = [
            'name' => $request->name,
            'email' => $request->email,
            'is_active' => $request->is_active,
            'mobile' => $request->mobile,
            'password' => Hash::make($request->password),
        ];

        if ($request->hasFile('image')) {
            $imagename = $imageService->uploadAndProcess(
                image: $request->file('image'),
                folder: $this->folder,
                thumbnailWidth: 128,
                thumbnailHeight: 128
            );
            $item_data['image'] = $imagename;
        }

        $item = User::create($item_data);
        $item->save();

        $item->assignRole('employee');

        $item->syncPermissions($request->permissions);



        DB::commit();
        return redirect()->route($this->routeName)->with(['success' => __('dashboard.Add_success')]);
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {}

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $item = User::find($id);
        if (!$item)
            return redirect()->route($this->routeName)->with(['error' => __('dashboard.ID_Not_Found')]);
        $permissions = Permission::where('guard_name', 'admin')->get();
        $data = [
            'item' => $item,
            'routeName' => $this->routeName,
            'folder' => $this->folder,
            'namesection' => $this->namesection,
            'permissions' => $permissions
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
    public function update(EmployeeRequest $request, $id, ImageUploadService $imageService)
    {

        $item = User::find($id);
        if (!$item)
            return redirect()->route($this->routeName)->with(['error' => __('dashboard.ID_Not_Found')]);

        DB::beginTransaction();


        $item_data = [
            'name' => $request->name,
            'email' => $request->email,
            'is_active' => $request->is_active,
            'mobile' => $request->mobile
        ];

        if ($request->has('image')) {
            $imagename = $imageService->uploadAndProcess(
                image: $request->file('image'),
                folder: $this->folder,
                thumbnailWidth: 128,
                thumbnailHeight: 128,
                oldFilename: $item->image
            );
            $item_data['image'] =  $imagename;
        }


        $item->update($item_data);
        $item->syncPermissions($request->permissions);
        DB::commit();
        return redirect()->route($this->routeName . '.edit', $id)->with(['success' => __('dashboard.Edit_success')]);
    }


    public function changepassword(Request $request, $id)
    {
        $item = User::find($id);
        if (!$item)
            return redirect()->route($this->routeName)->with(['error' => __('dashboard.ID_Not_Found')]);

        $rules = [
            'password' => 'required|string|min:6',
            'password-confirmation' => 'required|same:password',
        ];
        $messages = [];
        $attributes = [
            'password' => __('dashboard.New_Password'),
            'password-confirmation' => __('dashboard.Confirm_Password'),
        ];



        $validator = Validator::make($request->all(), $rules, $messages, $attributes)->validateWithBag("changepassbag_{$id}");


        try {
            $item = User::find($id);
            if (!$item)
                return redirect()->route($this->routeName)->with(['error' => __('dashboard.ID_Not_Found')]);
            DB::beginTransaction();
            $item_data = [
                'password' => Hash::make($request->password),
            ];
            $item->update($item_data);
            DB::commit();
            return redirect()->route($this->routeName)->with(['success' => __('dashboard.Edit_success')]);
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
            $item = User::find($id);
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
            User::find($ids)->each(function ($item, $key) {
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
