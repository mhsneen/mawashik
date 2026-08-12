<?php
// app/Services/WalletSettlementService.php

namespace App\Services;

use App\Models\{Wallet, WalletTransaction, WalletWithdrawalRequest};
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

/**
 * نقطة القرار الموحّدة لكل حركات "التسوية" بين السائق والمتجر:
 * - طلب سحب يبدأه السائق (يحتاج موافقة أدمن)
 * - تسوية يدوية يبدأها الأدمن مباشرة (دفع للسائق / تحصيل كاش منه)
 * كلاهما ينتهي بإنشاء WalletTransaction فعلي حتى يبقى سجل تدقيق واحد موحّد
 * لكل حركات المحفظة (نفس الجدول المستخدم لحركات الطلبات).
 */
class WalletSettlementService
{
    /**
     * السائق يطلب سحب مستحقاته (فقط إذا رصيده موجب = المتجر مديون له)
     *
     * @throws InvalidArgumentException
     */
    public function requestWithdrawal(Wallet $wallet, float $amount, ?string $note = null): WalletWithdrawalRequest
    {
        return DB::transaction(function () use ($wallet, $amount, $note) {
            $locked = Wallet::where('id', $wallet->id)->lockForUpdate()->first();

            if ($amount <= 0) {
                throw new InvalidArgumentException(__('api.Invalid_Withdrawal_Amount'));
            }

            if ($locked->balance < $amount) {
                throw new InvalidArgumentException(__('api.Insufficient_Wallet_Balance_For_Withdrawal'));
            }

            $hasPending = WalletWithdrawalRequest::where('wallet_id', $wallet->id)
                ->where('status', 'pending')
                ->exists();

            if ($hasPending) {
                throw new InvalidArgumentException(__('api.Pending_Withdrawal_Request_Exists'));
            }

            return WalletWithdrawalRequest::create([
                'wallet_id'    => $locked->id,
                'user_id'      => $locked->user_id,
                'amount'       => $amount,
                'status'       => 'pending',
                'driver_note'  => $note,
            ]);
        });
    }

    /**
     * السائق يلغي طلبه بنفسه طالما لسا Pending
     *
     * @throws InvalidArgumentException
     */
    public function cancel(WalletWithdrawalRequest $request): void
    {
        if ($request->status !== 'pending') {
            throw new InvalidArgumentException(__('api.Withdrawal_Request_Not_Pending'));
        }

        $request->update(['status' => 'cancelled', 'processed_at' => now()]);
    }

    /**
     * موافقة الأدمن: يخصم المبلغ فعلياً من رصيد المحفظة ويسجل حركة Debit
     *
     * @throws InvalidArgumentException
     */
    public function approve(WalletWithdrawalRequest $request, int $adminId, ?string $adminNote = null): void
    {
        DB::transaction(function () use ($request, $adminId, $adminNote) {
            $locked = WalletWithdrawalRequest::where('id', $request->id)->lockForUpdate()->first();

            if ($locked->status !== 'pending') {
                throw new InvalidArgumentException(__('dashboard.Withdrawal_Request_Not_Pending'));
            }

            $wallet = Wallet::where('id', $locked->wallet_id)->lockForUpdate()->first();

            if ($wallet->balance < $locked->amount) {
                throw new InvalidArgumentException(__('dashboard.Insufficient_Wallet_Balance_For_Withdrawal'));
            }

            $wallet->decrement('balance', $locked->amount);

            WalletTransaction::create([
                'wallet_id'   => $wallet->id,
                'order_id'    => null,
                'type'        => 'debit',
                'amount'      => $locked->amount,
                'description' => __('dashboard.withdrawal_request_approved_description', ['id' => $locked->id]),
            ]);

            $locked->update([
                'status'       => 'approved',
                'admin_note'   => $adminNote,
                'processed_by' => $adminId,
                'processed_at' => now(),
            ]);
        });
    }

    /**
     * رفض الأدمن لطلب السحب (بدون أي تأثير على الرصيد)
     *
     * @throws InvalidArgumentException
     */
    public function reject(WalletWithdrawalRequest $request, int $adminId, ?string $adminNote = null): void
    {
        if ($request->status !== 'pending') {
            throw new InvalidArgumentException(__('dashboard.Withdrawal_Request_Not_Pending'));
        }

        $request->update([
            'status'       => 'rejected',
            'admin_note'   => $adminNote,
            'processed_by' => $adminId,
            'processed_at' => now(),
        ]);
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
     *
     * @throws InvalidArgumentException
     */
    public function manualSettlement(Wallet $wallet, float $amount, string $direction, int $adminId, ?string $note = null): WalletTransaction
    {
        return DB::transaction(function () use ($wallet, $amount, $direction, $adminId, $note) {
            $locked = Wallet::where('id', $wallet->id)->lockForUpdate()->first();

            if ($amount <= 0) {
                throw new InvalidArgumentException(__('dashboard.Invalid_Amount'));
            }

            $type = $direction === 'pay_driver' ? 'debit' : 'credit';

            if ($direction === 'collect_cash') {
                if ($locked->balance >= 0) {
                    throw new InvalidArgumentException(__('dashboard.Driver_Not_Owes_Store'));
                }

                $debtAmount = abs($locked->balance);
                if ($amount > $debtAmount) {
                    throw new InvalidArgumentException(__('dashboard.Amount_Exceeds_Driver_Debt', ['debt' => $debtAmount]));
                }
            }

            if ($direction === 'pay_driver') {
                if ($locked->balance <= 0) {
                    throw new InvalidArgumentException(__('dashboard.Store_Not_Owes_Driver'));
                }

                if ($amount > $locked->balance) {
                    throw new InvalidArgumentException(__('dashboard.Amount_Exceeds_Driver_Balance', ['balance' => $locked->balance]));
                }
            }

            if ($type === 'debit') {
                $locked->decrement('balance', $amount);
            } else {
                $locked->increment('balance', $amount);
            }

            $description = $note ?: ($direction === 'pay_driver'
                ? __('dashboard.manual_settlement_pay_driver_description')
                : __('dashboard.manual_settlement_collect_cash_description'));

            return WalletTransaction::create([
                'wallet_id'   => $locked->id,
                'order_id'    => null,
                'type'        => $type,
                'amount'      => $amount,
                'description' => $description,
            ]);
        });
    }

}
