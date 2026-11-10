<?php

namespace App\Http\Controllers\Api\V1\Trader;

use App\Http\Controllers\Controller;
use App\Models\{Wallet, WalletTransaction, WalletWithdrawalRequest};
use App\Services\WalletSettlementService;
use App\Traits\ApiTrait;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use InvalidArgumentException;

/**
 * TraderWalletApiController — محفظة التاجر.
 *
 * العمليات:
 *  - balance: رصيد المحفظة + المستحقات القادمة.
 *  - transactions: سجل حركات المحفظة.
 *  - requestWithdrawal: طلب سحب مستحقاته.
 *  - getWithdrawalRequests: قائمة طلبات السحب.
 *  - cancelWithdrawalRequest: إلغاء طلب سحب معلق.
 *
 * الفرق عن محفظة المندوب: التاجر لا يحتاج موافقة على كل عملية إيداع،
 * لكن طلبات السحب تحتاج موافقة الأدمن (نفس نمط المندوب).
 *
 * ❗ إصلاحات هذا الملف:
 *  1) 🔴 cancelWithdrawalRequest كان يستدعي $service->cancelWithdrawal($withdrawal)
 *     لكن الدالة اسمها cancel() — كان يرمي BadMethodCallException في كل مرة!
 *  2) 🔴 requestWithdrawal و cancelWithdrawalRequest كانا يستخدمان $request->validate()
 *     التي ترمي ValidationException بصيغة HTML — غير متوافقة مع API. تحوّل لـ Validator::make().
 *  3) 🔴 cancelWithdrawalRequest لم يكن يمسك InvalidArgumentException بشكل منفصل،
 *     فكان يرجع "Api_Error_Contact_Programmer" حتى لأخطاء الأعمال المتوقعة (مثل "ليس pending").
 *  4) 🔴 balance(): الاستعلام عن pending_settlement كان يرجع 0 دائماً بسبب bug في
 *     $fillable لموديل WalletTransaction (تم إصلاحه في ملف منفصل).
 *  5) توحيد نمط الاستجابة (response shape) مع DriverWalletApiController.
 */
class TraderWalletApiController extends Controller
{
    use ApiTrait;

