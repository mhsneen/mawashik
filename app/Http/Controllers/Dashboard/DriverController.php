<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\{User, Wallet, AdminNotification};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Services\{ImageUploadService, FCMServices, AdminNotifier};
use Spatie\Permission\Models\Role;
use App\Http\Requests\Dashboard\DriverRequest;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\DriversExport;

/**
 * DriverController — إدارة مندوبي التوصيل (السائقين) في لوحة التحكم.
 *
 * التغييرات وفق البند الرابع من مواصفات "وسيط المواشي":
 *  - store/update: حفظ صورة الهوية + صورة الرخصة + الموافقة على الشروط + حالة الاعتماد + نسبة العمولة.
 *  - approve: اعتماد السائق (driver_status = approved) + تفعيل الحساب + إشعار.
 *  - reject: رفض السائق مع سبب.
 *  - suspend: إيقاف مؤقت.
 *  - reactivate: إعادة تفعيل.
 *  - updateCommission: تعديل نسبة عمولة السائق.
 *  - pending: قائمة السائقين بانتظار الاعتماد.
 */
class DriverController extends Controller
{
    protected $folder = "users";
    protected $view   = "drivers";
    protected $routeName = "drivers";
    protected $namesection = 'dashboard.Drivers';

    public function __construct(
        protected FCMServices $fcm
    ) {}

    /**
     * قائمة جميع السائقين مع الفلاتر.
     */
    public function index(Request $request)
    {
        $items = User::Drivers()
            ->FilterDashbordDrivers(request(['search_name', 'search_mobile', 'search_active', 'search_is_blocked']))
            ->select('id', 'name', 'mobile', 'is_active', 'is_blocked', 'driver_status', 'driver_commission_rate', 'created_at')
            ->latest('id')
            ->paginate(PAGANATION_COUNT)
            ->withQueryString();

        $data = [
            'items'        => $items,
            'routeName'    => $this->routeName,
            'namesection'  => $this->namesection,
            'request_search' => $request->all()
        ];
        return view('dashboard.' . $this->view . '.index', $data);
    }

    /**
     * قائمة السائقين بانتظار الاعتماد (الحالة pending).
     */
    public function pending(Request $request)
    {
        $items = User::pendingDrivers()
            ->select('id', 'name','image', 'mobile', 'is_active', 'driver_status', 'created_at','id_image','license_image')
            ->oldest('id')
            ->paginate(15)
            ->withQueryString();

        return view('dashboard.drivers.pending', [
            'items'       => $items,
            'routeName'   => $this->routeName,
            'namesection' => $this->namesection,
        ]);
    }

    /**
     * نموذج إنشاء سائق جديد.
     */
    public function create()
    {
        return view('dashboard.' . $this->view . '.create', [
            'routeName'   => $this->routeName,
            'folder'      => $this->folder,
            'namesection' => $this->namesection,
        ]);
    }

    /**
     * حفظ سائق جديد (يشمل رفع صورة الهوية + الرخصة + إعداد حالة الاعتماد).
     */
    public function store(DriverRequest $request, ImageUploadService $imageService)
    {
        try {
            DB::beginTransaction();

            $item_data = [
                'name'        => $request->name,
                'is_active'   => $request->is_active ?? 1,
                'mobile'      => $request->mobile,
                'email'       => $request->email,
              //  'password'    => Hash::make($request->password),
                // === حقول مندوب التوصيل (PDF — البند الرابع) ===
                'terms_accepted'         => $request->boolean('terms_accepted'),
                'driver_status'          => $request->driver_status ?? 'pending',
                'driver_commission_rate'  => $request->driver_commission_rate,
            ];

            // الصورة الشخصية
            if ($request->hasFile('image')) {
                $item_data['image'] = $imageService->uploadAndProcess(
                    image: $request->file('image'),
                    folder: $this->folder,
                    thumbnailWidth: 128,
                    thumbnailHeight: 128
                );
            }

            // صورة الهوية
            if ($request->hasFile('id_image')) {
                $item_data['id_image'] = $imageService->uploadAndProcess(
                    image: $request->file('id_image'),
                    folder: $this->folder,
                    thumbnailWidth: 300,
                    thumbnailHeight: 200
                );
            }

            // صورة الرخصة
            if ($request->hasFile('license_image')) {
                $item_data['license_image'] = $imageService->uploadAndProcess(
                    image: $request->file('license_image'),
                    folder: $this->folder,
                    thumbnailWidth: 300,
                    thumbnailHeight: 200
                );
            }

            $item = User::create($item_data);

            // إسناد دور السائق
            $driverRole = Role::firstOrCreate(['name' => 'driver', 'guard_name' => 'web']);
            $item->assignRole($driverRole);

            // إنشاء محفظة فارغة للسائق
            Wallet::firstOrCreate(
                ['user_id' => $item->id],
                ['balance' => 0, 'rolename' => 'driver']
            );

            // إشعار الأدمن بوجود سائق جديد بانتظار الاعتماد
            AdminNotifier::newDriverPendingApproval($item);

            DB::commit();
            return redirect()->route($this->routeName)
                ->with(['success' => __('dashboard.Add_success')]);
        } catch (Exception $ex) {
            DB::rollback();
            Log::error("Driver store failed: " . $ex->getMessage());
            return redirect()->back()
                ->with(['error' => __('dashboard.Error_DB')])
                ->withInput();
        }
    }

