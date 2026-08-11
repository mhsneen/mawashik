<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\{User, Wallet};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Services\ImageUploadService;
use Spatie\Permission\Models\Role;
use App\Http\Requests\Dashboard\DriverRequest;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\DriversExport;
class DriverController extends Controller
{
    protected  $folder = "users";
    protected  $view = "drivers";
    protected  $routeName = "drivers";
    protected  $namesection =  'dashboard.Drivers';

    public function __construct()
    {
        // Assuming there is a permission called 'Drivers', or you can change it if you have a specific one
        // $this->middleware('permission:Drivers');
    }

      public function index(Request $request)
    {
        // ✅ جلب is_blocked بجانب is_active وإضافة فلتر البحث الخاص به
        $items = User::Drivers()
                    ->FilterDashbordDrivers(request(['search_name', 'search_mobile', 'search_active', 'search_is_blocked']))
                    ->select('id', 'name', 'mobile', 'is_active', 'is_blocked', 'created_at')
                    ->latest('id')
                    ->paginate(PAGANATION_COUNT)
                    ->withQueryString();

        $data = [
            'items' => $items,
            'routeName' => $this->routeName,
            'namesection' => $this->namesection,
            'request_search' => $request->all()
        ];
        return view('dashboard.' . $this->view . '.index', $data);
    }

    public function create()
    {
        $data = [
            'routeName' => $this->routeName,
            'folder' => $this->folder,
            'namesection' => $this->namesection
        ];
        return view('dashboard.' . $this->view . '.create', $data);
    }