    public function balance(Request $request)
    {
        try {
            $trader = $request->user();
            $wallet = $trader->wallet ?: Wallet::firstOrCreate(
                ['user_id' => $trader->id],
                ['balance' => 0, 'rolename' => 'trader']
            );

            // ✅ المستحقات القادمة = عمولات تاجر مُسجّلة كـ credit (لم تُسحب بعد).
            // ملاحظة: هذا الاستعلام يعمل الآن بعد إصلاح $fillable في WalletTransaction.
            // إذا أردت تعريف "المستحقات القادمة" = "طلبات سُلّمت لكنها لم تُحوّل للمحفظة" بدلاً من ذلك،
            // فيجب التحقق من الطلبات التي لا توجد لها حركة commission_trader بعد.
            $pendingSettlement = WalletTransaction::where('transaction_type', 'commission_trader')
                ->where('related_user_id', $trader->id)
                ->where('type', 'credit')
                ->whereBetween('created_at', [now()->subDays(30), now()])
                ->sum('amount');

            // ✅ مواشي وسيط: إحصائيات مالية شاملة للتاجر
            $completedOrdersQuery = $trader->traderOrders()->where('status_id', 5);

            $totalSales       = (clone $completedOrdersQuery)->sum('final_price');
            $totalCommission  = (clone $completedOrdersQuery)->sum('commission_trader');
            $totalDeposit     = (clone $completedOrdersQuery)->sum('deposit_amount');
            $totalSlaughter   = (clone $completedOrdersQuery)->sum('slaughter_total');
            $totalNetRevenue  = $totalSales - $totalCommission;

            $todaySales      = (clone $completedOrdersQuery)->whereDate('delivered_at', today())->sum('final_price');
            $todayCommission = (clone $completedOrdersQuery)->whereDate('delivered_at', today())->sum('commission_trader');

            $monthSales      = (clone $completedOrdersQuery)
                ->whereMonth('delivered_at', now()->month)
                ->whereYear('delivered_at', now()->year)
                ->sum('final_price');
            $monthCommission = (clone $completedOrdersQuery)
                ->whereMonth('delivered_at', now()->month)
                ->whereYear('delivered_at', now()->year)
                ->sum('commission_trader');

            $completedOrdersCount = $completedOrdersQuery->count();

            return $this->returnData('data', [
                'balance'              => (float) $wallet->balance,
                'pending_settlement'   => (float) $pendingSettlement,
                'currency'             => 'SAR',

                // ✅ مواشي وسيط: ملخص مالي شامل للتاجر
                'financial_summary' => [
                    'total_sales'             => (float) $totalSales,
                    'total_commission'        => (float) $totalCommission,
                    'total_deposit_collected' => (float) $totalDeposit,
                    'total_slaughter_revenue' => (float) $totalSlaughter,
                    'total_net_revenue'       => (float) $totalNetRevenue,    // = مبيعات - عمولات
                    'completed_orders'        => (int) $completedOrdersCount,

                    'today' => [
                        'sales'      => (float) $todaySales,
                        'commission' => (float) $todayCommission,
                        'net'        => (float) ($todaySales - $todayCommission),
                    ],
                    'month' => [
                        'sales'      => (float) $monthSales,
                        'commission' => (float) $monthCommission,
                        'net'        => (float) ($monthSales - $monthCommission),
                    ],
                ],

                // ✅ نسبة العمولة المطبّقة على هذا التاجر
                'commission_rate' => $trader->commission_rate !== null
                    ? (float) $trader->commission_rate
                    : null,  // null = يستخدم النسبة الافتراضية من الإعدادات
            ], __('api.Api_Return_Data'));

        } catch (Exception $ex) {
            Log::error('Trader wallet balance: ' . $ex->getMessage());
            return $this->returnError(__('api.Api_Error_Contact_Programmer'));
        }
    }

    public function transactions(Request $request)
    {
        try {
            $trader = $request->user();
            $wallet = $trader->wallet;

            if (!$wallet) {
                return $this->returnData('data', ['items' => [], 'meta' => [
                    'current_page' => 1, 'total_pages' => 1, 'total_items' => 0,
                ]], __('api.Api_Return_Data'));
            }

            $query = WalletTransaction::where('wallet_id', $wallet->id)
                ->when($request->filled('type'), fn($q) => $q->where('type', $request->type))
                ->when($request->filled('transaction_type'), fn($q) => $q->where('transaction_type', $request->transaction_type))
                ->orderBy('id', 'desc');

            $txns = $query->paginate(API_PAGANATION_COUNT)->withQueryString();

            $data = [
                'items' => $txns->getCollection()->map(fn($t) => [
                    'id'                => $t->id,
                    'type'              => $t->type,
                    'amount'            => (float) $t->amount,
                    'transaction_type'  => $t->transaction_type,
                    'description'       => $t->description,
                    'order_id'          => $t->order_id,
                    'created_at'        => $t->created_at?->format('Y-m-d H:i'),
                ]),
                'meta' => [
                    'current_page' => $txns->currentPage(),
                    'total_pages'  => $txns->lastPage(),
                    'total_items'  => $txns->total(),
                ],
            ];

            return $this->returnData('data', $data, __('api.Api_Return_Data'));

        } catch (Exception $ex) {
            Log::error('Trader wallet transactions: ' . $ex->getMessage());
            return $this->returnError(__('api.Api_Error_Contact_Programmer'));
        }
    }

    public function requestWithdrawal(Request $request, WalletSettlementService $service)
    {
        // ✅ إصلاح: استخدام Validator::make بدل $request->validate() لموافقة نمط API
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:0.01',
            'note'   => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return $this->returnError($validator->errors()->first());
        }

