<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;

class WalletClientController extends Controller
{
    protected $routeName = 'walletsclients';
    protected $namesection = 'dashboard.Client_Wallets';

    public function __construct()
    {
        $this->middleware('permission:Clients');
    }

    /**
     * بناء استعلام محافظ العملاء (فقط اللي عندها رصيد أو لها حركات سابقة)
     */
    private function walletsQuery(Request $request)
    {
        $query = Wallet::whereHas('user', fn($q) => $q->role('client', 'web'))
            ->where(function ($q) {
                $q->where('balance', '>', 0)
                  ->orWhereHas('transactions');
            });

        if ($request->filled('search_name')) {
            $query->whereHas('user', fn($q) => $q->where('name', 'LIKE', '%' . $request->search_name . '%'));
        }

        if ($request->filled('search_mobile')) {
            $query->whereHas('user', fn($q) => $q->where('mobile', 'LIKE', '%' . $request->search_mobile . '%'));
        }

        return $query;
    }

    public function index(Request $request)
    {
        $totalBalance = $this->walletsQuery($request)->sum('balance');
        $totalWalletsCount = $this->walletsQuery($request)->count();

        $wallets = $this->walletsQuery($request)
            ->with('user:id,name,mobile,image')
            ->withCount('transactions')
            ->withMax('transactions', 'created_at')
            ->orderByDesc('balance')
            ->paginate(PAGANATION_COUNT)
            ->withQueryString();

        $data = [
            'wallets' => $wallets,
            'totalBalance' => $totalBalance,
            'totalWalletsCount' => $totalWalletsCount,
            'routeName' => $this->routeName,
            'namesection' => $this->namesection,
            'request_search' => $request->all(),
        ];

        return view('dashboard.walletsclients.index', $data);
    }

    public function transactions(Request $request, $id)
    {
        $wallet = Wallet::with('user:id,name,mobile,image')->find($id);

        if (!$wallet) {
            return redirect()->route($this->routeName)->with(['error' => __('dashboard.ID_Not_Found')]);
        }

        $totalCredit = WalletTransaction::where('wallet_id', $wallet->id)
            ->whereIn('type', ['credit', 'refund'])
            ->sum('amount');

        $totalDebit = WalletTransaction::where('wallet_id', $wallet->id)
            ->where('type', 'debit')
            ->sum('amount');

        $transactions = WalletTransaction::where('wallet_id', $wallet->id)
            ->with('order:id,order_number')
            ->orderByDesc('id')
            ->paginate(PAGANATION_COUNT);

        $data = [
            'wallet' => $wallet,
            'transactions' => $transactions,
            'totalCredit' => $totalCredit,
            'totalDebit' => $totalDebit,
            'routeName' => $this->routeName,
            'namesection' => $this->namesection,
        ];

        return view('dashboard.walletsclients.transactions', $data);
    }
}
