<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\{User, Commission, Wallet, AdminNotification, Country, City, Area, Neighborhood};
use App\Services\{CommissionService, FCMServices, ImageUploadService, AdminNotifier};
use App\Http\Requests\Dashboard\TraderRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;
use Exception;

/**
 * TradersController — إدارة التجار في لوحة التحكم.
 *
 * العمليات:
 *  - index: قائمة التجار مع فلترة (الحالة، الاسم، الجوال، المدينة).
 *  - pending: قائمة الطلبات بانتظار الاعتماد.
 *  - create / store: إنشاء تاجر جديد (مع رفع شعار المتجر + بيانات الموقع + الشروط).
 *  - edit / update: تعديل بيانات التاجر (مع إمكانية تعديل حالة الاعتماد والعمولة).
 *  - destroy: حذف التاجر.
 *  - show: تفاصيل تاجر + إحصائياته.
 *  - approve: اعتماد تاجر + تفعيل حسابه + إشعار FCM.
 *  - reject: رفض تاجر مع سبب.
 *  - suspend: إيقاف مؤقت.
 *  - reactivate: إعادة تفعيل.
 *  - updateCommission: تعديل نسبة العمولة.
 *  - setCommissionRate: تعيين عمولة جديدة عبر سجل Commission.
 */
class TradersController extends Controller
{
    public function __construct(
        protected CommissionService $commissionService,
        protected FCMServices $fcm
    ) {}

    public function index(Request $request)
    {
        $traders = User::traders()
            ->with(['city.translations', 'area.translations'])
            ->when($request->filled('search_name'), fn($q) =>
                $q->where('name', 'like', '%' . $request->search_name . '%'))
            ->when($request->filled('search_mobile'), fn($q) =>
                $q->where('mobile', 'like', '%' . $request->search_mobile . '%'))
            ->when($request->filled('trader_status'), fn($q) =>
                $q->where('trader_status', $request->trader_status))
            ->when($request->filled('city_id'), fn($q) =>
                $q->where('city_id', $request->city_id))
            ->orderBy('id', 'desc')
            ->paginate(15)
            ->withQueryString();

        $cities = City::with('translations')->get();

        return view('dashboard.traders.index', compact('traders', 'cities'));
    }

    public function pending(Request $request)
    {
        $traders = User::pendingTraders()
            ->with(['city.translations'])
            ->orderBy('id', 'asc')
            ->paginate(15);

        return view('dashboard.traders.pending', compact('traders'));
    }

    /**
     * نموذج إنشاء تاجر جديد.
     */
    public function create()
    {
        $countries = Country::with('translations')->get();
        $cities    = City::with('translations')->get();

        return view('dashboard.traders.create', compact('countries', 'cities'));
    }