        try {
            $trader = $request->user();
            $wallet = $trader->wallet ?: Wallet::firstOrCreate(
                ['user_id' => $trader->id],
                ['balance' => 0, 'rolename' => 'trader']
            );

            $withdrawal = $service->requestWithdrawal($wallet, (float) $request->amount, $request->note);

            return $this->returnData('data', [
                'id'         => $withdrawal->id,
                'amount'     => (float) $withdrawal->amount,
                'status'     => $withdrawal->status,
                'created_at' => $withdrawal->created_at?->diffForHumans(),
            ], __('api.Withdrawal_Request_Sent_Success'));

        } catch (InvalidArgumentException $ex) {
            // ✅ إصلاح: إرجاع رسالة الخطأ الفعلية بدل رسالة عامة
            return $this->returnError($ex->getMessage());
        } catch (Exception $ex) {
            Log::error('Trader withdrawal: ' . $ex->getMessage(), ['trace' => $ex->getTraceAsString()]);
            return $this->returnError(__('api.Api_Error_Contact_Programmer'));
        }
    }

    public function getWithdrawalRequests(Request $request)
    {
        try {
            $trader = $request->user();

            $requests = WalletWithdrawalRequest::where('user_id', $trader->id)
                ->orderBy('id', 'desc')
                ->paginate(API_PAGANATION_COUNT);

            $data = [
                'items' => $requests->getCollection()->map(fn($w) => [
                    'id'           => $w->id,
                    'amount'       => (float) $w->amount,
                    'status'       => $w->status,
                    'note'         => $w->driver_note,   // ❗ الحقل في DB اسمه driver_note (يُستخدم للسائق والتاجر)
                    'admin_note'   => $w->admin_note,
                    'created_at'   => $w->created_at ? \Carbon\Carbon::parse($w->created_at)->translatedFormat('Y-m-d h:i A') : null,
                    'processed_at' => $w->processed_at ? \Carbon\Carbon::parse($w->processed_at)->translatedFormat('Y-m-d h:i A') : null,
                ]),
                'meta' => [
                    'current_page' => $requests->currentPage(),
                    'total_pages'  => $requests->lastPage(),
                    'total_items'  => $requests->total(),
                    'per_page'     => $requests->perPage(),
                ],
            ];

            return $this->returnData('data', $data, __('api.Api_Return_Data'));

        } catch (Exception $ex) {
            Log::error('Trader get withdrawals: ' . $ex->getMessage());
            return $this->returnError(__('api.Api_Error_Contact_Programmer'));
        }
    }

    public function cancelWithdrawalRequest(Request $request, WalletSettlementService $service)
    {
        // ✅ إصلاح: استخدام Validator::make بدل $request->validate()
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|exists:wallet_withdrawal_requests,id',
        ]);

        if ($validator->fails()) {
            return $this->returnError($validator->errors()->first());
        }

        try {
            $trader = $request->user();

            // نضمن أن الطلب يعود لهذا التاجر فقط (مع الحالة pending)
            $withdrawal = WalletWithdrawalRequest::where('user_id', $trader->id)
                ->where('id', $request->id)
                ->where('status', 'pending')
                ->first();

            if (!$withdrawal) {
                return $this->returnError(__('api.Withdrawal_Not_Found_Or_Not_Pending'));
            }

            // ✅ إصلاح حرج: كان $service->cancelWithdrawal($withdrawal) — الدالة اسمها cancel()
            $service->cancel($withdrawal);

            return $this->returnSuccessMessage(__('api.Withdrawal_Request_Cancelled_Success'));

        } catch (InvalidArgumentException $ex) {
            // ✅ إصلاح: إرجاع رسالة الخطأ الفعلية بدل رسالة عامة
            return $this->returnError($ex->getMessage());
        } catch (Exception $ex) {
            Log::error('Trader cancel withdrawal: ' . $ex->getMessage(), ['trace' => $ex->getTraceAsString()]);
            return $this->returnError(__('api.Api_Error_Contact_Programmer'));
        }
    }
}