    public function store(DriverRequest $request, ImageUploadService $imageService)
    {


        try {
            DB::beginTransaction();

            $item_data = [
                'name' => $request->name,
                'is_active' => $request->is_active ?? 1,
                'mobile' => $request->mobile,
                'email' => $request->email,
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

            $driverRole = Role::firstOrCreate(['name' => 'driver', 'guard_name' => 'web']);
            $item->assignRole($driverRole);



            DB::commit();
            return redirect()->route($this->routeName)->with(['success' => __('dashboard.Add_success')]);
        } catch (Exception $ex) {
            DB::rollback();
            return redirect()->back()->with(['error' => __('dashboard.Error_DB')]);
        }
    }


    public function edit($id)
    {
        $item = User::Drivers()->find($id);
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

    public function update(DriverRequest $request, $id, ImageUploadService $imageService)
    {


        try {
            $item = User::Drivers()->find($id);
            if (!$item)
                return redirect()->route($this->routeName)->with(['error' => __('dashboard.ID_Not_Found')]);

            DB::beginTransaction();

            $item_data = [
                'name' => $request->name,
                'is_active' => $request->is_active ?? 1,
                'mobile' => $request->mobile,
                'email' => $request->email,
            ];

            if ($request->hasFile('image')) {
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

            DB::commit();
            return redirect()->route($this->routeName . '.edit', $id)->with(['success' => __('dashboard.Edit_success')]);
        } catch (Exception $ex) {
            DB::rollback();
            return redirect()->back()->with(['error' => __('dashboard.Error_DB')]);
        }
    }


    public function changepassword(Request $request, $id)
    {
        $item = User::Drivers()->find($id);
        if (!$item)
            return redirect()->route($this->routeName)->with(['error' => __('dashboard.ID_Not_Found')]);

        $rules = [
            'password' => 'required|string|min:6',
            'password-confirmation' => 'required|same:password',
        ];
        $attributes = [
            'password' => __('dashboard.New_Password'),
            'password-confirmation' => __('dashboard.Confirm_Password'),
        ];

        Validator::make($request->all(), $rules, [], $attributes)->validateWithBag("changepassbag_{$id}");

        try {
            DB::beginTransaction();
            $item->update(['password' => Hash::make($request->password)]);
            DB::commit();
            return redirect()->route($this->routeName)->with(['success' => __('dashboard.Edit_success')]);
        } catch (Exception $ex) {
            DB::rollback();
            return redirect()->back()->with(['error' => __('dashboard.Error_DB')]);
        }
    }

    public function ChangeActive(Request $request, $id)
    {
        $item = User::Drivers()->find($id);
        if (!$item)
            return redirect()->route($this->routeName)->with(['error' => __('dashboard.ID_Not_Found')]);

        $rules = [
            'is_active' => 'required|in:0,1'
        ];
        $attributes = [
            'is_active' => __('dashboard.Efficiency_Status'),
        ];

        Validator::make($request->all(), $rules, [], $attributes)->validateWithBag("changeactivebag_{$id}");

        try {
            DB::beginTransaction();
            $item->update(['is_active' => $request->is_active]);
            DB::commit();
            return redirect()->route($this->routeName)->with(['success' => __('dashboard.Edit_success')]);
        } catch (Exception $ex) {
            DB::rollback();
            return redirect()->back()->with(['error' => __('dashboard.Error_DB')]);
        }
    }


     public function ChangeBlock(Request $request, $id)
    {
        $item = User::Drivers()->find($id);
        if (!$item)
            return redirect()->route($this->routeName)->with(['error' => __('dashboard.ID_Not_Found')]);

        $rules = [
            'is_blocked' => 'required|in:0,1'
        ];
        $attributes = [
            'is_blocked' => __('dashboard.Block_Status'),
        ];

        $validator = Validator::make($request->all(), $rules, [], $attributes)->validateWithBag("changeblockbag_{$id}");

        try {
            $item = User::Drivers()->find($id);
            if (!$item)
                return redirect()->route($this->routeName)->with(['error' => __('dashboard.ID_Not_Found')]);

            DB::beginTransaction();
            $item_data = [
                'is_blocked' => $request->is_blocked,
            ];
            $item->update($item_data);
            DB::commit();
            return redirect()->route($this->routeName)->with(['success' => __('dashboard.Edit_success')]);
        } catch (Exception $ex) {
            DB::rollback();
            return redirect()->back()->with(['error' => __('dashboard.Error_DB')]);
        }
    }


    public function destroy($id)
    {
        try {
            $item = User::Drivers()->find($id);
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
            User::Drivers()->whereIn('id', $ids)->each(function ($item, $key) {
                $item->delete();
            });
            DB::commit();
            return response()->json(['success' => __('dashboard.Delete_success')]);
        } catch (Exception $ex) {
            DB::rollback();
            return response()->json(['error' => __('dashboard.Error_DB')]);
        }
    }



    public function show($id)
    {
        $item = User::Drivers()
            ->withCount(['driverOrders as orders_count'])
            ->find($id);

        if (!$item)
            return redirect()->route($this->routeName)->with(['error' => __('dashboard.ID_Not_Found')]);

        // الطلبيات المعيّنة لهذا السائق (لعرضها بتبويب Orders بالصفحة)
        $item->setRelation('orders', $item->driverOrders()->with('status:id')->latest('id')->get());

        // ✅ إحصائيات المحفظة والأرباح (نفس منطق واجهة الـ API الخاصة بالسائق)
        $wallet = Wallet::firstOrCreate(
            ['user_id' => $item->id],
            ['balance' => 0, 'rolename' => 'driver']
        );

        $completedOrdersQuery = $item->driverOrders()->where('status_id', 5);

        $todayCompletedOrders = (clone $completedOrdersQuery)->whereDate('delivered_at', today())->count();
        $totalCompletedOrders = (clone $completedOrdersQuery)->count();
        $cashCollected = (clone $completedOrdersQuery)->where('payment_method', 'cash')->sum('final_price');
        $onlineEarnings = (clone $completedOrdersQuery)->whereIn('payment_method', ['online', 'wallet'])->sum('delivery_price');
        $totalEarnings = (clone $completedOrdersQuery)->sum('delivery_price');

        // تقييمات السائق ومتوسطها
        $reviews = $item->driverReviews()->with('client:id,name')->latest('id')->get();
        $averageRating = $item->average_rating; // null إذا ماله تقييمات لسا
        $reviewsCount = $reviews->count();

        $data = [
            'item'                      => $item,
            'routeName'                 => $this->routeName,
            'namesection'               => $this->namesection,
            'walletBalance'             => (float) $wallet->balance,
            'todayCompletedOrders'      => $todayCompletedOrders,
            'totalCompletedOrders'      => $totalCompletedOrders,
            'cashCollected'             => (float) $cashCollected,
            'onlineEarnings'            => (float) $onlineEarnings,
            'totalEarnings'             => (float) $totalEarnings,
            'reviews'                   => $reviews,
            'averageRating'             => $averageRating,
            'reviewsCount'              => $reviewsCount,
        ];
        return view('dashboard.' . $this->view . '.show', $data);
    }

    public function Export_Excel(Request $request)
    {

        return Excel::download(new DriversExport($request), 'drivers.xlsx');
    }
}