    /**
     * حفظ تاجر جديد — يشمل بيانات الموقع + الشروط + الإعدادات.
     */
    public function store(TraderRequest $request, ImageUploadService $imageService)
    {
        try {
            DB::beginTransaction();

            $data = [
                'name'                  => $request->name,
                'mobile'                => $request->mobile,
                'email'                 => $request->email,
                'password'              => Hash::make($request->password),
                'is_active'             => $request->is_active ?? 1,

                // === بيانات المتجر ===
                'commercial_register'  => $request->commercial_register,
                'store_lat'             => $request->store_lat,
                'store_lng'             => $request->store_lng,
                'store_address'         => $request->store_address,

                // === الجغرافيا ===
                'country_id'            => $request->country_id,
                'city_id'               => $request->city_id,
                'area_id'               => $request->area_id,
                'neighborhood_id'       => $request->neighborhood_id,

                // === الموافقة على الشروط ===
                'terms_accepted'        => $request->boolean('terms_accepted'),

                // === إعدادات الاعتماد والعمولة ===
                'trader_status'         => $request->trader_status ?? 'pending',
                'admin_status_note'     => $request->admin_status_note,
                'commission_rate'       => $request->commission_rate,
            ];

            // رفع شعار / صورة المتجر
            if ($request->hasFile('image')) {
                $data['image'] = $imageService->uploadAndProcess(
                    image: $request->file('image'),
                    folder: 'users',
                    thumbnailWidth: 128,
                    thumbnailHeight: 128
                );
            }

            $trader = User::create($data);

            // إسناد دور التاجر
            $traderRole = Role::firstOrCreate(['name' => 'trader', 'guard_name' => 'web']);
            $trader->assignRole($traderRole);

            // إنشاء محفظة فارغة للتاجر
            Wallet::firstOrCreate(
                ['user_id' => $trader->id],
                ['balance' => 0, 'rolename' => 'trader']
            );

            // إشعار الأدمن بوجود تاجر جديد بانتظار الاعتماد
            AdminNotifier::newTraderPendingApproval($trader);

            DB::commit();
            return redirect()->route('traders.index')
                ->with('success', __('dashboard.Add_success'));
        } catch (Exception $ex) {
            DB::rollback();
            Log::error('Trader store failed: ' . $ex->getMessage());
            return redirect()->back()
                ->with('error', __('dashboard.Error_DB'))
                ->withInput();
        }
    }

    /**
     * نموذج تعديل التاجر.
     */
    public function edit($id)
    {
        $trader = User::traders()->findOrFail($id);
        $countries = Country::with('translations')->get();
        $cities    = City::with('translations')->get();

        return view('dashboard.traders.edit', compact('trader', 'countries', 'cities'));
    }

    /**
     * تحديث بيانات التاجر.
     */
    public function update(TraderRequest $request, $id, ImageUploadService $imageService)
    {
        try {
            $trader = User::traders()->findOrFail($id);

            DB::beginTransaction();

            $data = [
                'name'                  => $request->name,
                'mobile'                => $request->mobile,
                'email'                 => $request->email,
                'is_active'             => $request->is_active ?? 1,

                // === بيانات المتجر ===
                'commercial_register'  => $request->commercial_register,
                'store_lat'             => $request->store_lat,
                'store_lng'             => $request->store_lng,
                'store_address'         => $request->store_address,

                // === الجغرافيا ===
                'country_id'            => $request->country_id,
                'city_id'               => $request->city_id,
                'area_id'               => $request->area_id,
                'neighborhood_id'       => $request->neighborhood_id,

                // === الموافقة على الشروط ===
                'terms_accepted'        => $request->boolean('terms_accepted', $trader->terms_accepted ?? false),

                // === إعدادات الاعتماد والعمولة ===
                'trader_status'         => $request->trader_status ?? $trader->trader_status,
                'admin_status_note'     => $request->admin_status_note,
                'commission_rate'       => $request->commission_rate,
            ];



            // رفع شعار جديد
            if ($request->hasFile('image')) {
                $data['image'] = $imageService->uploadAndProcess(
                    image: $request->file('image'),
                    folder: 'users',
                    thumbnailWidth: 128,
                    thumbnailHeight: 128,
                    oldFilename: $trader->image
                );
            }

            // === تحديث التواريخ عند تغيير الحالة ===
            $newStatus = $data['trader_status'] ?? $trader->trader_status;
            $justApproved = $newStatus === 'approved' && $trader->trader_status !== 'approved';
            if ($newStatus === 'approved' && $trader->trader_status !== 'approved') {
                $data['approved_at'] = now();
                $data['rejected_at'] = null;
                $data['is_active']    = 1;
            } elseif ($newStatus === 'rejected' && $trader->trader_status !== 'rejected') {
                $data['rejected_at'] = now();
                $data['approved_at'] = null;
                $data['is_active']    = 0;
            } elseif ($newStatus === 'suspended') {
                $data['is_active'] = 0;
            }

            $trader->update($data);

            // الاعتماد من نموذج التعديل أيضاً يرسل إشعار الاعتماد للتاجر (مثل زر "اعتماد")
            if ($justApproved) {
                $this->fcm->sendMsg(
                    $trader->id,
                    $trader->device_token,
                    __('notifications.trader_approved_title'),
                    __('notifications.trader_approved_message'),
                    ['type' => 'trader_approved']
                );
            }

            DB::commit();
            return redirect()->route('traders.edit', $id)
                ->with('success', __('dashboard.Edit_success'));
        } catch (Exception $ex) {
            DB::rollback();
            Log::error('Trader update failed: ' . $ex->getMessage());
            return redirect()->back()
                ->with('error', __('dashboard.Error_DB'))
                ->withInput();
        }
    }

