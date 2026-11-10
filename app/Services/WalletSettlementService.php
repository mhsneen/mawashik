<?php
// app/Services/WalletSettlementService.php

namespace App\Services;

use App\Models\{Wallet, WalletTransaction, WalletWithdrawalRequest};
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

/**
 * نقطة القرار الموحّدة لكل حركات "التسوية" بين المستخدمين (سائق/تاجر) والمنصة:
 *  - طلب سحب يبدأه المستخدم (يحتاج موافقة أدمن).
 *  - تسوية يدوية يبدأها الأدمن مباشرة (دفع للمستخدم / تحصيل كاش منه).
 *  كلاهما ينتهي بإنشاء WalletTransaction فعلي حتى يبقى سجل تدقيق واحد موحّد
 *  لكل حركات المحفظة (نفس الجدول المستخدم لحركات الطلبات).
 *
 * ❗ تعديلات هذا الإصلاح:
 *  1) إضافة transaction_type لكل WalletTransaction::create (كانت تُسجّل كلها كـ order_payment).
 *  2) إضافة related_user_id لكل حركة (للربط بالتاجر/المندوب).
 *  3) cancel() أصبحت داخل DB::transaction مع lockForUpdate لمنع race condition
 *     بين إلغاء المستخدم وموافقة الأدمن المتزامنة.
 *  4) إزالة docblock المكرر في manualSettlement().
 *  5) تعميم صياغة DocBlocks لتشمل التاجر والمندوب (كانت "السائق" فقط).
 */
class WalletSettlementService
{
    /**
     * المستخدم يطلب سحب مستحقاته (سواء كان سائقاً أو تاجراً).
     * يشترط أن يكون رصيد المحفظة موجباً (المنصة مديونة له).
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

            $withdrawalRequest = WalletWithdrawalRequest::create([
                'wallet_id'    => $locked->id,
                'user_id'      => $locked->user_id,
                'amount'       => $amount,
                'status'       => 'pending',
                'driver_note'  => $note, // ❗ الحقل في الـ DB اسمه driver_note، لكنه يُستخدم للسائق والتاجر معاً.
            ]);

            AdminNotifier::withdrawalRequested($withdrawalRequest);

            return $withdrawalRequest;
        });
    }

    /**
     * المستخدم (سائق/تاجر) يلغي طلبه بنفسه طالما لسا Pending.
     *
     * ❗ إصلاح: أصبح داخل DB::transaction مع lockForUpdate لمنع race condition.
     *
     * @throws InvalidArgumentException
     */
    public function cancel(WalletWithdrawalRequest $request): void
    {
        DB::transaction(function () use ($request): void {
            // ❗ نأخذ نسخة مغلقة لمنع الموافقة المتزامنة من الأدمن أثناء الإلغاء.
            $locked = WalletWithdrawalRequest::where('id', $request->id)->lockForUpdate()->first();

            if (!$locked || $locked->status !== 'pending') {
                throw new InvalidArgumentException(__('api.Withdrawal_Request_Not_Pending'));
            }

            $locked->update([
                'status'       => 'cancelled',
                'processed_at' => now(),
            ]);
        });
    }

    /**
     * موافقة الأدمن: يخصم المبلغ فعلياً من رصيد المحفظة ويسجل حركة Debit.
     *
     * ❗ إصلاح: إضافة transaction_type='withdrawal_approved' و related_user_id.
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

            // ✅ إصلاح: إضافة transaction_type + related_user_id للحركة
            WalletTransaction::create([
                'wallet_id'        => $wallet->id,
                'order_id'         => null,
                'type'             => 'debit',
                'amount'           => $locked->amount,
                'transaction_type' => 'withdrawal_approved', // ✅ بدل الافتراضي order_payment
                'related_user_id' => $locked->user_id,      // ✅ لربط الحركة بالمستخدم
                'description'      => __('dashboard.withdrawal_request_approved_description', ['id' => $locked->id]),
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
     * رفض الأدمن لطلب السحب (بدون أي تأثير على الرصيد).
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
     * تسوية يدوية يبدأها الأدمن مباشرة من لوحة التحكم (بدون طلب من المستخدم):
     *  - pay_driver   : المنصة تدفع للمستخدم مستحقاته (Debit يخصم من رصيده الموجب).
     *  - collect_cash : الأدمن يوثّق استلام كاش من المستخدم (Credit يقرّب رصيده السالب من الصفر).
     *
     * ❗ إصلاحات:
     *  - إزالة docblock المكرر (كان مكتوب مرتين متتاليتين).
     *  - إضافة transaction_type مناسب لكل اتجاه (admin_deduction / admin_deposit).
     *  - إضافة related_user_id للحركة.
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

            // ✅ تصنيف الحركة بشكل صحيح
            $transactionType = $direction === 'pay_driver' ? 'admin_deduction' : 'admin_deposit';

            return WalletTransaction::create([
                'wallet_id'        => $locked->id,
                'order_id'         => null,
                'type'             => $type,
                'amount'           => $amount,
                'transaction_type' => $transactionType, // ✅ بدل الافتراضي order_payment
                'related_user_id' => $locked->user_id,   // ✅ ربط الحركة بالمستخدم
                'description'      => $description,
            ]);
        });
    }
}