    /**
     * نموذج تعديل سائق.
     */
    public function edit($id)
    {
        $item = User::Drivers()->find($id);
        if (!$item) {
            return redirect()->route($this->routeName)
                ->with(['error' => __('dashboard.ID_Not_Found')]);
        }

        return view('dashboard.' . $this->view . '.edit', [
            'item'        => $item,
            'routeName'   => $this->routeName,
            'folder'      => $this->folder,
            'namesection' => $this->namesection,
        ]);
    }

    /**
     * تحديث بيانات السائق (يشمل رفع صورة هوية/رخصة جديدة + تعديل حالة الاعتماد والعمولة).
     */
    public function update(DriverRequest $request, $id, ImageUploadService $imageService)
    {
        try {
            $item = User::Drivers()->find($id);
            if (!$item) {
                return redirect()->route($this->routeName)
                    ->with(['error' => __('dashboard.ID_Not_Found')]);
            }

            DB::beginTransaction();

            $item_data = [
                'name'      => $request->name,
                'is_active' => $request->is_active ?? 1,
                'mobile'    => $request->mobile,
                'email'     => $request->email,

                // === حقول مندوب التوصيل (قابلة للتعديل من الأدمن) ===
                'driver_status'          => $request->driver_status ?? $item->driver_status,
                'driver_status_note'    => $request->driver_status_note,
                'driver_commission_rate' => $request->driver_commission_rate,
                'terms_accepted'         => $request->boolean('terms_accepted', $item->terms_accepted ?? false),
            ];

            // كلمة المرور (اختياري)
            if ($request->filled('password')) {
                $item_data['password'] = Hash::make($request->password);
            }

            // الصورة الشخصية
            if ($request->hasFile('image')) {
                $item_data['image'] = $imageService->uploadAndProcess(
                    image: $request->file('image'),
                    folder: $this->folder,
                    thumbnailWidth: 128,
                    thumbnailHeight: 128,
                    oldFilename: $item->image
                );
            }

            // صورة الهوية
            if ($request->hasFile('id_image')) {
                $item_data['id_image'] = $imageService->uploadAndProcess(
                    image: $request->file('id_image'),
                    folder: $this->folder,
                    thumbnailWidth: 300,
                    thumbnailHeight: 200,
                    oldFilename: $item->id_image
                );
            }

            // صورة الرخصة
            if ($request->hasFile('license_image')) {
                $item_data['license_image'] = $imageService->uploadAndProcess(
                    image: $request->file('license_image'),
                    folder: $this->folder,
                    thumbnailWidth: 300,
                    thumbnailHeight: 200,
                    oldFilename: $item->license_image
                );
            }

            // === إذا تم تغيير الحالة إلى approved/rejected/suspended — نحدّث التواريخ ===
            $newStatus = $item_data['driver_status'] ?? $item->driver_status;
            if ($newStatus === 'approved' && $item->driver_status !== 'approved') {
                $item_data['driver_approved_at']  = now();
                $item_data['driver_rejected_at']  = null;
                $item_data['is_active']           = 1;
            } elseif ($newStatus === 'rejected' && $item->driver_status !== 'rejected') {
                $item_data['driver_rejected_at']  = now();
            } elseif ($newStatus === 'suspended') {
                $item_data['is_active'] = 0;
            }

            $item->update($item_data);

            DB::commit();
            return redirect()->route($this->routeName . '.edit', $id)
                ->with(['success' => __('dashboard.Edit_success')]);
        } catch (Exception $ex) {
            DB::rollback();
            Log::error("Driver update failed: " . $ex->getMessage());
            return redirect()->back()
                ->with(['error' => __('dashboard.Error_DB')])
                ->withInput();
        }
    }