    /**
     * حذف التاجر.
     */
    public function destroy($id)
    {
        try {
            $trader = User::traders()->findOrFail($id);
            DB::beginTransaction();
            $trader->delete();
            DB::commit();
            return redirect()->route('traders.index')
                ->with('success', __('dashboard.Delete_success'));
        } catch (Exception $ex) {
            DB::rollback();
            Log::error('Trader delete failed: ' . $ex->getMessage());
            return redirect()->back()
                ->with('error', __('dashboard.Error_DB'));
        }
    }

    public function show($id)
    {
        $trader = User::traders()->with([
            'city.translations', 'area.translations', 'neighborhood.translations',
            'traderProducts' => fn($q) => $q->withCount('orderItems')->latest()->limit(20),
            'traderOrders' => fn($q) => $q->latest()->limit(10),
            'commissions' => fn($q) => $q->where('role', 'trader')->latest()->limit(10),
        ])->findOrFail($id);

        $stats = [
            'products_count'    => $trader->traderProducts()->count(),
            'approved_count'    => $trader->traderProducts()->where('product_status', 'approved')->count(),
            'pending_count'     => $trader->traderProducts()->where('product_status', 'pending')->count(),
            'orders_count'      => $trader->traderOrders()->count(),
            'delivered_count'   => $trader->traderOrders()->where('status_id', 5)->count(),
            'revenue_total'     => $trader->traderOrders()->where('status_id', 5)->sum('final_price'),
            'commission_total'  => $trader->traderOrders()->where('status_id', 5)->sum('commission_trader'),
            'current_commission_rate' => $this->commissionService->rateForTrader($trader),
        ];

        return view('dashboard.traders.show', compact('trader', 'stats'));
    }

    /**
     * اعتماد تاجر:
     *  - تفعيل الحساب + تسجيل تاريخ الاعتماد.
     *  - إشعار FCM بقبول الطلب.
     */
    public function approve(Request $request, $id)
    {
        try {
            $trader = User::traders()->findOrFail($id);

            if ($trader->trader_status === 'approved') {
                return redirect()->back()->with('error', __('dashboard.Trader_Already_Approved'));
            }

            DB::transaction(function () use ($trader, $request) {
                $trader->update([
                    'trader_status'    => 'approved',
                    'is_active'        => 1,
                    'approved_at'      => now(),
                    'rejected_at'       => null,
                    'admin_status_note' => $request->note,
                ]);

                // إشعار للتاجر
                if ($trader->device_token) {
                    $this->fcm->sendMsg(
                        $trader->id,
                        $trader->device_token,
                        __('notifications.trader_approved_title'),
                        __('notifications.trader_approved_message'),
                        ['type' => 'trader_approved']
                    );
                }
            });

            return redirect()->back()->with('success', __('dashboard.Trader_Approved_Success'));
        } catch (Exception $ex) {
            Log::error('Trader approve: ' . $ex->getMessage());
            return redirect()->back()->with('error', __('dashboard.Api_Error'));
        }
    }

