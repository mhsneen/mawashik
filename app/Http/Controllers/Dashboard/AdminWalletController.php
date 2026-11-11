<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\{Order, WalletTransaction};
use Illuminate\Http\Request;

/**
 * AdminWalletController — محفظة الإدارة (المنصة).
 *
 * لا توجد محفظة فعلية للمنصة: أرصدتها مشتقة من الطلبات وحركات المحافظ:
 *  - عربون محتجز: طلبات deposit_status = paid (لم يُسوَّ بعد).
 *  - عربون مصادَر: deposit_status = forfeited (إيراد للمنصة).
 *  - عمولات: commission_trader + commission_driver للطلبات المسلَّمة (status 5).
 */
class AdminWalletController extends Controller
{
    private const TYPES = [
        'deposit_paid', 'deposit_refunded', 'deposit_forfeited', 'deposit_released',
        'settlement', 'commission_trader', 'commission_driver', 'admin_deposit', 'admin_deduction',
    ];

    public function index(Request $request)
    {
        $delivered = Order::where('status_id', 5)
            ->when($request->filled('date_from'), fn($q) => $q->whereDate('delivered_at', '>=', $request->date_from))
            ->when($request->filled('date_to'), fn($q) => $q->whereDate('delivered_at', '<=', $request->date_to));

        $commissionTrader = (float) (clone $delivered)->sum('commission_trader');
        $commissionDriver = (float) (clone $delivered)->sum('commission_driver');
        $forfeited = (float) Order::where('deposit_status', 'forfeited')->sum('deposit_amount');

        $summary = (object) [
            'held_deposits'     => (float) Order::where('deposit_status', 'paid')->sum('deposit_amount'),
            'forfeited'         => $forfeited,
            'commission_trader' => $commissionTrader,
            'commission_driver' => $commissionDriver,
            'total_revenue'     => $commissionTrader + $commissionDriver + $forfeited,
        ];

        $transactions = WalletTransaction::with(['wallet.user:id,name', 'order:id,order_number'])
            ->whereIn('transaction_type', self::TYPES)
            ->when($request->filled('transaction_type'), fn($q) => $q->where('transaction_type', $request->transaction_type))
            ->when($request->filled('date_from'), fn($q) => $q->whereDate('created_at', '>=', $request->date_from))
            ->when($request->filled('date_to'), fn($q) => $q->whereDate('created_at', '<=', $request->date_to))
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        $types = self::TYPES;

        return view('dashboard.adminwallet.index', compact('summary', 'transactions', 'types'));
    }
}
