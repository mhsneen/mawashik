<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\{Setting, Order};
use Illuminate\Http\Request;

class SlaughtersController extends Controller
{
    /**
     * عرض صفحة إعدادات خدمة الذبح.
     */
    public function settings()
    {
        $slaughter_active = Setting::where('key', 'slaughter_active')->value('plain_value');
        $slaughter_default_price = Setting::where('key', 'slaughter_default_price')->value('plain_value');

        return view('dashboard.slaughters.settings', compact('slaughter_active', 'slaughter_default_price'));
    }

    /**
     * تحديث إعدادات خدمة الذبح.
     * ✅ تم تعديلها لتقبل قيمة واحدة فقط (بحد أدنى وحد أقصى)
     */
    public function updateSettings(Request $request)
    {
        $request->validate([
            'slaughter_active' => 'nullable|boolean',
            'slaughter_default_price' => 'required|numeric|min:0',
        ]);

        Setting::setMany([
            'slaughter_active' => $request->has('slaughter_active') ? '1' : '0',
            'slaughter_default_price' => $request->slaughter_default_price,
        ]);

        return redirect()->back()->with('success', __('dashboard.Slaughter_Settings_Updated'));
    }

    /**
     * عرض تقرير خدمة الذبح.
     */
    public function report(Request $request)
    {
        $query = Order::where('status_id', 5)
            ->where('slaughter_total', '>', 0)
            ->with(['user:id,name', 'trader:id,name'])
            ->latest('id');

        $orders = $query->paginate(PAGANATION_COUNT)->withQueryString();

        $totals = (object) [
            'products_count' => (clone $query)->count(),
            'orders_count'   => (clone $query)->count(),
            'slaughter_total_sum' => (clone $query)->sum('slaughter_total'),
        ];

        return view('dashboard.slaughters.report', compact('orders', 'totals'));
    }
}