    public function reject(Request $request, $id)
    {
        $request->validate(['note' => 'required|string|max:500']);

        try {
            $trader = User::traders()->findOrFail($id);

            DB::transaction(function () use ($trader, $request) {
                $trader->update([
                    'trader_status'    => 'rejected',
                    'rejected_at'      => now(),
                    'approved_at'       => null,
                    'admin_status_note' => $request->note,
                    'is_active'         => 0,
                ]);

                if ($trader->device_token) {
                    $this->fcm->sendMsg(
                        $trader->id,
                        $trader->device_token,
                        __('notifications.trader_rejected_title'),
                        __('notifications.trader_rejected_message', ['reason' => $request->note]),
                        ['type' => 'trader_rejected']
                    );
                }
            });

            return redirect()->back()->with('success', __('dashboard.Trader_Rejected_Success'));
        } catch (Exception $ex) {
            Log::error('Trader reject: ' . $ex->getMessage());
            return redirect()->back()->with('error', __('dashboard.Api_Error'));
        }
    }

    public function suspend(Request $request, $id)
    {
        $request->validate(['note' => 'required|string|max:500']);

        try {
            $trader = User::traders()->findOrFail($id);

            DB::transaction(function () use ($trader, $request) {
                $trader->update([
                    'trader_status'    => 'suspended',
                    'admin_status_note' => $request->note,
                    'is_active'         => 0,
                ]);

                // إيقاف كل منتجاته تلقائياً
                $trader->traderProducts()->where('product_status', 'approved')
                    ->update(['product_status' => 'suspended']);
            });

            return redirect()->back()->with('success', __('dashboard.Trader_Suspended_Success'));
        } catch (Exception $ex) {
            Log::error('Trader suspend: ' . $ex->getMessage());
            return redirect()->back()->with('error', __('dashboard.Api_Error'));
        }
    }

    public function reactivate(Request $request, $id)
    {
        try {
            $trader = User::traders()->findOrFail($id);

            DB::transaction(function () use ($trader, $request) {
                $trader->update([
                    'trader_status'    => 'approved',
                    'admin_status_note' => $request->note,
                    'is_active'         => 1,
                    'approved_at'       => $trader->approved_at ?? now(),
                    'rejected_at'        => null,
                ]);

                // إعادة تفعيل منتجاته
                $trader->traderProducts()->where('product_status', 'suspended')
                    ->update(['product_status' => 'approved']);
            });

            return redirect()->back()->with('success', __('dashboard.Trader_Reactivated_Success'));
        } catch (Exception $ex) {
            Log::error('Trader reactivate: ' . $ex->getMessage());
            return redirect()->back()->with('error', __('dashboard.Api_Error'));
        }
    }

    /**
     * تعديل نسبة عمولة التاجر (سريع عبر عمود commission_rate).
     */
    public function updateCommission(Request $request, $id)
    {
        $request->validate([
            'commission_rate' => 'required|numeric|min:0|max:100',
        ]);

        try {
            $trader = User::traders()->findOrFail($id);
            $trader->update(['commission_rate' => $request->commission_rate]);

            return redirect()->back()->with('success', __('dashboard.Commission_Updated_Success'));
        } catch (Exception $ex) {
            Log::error('Trader update commission: ' . $ex->getMessage());
            return redirect()->back()->with('error', __('dashboard.Api_Error'));
        }
    }

    /**
     * تعيين عمولة جديدة بسجل Commission (تاريخي) للتاجر.
     */
    public function setCommissionRate(Request $request, $id)
    {
        $request->validate([
            'rate' => 'required|numeric|min:0|max:100',
            'note' => 'nullable|string|max:500',
        ]);

        try {
            $trader = User::traders()->findOrFail($id);
            $this->commissionService->setRate(
                $trader, 'trader', $request->rate, $request->note, auth()->id()
            );

            return redirect()->back()->with('success', __('dashboard.Commission_Rate_Set_Success'));
        } catch (Exception $ex) {
            Log::error('Trader set commission rate: ' . $ex->getMessage());
            return redirect()->back()->with('error', __('dashboard.Api_Error'));
        }
    }
}