    /**
     * اعتماد السائق — تفعيل الحساب + تسجيل تاريخ الاعتماد + إشعار FCM.
     */
    public function approve(Request $request, $id)
    {
        try {
            $driver = User::Drivers()->findOrFail($id);

            if ($driver->driver_status === 'approved') {
                return redirect()->back()
                    ->with('error', __('dashboard.Driver_Already_Approved'));
            }

            DB::transaction(function () use ($driver) {
                $driver->update([
                    'driver_status'         => 'approved',
                    'driver_approved_at'    => now(),
                    'driver_rejected_at'    => null,
                    'is_active'             => 1,
                ]);

                // إشعار FCM للسائق بقبول الطلب
                try {
                    $this->fcm->sendMsg($driver->id, $driver->device_token, __('dashboard.Driver_Approved_Title'), __('dashboard.Driver_Approved_Body'));
                } catch (\Throwable $e) {
                    Log::warning("FCM driver approve failed: " . $e->getMessage());
                }
            });

            return redirect()->back()
                ->with('success', __('dashboard.Driver_Approved_Success'));
        } catch (Exception $ex) {
            Log::error("Driver approve failed: " . $ex->getMessage());
            return redirect()->back()
                ->with('error', __('dashboard.Error_DB'));
        }
    }

    /**
     * رفض السائق — مع سبب الرفض.
     */
    public function reject(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'driver_status_note' => 'required|string|max:500',
        ], [], [
            'driver_status_note' => __('dashboard.Driver_Status_Note'),
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator, "rejectbag_{$id}")
                ->withInput();
        }

        try {
            $driver = User::Drivers()->findOrFail($id);

            DB::transaction(function () use ($driver, $request) {
                $driver->update([
                    'driver_status'         => 'rejected',
                    'driver_rejected_at'    => now(),
                    'driver_approved_at'    => null,
                    'is_active'             => 0,
                    'driver_status_note'    => $request->driver_status_note,
                ]);

                try {
                    $this->fcm->sendMsg($driver->id, $driver->device_token, __('dashboard.Driver_Rejected_Title'), $request->driver_status_note);
                } catch (\Throwable $e) {
                    Log::warning("FCM driver reject failed: " . $e->getMessage());
                }
            });

            return redirect()->back()
                ->with('success', __('dashboard.Driver_Rejected_Success'));
        } catch (Exception $ex) {
            Log::error("Driver reject failed: " . $ex->getMessage());
            return redirect()->back()
                ->with('error', __('dashboard.Error_DB'));
        }
    }

    /**
     * إيقاف مؤقت للسائق.
     */
    public function suspend(Request $request, $id)
    {
        try {
            $driver = User::Drivers()->findOrFail($id);

            DB::transaction(function () use ($driver, $request) {
                $driver->update([
                    'driver_status'      => 'suspended',
                    'is_active'           => 0,
                    'driver_status_note'  => $request->driver_status_note,
                ]);

                try {
                    $this->fcm->sendMsg($driver->id, $driver->device_token, __('dashboard.Driver_Suspended_Title'), __('dashboard.Driver_Suspended_Body'));
                } catch (\Throwable $e) {
                    Log::warning("FCM driver suspend failed: " . $e->getMessage());
                }
            });

            return redirect()->back()
                ->with('success', __('dashboard.Driver_Suspended_Success'));
        } catch (Exception $ex) {
            Log::error("Driver suspend failed: " . $ex->getMessage());
            return redirect()->back()
                ->with('error', __('dashboard.Error_DB'));
        }
    }

    /**
     * إعادة تفعيل السائق بعد الإيقاف أو الرفض.
     */
    public function reactivate(Request $request, $id)
    {
        try {
            $driver = User::Drivers()->findOrFail($id);

            DB::transaction(function () use ($driver) {
                $driver->update([
                    'driver_status'         => 'approved',
                    'driver_approved_at'    => now(),
                    'driver_rejected_at'    => null,
                    'is_active'             => 1,
                    'driver_status_note'     => null,
                ]);

                try {
                    $this->fcm->sendMsg($driver->id, $driver->device_token, __('dashboard.Driver_Reactivated_Title'), __('dashboard.Driver_Reactivated_Body'));
                } catch (\Throwable $e) {
                    Log::warning("FCM driver reactivate failed: " . $e->getMessage());
                }
            });

            return redirect()->back()
                ->with('success', __('dashboard.Driver_Reactivated_Success'));
        } catch (Exception $ex) {
            Log::error("Driver reactivate failed: " . $ex->getMessage());
            return redirect()->back()
                ->with('error', __('dashboard.Error_DB'));
        }
    }

    /**
     * تعديل نسبة عمولة السائق بشكل مستقل.
     */
    public function updateCommission(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'driver_commission_rate' => 'required|numeric|between:0,100',
        ], [], [
            'driver_commission_rate' => __('dashboard.Driver_Commission_Rate'),
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator, "commissionbag_{$id}")
                ->withInput();
        }

        try {
            $driver = User::Drivers()->findOrFail($id);
            $driver->update([
                'driver_commission_rate' => $request->driver_commission_rate,
            ]);

            return redirect()->back()
                ->with('success', __('dashboard.Driver_Commission_Updated'));
        } catch (Exception $ex) {
            Log::error("Driver commission update failed: " . $ex->getMessage());
            return redirect()->back()
                ->with('error', __('dashboard.Error_DB'));
        }
    }

    /**
     * تغيير كلمة المرور.
     */
    public function changepassword(Request $request, $id)
    {
        $item = User::Drivers()->find($id);
        if (!$item) {
            return redirect()->route($this->routeName)
                ->with(['error' => __('dashboard.ID_Not_Found')]);
        }

        $rules = [
            'password'             => 'required|string|min:6',
            'password-confirmation' => 'required|same:password',
        ];
        $attributes = [
            'password'             => __('dashboard.New_Password'),
            'password-confirmation' => __('dashboard.Confirm_Password'),
        ];

        Validator::make($request->all(), $rules, [], $attributes)
            ->validateWithBag("changepassbag_{$id}");

        try {
            DB::beginTransaction();
            $item->update(['password' => Hash::make($request->password)]);
            DB::commit();
            return redirect()->route($this->routeName)
                ->with(['success' => __('dashboard.Edit_success')]);
        } catch (Exception $ex) {
            DB::rollback();
            return redirect()->back()
                ->with(['error' => __('dashboard.Error_DB')]);
        }
    }

    /**
     * تغيير الحالة الفعالة.
     */
    public function ChangeActive(Request $request, $id)
    {
        $item = User::Drivers()->find($id);
        if (!$item) {
            return redirect()->route($this->routeName)
                ->with(['error' => __('dashboard.ID_Not_Found')]);
        }

        Validator::make($request->all(), [
            'is_active' => 'required|in:0,1',
        ], [], [
            'is_active' => __('dashboard.Efficiency_Status'),
        ])->validateWithBag("changeactivebag_{$id}");

        try {
            DB::beginTransaction();
            $item->update(['is_active' => $request->is_active]);
            DB::commit();
            return redirect()->route($this->routeName)
                ->with(['success' => __('dashboard.Edit_success')]);
        } catch (Exception $ex) {
            DB::rollback();
            return redirect()->back()
                ->with(['error' => __('dashboard.Error_DB')]);
        }
    }

    /**
     * حظر/إلغاء حظر.
     */
    public function ChangeBlock(Request $request, $id)
    {
        $item = User::Drivers()->find($id);
        if (!$item) {
            return redirect()->route($this->routeName)
                ->with(['error' => __('dashboard.ID_Not_Found')]);
        }

        Validator::make($request->all(), [
            'is_blocked' => 'required|in:0,1',
        ], [], [
            'is_blocked' => __('dashboard.Block_Status'),
        ])->validateWithBag("changeblockbag_{$id}");

        try {
            DB::beginTransaction();
            $item->update(['is_blocked' => $request->is_blocked]);
            DB::commit();
            return redirect()->route($this->routeName)
                ->with(['success' => __('dashboard.Edit_success')]);
        } catch (Exception $ex) {
            DB::rollback();
            return redirect()->back()
                ->with(['error' => __('dashboard.Error_DB')]);
        }
    }

    public function destroy($id)
    {
        try {
            $item = User::Drivers()->find($id);
            if (!$item) {
                return redirect()->route($this->routeName)
                    ->with(['error' => __('dashboard.ID_Not_Found')]);
            }
            DB::beginTransaction();
            $item->delete();
            DB::commit();
            return redirect()->route($this->routeName)
                ->with(['success' => __('dashboard.Delete_success')]);
        } catch (Exception $ex) {
            DB::rollback();
            return redirect()->back()
                ->with(['error' => __('dashboard.Error_DB')]);
        }
    }

    public function deleteAll(Request $request)
    {
        try {
            $ids = explode(",", $request->ids);
            DB::beginTransaction();
            User::Drivers()->whereIn('id', $ids)->each(function ($item) {
                $item->delete();
            });
            DB::commit();
            return response()->json(['success' => __('dashboard.Delete_success')]);
        } catch (Exception $ex) {
            DB::rollback();
            return response()->json(['error' => __('dashboard.Error_DB')]);
        }
    }

    /**
     * عرض تفاصيل السائق — يشمل صورة الهوية والرخصة وحالة الاعتماد والعمولة.
     */
    public function show($id)
    {
        $item = User::Drivers()
            ->withCount(['driverOrders as orders_count'])
            ->find($id);

        if (!$item) {
            return redirect()->route($this->routeName)
                ->with(['error' => __('dashboard.ID_Not_Found')]);
        }

        // الطلبيات المعيّنة لهذا السائق
        $item->setRelation('orders', $item->driverOrders()->with('status:id')->latest('id')->get());

        // إحصائيات المحفظة والأرباح
        $wallet = Wallet::firstOrCreate(
            ['user_id' => $item->id],
            ['balance' => 0, 'rolename' => 'driver']
        );

        $completedOrdersQuery = $item->driverOrders()->where('status_id', 5);

        $todayCompletedOrders = (clone $completedOrdersQuery)->whereDate('delivered_at', today())->count();
        $totalCompletedOrders = (clone $completedOrdersQuery)->count();
        $cashCollected        = (clone $completedOrdersQuery)->where('payment_method', 'cash')->sum('final_price');
        $onlineEarnings       = (clone $completedOrdersQuery)->whereIn('payment_method', ['online', 'wallet'])->sum('delivery_price');
        $totalEarnings        = (clone $completedOrdersQuery)->sum('delivery_price');

        // تقييمات السائق
        $reviews        = $item->driverReviews()->with('client:id,name')->latest('id')->get();
        $averageRating  = $item->average_rating;
        $reviewsCount   = $reviews->count();

        return view('dashboard.' . $this->view . '.show', [
            'item'                  => $item,
            'routeName'             => $this->routeName,
            'namesection'           => $this->namesection,
            'walletBalance'         => (float) $wallet->balance,
            'todayCompletedOrders'  => $todayCompletedOrders,
            'totalCompletedOrders'  => $totalCompletedOrders,
            'cashCollected'         => (float) $cashCollected,
            'onlineEarnings'        => (float) $onlineEarnings,
            'totalEarnings'         => (float) $totalEarnings,
            'reviews'               => $reviews,
            'averageRating'         => $averageRating,
            'reviewsCount'          => $reviewsCount,
        ]);
    }

    public function Export_Excel(Request $request)
    {
        return Excel::download(new DriversExport($request), 'drivers.xlsx');
    }
}
