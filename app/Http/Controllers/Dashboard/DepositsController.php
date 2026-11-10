<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\{Order, Setting};
use Illuminate\Http\Request;

/**
 * DepositsController — إدارة إعدادات العربون + تقرير العربون.
 *
 * الصلاحيات:
 *  - Deposits_Settings: إعدادات العربون
 *  - Reports_Deposits: تقارير العربون
 */
class DepositsController extends Controller
{
    /**
     * إعدادات العربون (نسبة، حدود، سياسة الاسترداد).
     */
    public function settings()
    {
        $settings = Setting::whereIn('key', [
            'deposit_active', 'deposit_percentage', 'deposit_min_amount', 'deposit_max_amount',
            'deposit_refund_before', 'deposit_forfeit_after',
        ])->get()->keyBy('key');

        return view('dashboard.deposits.settings', compact('settings'));
    }

    public function updateSettings(Request $request)
    {
        $request->validate([
            'deposit_active'         => 'nullable|boolean',
            'deposit_percentage'    => 'required|numeric|min:0|max:100',
            'deposit_min_amount'    => 'nullable|numeric|min:0',
            'deposit_max_amount'    => 'nullable|numeric|min:0',
            'deposit_refund_before' => 'required|in:anytime,preparing,never',
            'deposit_forfeit_after' => 'nullable|in:preparing,never',
        ]);

        Setting::setMany([
            'deposit_active'         => $request->has('deposit_active') ? '1' : '0',
            'deposit_percentage'    => $request->deposit_percentage,
            'deposit_min_amount'    => $request->deposit_min_amount ?? 0,
            'deposit_max_amount'    => $request->deposit_max_amount ?? 0,
            'deposit_refund_before' => $request->deposit_refund_before,
            'deposit_forfeit_after' => $request->deposit_forfeit_after ?? 'preparing',
        ]);

        return redirect()->back()->with('success', __('dashboard.Deposit_Settings_Updated'));
    }

    /**
     * تقرير العربون: حركات العربون على الطلبات.
     */
    public function report(Request $request)
    {
        $query = Order::with(['user:id,name,mobile', 'trader:id,name'])
            ->where('deposit_amount', '>', 0)
            ->when($request->filled('date_from'), fn($q) =>
                $q->whereBetween('created_at', [$request->date_from, $request->date_to ?? now()]))
            ->when($request->filled('deposit_status'), fn($q) =>
                $q->where('deposit_status', $request->deposit_status))
            ->when($request->filled('trader_id'), fn($q) => $q->where('trader_id', $request->trader_id))
            ->orderBy('id', 'desc');

        $orders = $query->paginate(20)->withQueryString();

        $totals = (object) [
            'total_deposit'     => (clone $query)->sum('deposit_amount'),
            'refunded'          => (clone $query)->where('deposit_status', 'refunded')->sum('deposit_amount'),
            'partial_refund'    => (clone $query)->where('deposit_status', 'partial_refund')->sum('deposit_amount'),
            'released'          => (clone $query)->where('deposit_status', 'released')->sum('deposit_amount'),
            'pending'           => (clone $query)->where('deposit_status', 'paid')->sum('deposit_amount'),
        ];

        return view('dashboard.deposits.report', compact('orders', 'totals'));
    }
}
