<?php
// app/Http/Controllers/Dashboard/WalletDriverController.php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\{Wallet, WalletTransaction, WalletWithdrawalRequest};
use App\Services\WalletSettlementService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Exception;

class WalletDriverController extends Controller
{
    protected $routeName = 'walletsdrivers';
    protected $namesection = 'dashboard.Driver_Wallets';

    public function __construct()
    {
        $this->middleware('permission:Drivers');
    }

    /**
     * بناء استعلام محافظ السائقين (فقط اللي عندها رصيد ≠ 0 أو لها حركات سابقة)
     */
    private function walletsQuery(Request $request)
    {
        $query = Wallet::whereHas('user', fn($q) => $q->role('driver', 'web'))
            ->where(function ($q) {
                $q->where('balance', '<>', 0)
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
        $pendingRequestsCount = WalletWithdrawalRequest::pending()->count();

        $wallets = $this->walletsQuery($request)
            ->with('user:id,name,mobile,image')
            ->withCount('transactions')
            ->withMax('transactions', 'created_at')
            ->orderByDesc('balance')
            ->paginate(PAGANATION_COUNT)
            ->withQueryString();

        $data = [
            'wallets'              => $wallets,
            'totalBalance'         => $totalBalance,
            'totalWalletsCount'    => $totalWalletsCount,
            'pendingRequestsCount' => $pendingRequestsCount,
            'routeName'            => $this->routeName,
            'namesection'          => $this->namesection,
            'request_search'       => $request->all(),
        ];

        return view('dashboard.walletsdrivers.index', $data);
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

        $withdrawalRequests = WalletWithdrawalRequest::where('wallet_id', $wallet->id)
            ->with('processedBy:id,name')
            ->orderByDesc('id')
            ->paginate(10, ['*'], 'withdrawal_page');

        $data = [
            'wallet'              => $wallet,
            'transactions'        => $transactions,
            'withdrawalRequests'  => $withdrawalRequests,
            'totalCredit'         => $totalCredit,
            'totalDebit'          => $totalDebit,
            'routeName'           => $this->routeName,
            'namesection'         => $this->namesection,
        ];

        return view('dashboard.walletsdrivers.transactions', $data);
    }

    public function withdrawalRequests(Request $request)
    {
        $query = WalletWithdrawalRequest::with(['wallet.user:id,name,mobile,image', 'processedBy:id,name']);

        if ($request->filled('search_status')) {
            $query->where('status', $request->search_status);
        }

        $requests = $query->orderByDesc('id')->paginate(PAGANATION_COUNT)->withQueryString();

        $data = [
            'requests'        => $requests,
            'routeName'       => $this->routeName,
            'namesection'     => $this->namesection,
            'request_search'  => $request->all(),
        ];

        return view('dashboard.walletsdrivers.withdrawal_requests', $data);
    }

    public function approveWithdrawal(Request $request, $id, WalletSettlementService $service)
    {
        $withdrawalRequest = WalletWithdrawalRequest::find($id);
        if (!$withdrawalRequest) {
            return redirect()->back()->with(['error' => __('dashboard.ID_Not_Found')]);
        }

        try {
            $service->approve($withdrawalRequest, Auth::guard('admin')->id(), $request->admin_note);
            return redirect()->back()->with(['success' => __('dashboard.Withdrawal_Request_Approved')]);
        } catch (Exception $ex) {
            return redirect()->back()->with(['error' => $ex->getMessage()]);
        }
    }

    public function rejectWithdrawal(Request $request, $id, WalletSettlementService $service)
    {
        $withdrawalRequest = WalletWithdrawalRequest::find($id);
        if (!$withdrawalRequest) {
            return redirect()->back()->with(['error' => __('dashboard.ID_Not_Found')]);
        }

        try {
            $service->reject($withdrawalRequest, Auth::guard('admin')->id(), $request->admin_note);
            return redirect()->back()->with(['success' => __('dashboard.Withdrawal_Request_Rejected')]);
        } catch (Exception $ex) {
            return redirect()->back()->with(['error' => $ex->getMessage()]);
        }
    }

     /**
     * تسوية يدوية يبدأها الأدمن مباشرة من لوحة التحكم (بدون طلب من السائق):
     * - pay_driver   : المتجر يدفع للسائق مستحقاته (Debit يخصم من رصيده الموجب)
     * - collect_cash : الأدمن يوثّق استلام كاش من السائق (Credit يقرّب رصيده السالب من الصفر)
     *
     * @throws InvalidArgumentException
     */
/**
     * تسوية يدوية يبدأها الأدمن مباشرة من لوحة التحكم (بدون طلب من السائق):
     * - pay_driver   : المتجر يدفع للسائق مستحقاته (Debit يخصم من رصيده الموجب)
     * - collect_cash : الأدمن يوثّق استلام كاش من السائق (Credit يقرّب رصيده السالب من الصفر)
     */
    public function manualSettlement(Request $request, $id, WalletSettlementService $service)
    {
        $request->validate([
            'amount'    => 'required|numeric|min:0.01',
            'direction' => 'required|in:pay_driver,collect_cash',
            'note'      => 'nullable|string|max:255',
        ]);

        $wallet = Wallet::find($id);
        if (!$wallet) {
            return redirect()->back()->with(['error' => __('dashboard.ID_Not_Found')]);
        }

        try {
            $service->manualSettlement(
                $wallet,
                (float) $request->amount,
                $request->direction,
                Auth::guard('admin')->id(),
                $request->note
            );

            return redirect()->back()->with(['success' => __('dashboard.Edit_success')]);
        } catch (Exception $ex) {
            return redirect()->back()->with(['error' => $ex->getMessage()]);
        }
    }

}
