<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\{User, Commission, Order};
use App\Services\CommissionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * CommissionsController — إدارة عمولات التجار والمندوبين.
 *
 * الصلاحيات:
 *  - Trader_Commissions: عمولات التجار
 *  - Driver_Commissions: عمولات المندوبين
 *  - Reports_Commissions: تقارير العمولات
 */
class CommissionsController extends Controller
{
    public function __construct(protected CommissionService $commissionService) {}

    /**
     * قائمة العمولات لكل المستخدمين (تجار + مندوبين) مع فلترة.
     */
    public function index(Request $request)
    {
        $commissions = Commission::with(['user:id,name,mobile', 'setBy:id,name'])
            ->when($request->filled('role'), fn($q) => $q->where('role', $request->role))
            ->when($request->filled('user_id'), fn($q) => $q->where('user_id', $request->user_id))
            ->when($request->filled('search_name'), fn($q) =>
                $q->whereHas('user', fn($u) => $u->where('name', 'like', '%' . $request->search_name . '%')))
            ->orderBy('id', 'desc')
            ->paginate(15)
            ->withQueryString();

        // ✅ قوائم التجار والمندوبين للاختيار من dropdown بدل إدخال user_id يدوياً
        $traders = User::traders()->select('id', 'name', 'mobile', 'trader_status')
            ->orderBy('name')->get();
        $drivers = User::Drivers()->select('id', 'name', 'mobile', 'driver_status')
            ->orderBy('name')->get();

        // إعدادات العمولات الافتراضية
        $defaults = [
            'commission_active'         => \App\Models\Setting::where('key', 'commission_active')->value('plain_value'),
            'commission_trader_default' => \App\Models\Setting::where('key', 'commission_trader_default')->value('plain_value'),
            'commission_driver_default' => \App\Models\Setting::where('key', 'commission_driver_default')->value('plain_value'),
        ];

        return view('dashboard.commissions.index', compact('commissions', 'defaults', 'traders', 'drivers'));
    }

    /**
     * تحديث الإعدادات الافتراضية للعمولات.
     */
    public function updateDefaults(Request $request)
    {
        $request->validate([
            'commission_trader_default' => 'required|numeric|min:0|max:100',
            'commission_driver_default' => 'required|numeric|min:0|max:100',
            'commission_active'         => 'nullable|boolean',
        ]);

        \App\Models\Setting::setMany([
            'commission_trader_default' => $request->commission_trader_default,
            'commission_driver_default' => $request->commission_driver_default,
            'commission_active'         => $request->has('commission_active') ? '1' : '0',
        ]);

        return redirect()->back()->with('success', __('dashboard.Commission_Defaults_Updated'));
    }

    /**
     * تقرير العمولات المالية (للطلبات المكتملة).
     */
    public function report(Request $request)
    {
        $query = Order::where('status_id', 5)
            ->with(['trader:id,name', 'driver:id,name'])
            ->when($request->filled('date_from'), fn($q) =>
                $q->whereBetween('created_at', [$request->date_from, $request->date_to ?? now()]))
            ->when($request->filled('trader_id'), fn($q) => $q->where('trader_id', $request->trader_id))
            ->when($request->filled('driver_id'), fn($q) => $q->where('driver_id', $request->driver_id))
            ->orderBy('id', 'desc');

        $orders = $query->paginate(20)->withQueryString();

        // إجماليات
        $totals = (object) [
            'commission_trader_total' => (clone $query)->sum('commission_trader'),
            'commission_driver_total'  => (clone $query)->sum('commission_driver'),
            'orders_count'             => (clone $query)->count(),
            'final_price_total'         => (clone $query)->sum('final_price'),
        ];

        return view('dashboard.commissions.report', compact('orders', 'totals'));
    }

    /**
     * تعيين نسبة عمولة لمستخدم من شاشة العمولات.
     */
    public function setRate(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'role'    => 'required|in:trader,driver',
            'rate'    => 'required|numeric|min:0|max:100',
            'note'    => 'nullable|string|max:500',
        ]);

        $user = User::findOrFail($request->user_id);
        $this->commissionService->setRate(
            $user, $request->role, $request->rate, $request->note, auth()->id()
        );

        return redirect()->back()->with('success', __('dashboard.Commission_Rate_Set_Success'));
    }
}
