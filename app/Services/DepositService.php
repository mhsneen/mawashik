<?php

namespace App\Services;

use App\Models\{Order, Setting, User, Wallet, WalletTransaction};
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * DepositService — خدمة العربون (Deposit).
 *
 * المسؤوليات:
 *  - حساب قيمة العربون لطلب (نسبة مئوية من قيمة الطلب بعد الخصم).
 *  - التحقق من تفعيل النظام.
 *  - تسجيل العربون كمدفوع عندما يكون المبلغ محصَّلاً فعلاً (محفظة/دفع إلكتروني).
 *  - استرداد العربون عند الإلغاء المبكر.
 *  - إطلاق العربون للتاجر عند التسليم الناجح.
 *  - مصادرة العربون للمنصة عند الإلغاء المتأخر.
 *
 * قواعد مهمة:
 *  - العربون يُسجَّل "مدفوعاً" فقط إذا كان الطلب مدفوعاً فعلاً (payment_status = paid).
 *    في الدفع النقدي لا يُحصَّل أي مبلغ مسبقاً، فلا يُسجَّل عربون ولا يُسترد ولا يُصادَر.
 *  - كل عملية (إطلاق/مصادرة/استرداد) idempotent: لا تُنفَّذ مرتين لنفس الطلب.
 *  - أساس الحساب: total_price (المنتجات + الذبح) - discount_amount.
 */
class DepositService
{
    public function isEnabled(): bool
    {
        return Setting::where('key', 'deposit_active')->value('plain_value') == '1';
    }

    public function percentage(): float
    {
        return (float) (Setting::where('key', 'deposit_percentage')->value('plain_value') ?? 0);
    }

    /**
     * حساب قيمة العربون لطلب.
     *
     * الأساس: قيمة الطلب بعد الخصم (المنتجات + خدمة الذبح، بدون التوصيل ولا الضريبة).
     * لو deposit_active=false، القيمة = 0.
     *
     * @return array{amount: float, remaining: float}
     */
    public function calculate(Order $order): array
    {
        if (!$this->isEnabled()) {
            return ['amount' => 0, 'remaining' => $order->final_price];
        }

        $base = max(0, $order->total_price - $order->discount_amount);

        $amount = round($base * ($this->percentage() / 100), 2);

        $remaining = max(0, $order->final_price - $amount);

        return ['amount' => $amount, 'remaining' => $remaining];
    }

    /**
     * يحسب ويثبّت العربون والمتبقي وعمولة التاجر على الطلب.
     * يجب استدعاؤه بعد تثبيت الإجماليات (total_price / final_price) وليس عند created.
     */
    public function applyToOrder(Order $order): void
    {
        $deposit = $this->calculate($order);

        $order->deposit_amount   = $deposit['amount'];
        $order->remaining_amount = $deposit['remaining'];
        $order->slaughter_total  = $order->items()->sum('slaughter_total');
        $order->commission_trader = app(CommissionService::class)->calculateForTrader($order);

        $order->saveQuietly();
    }

    /**
     * تسجيل العربون كمدفوع (محجوز لدى المنصة).
     *
     * يُستدعى فقط بعد أن يكون المبلغ قد حُصِّل فعلاً:
     *  - الدفع من المحفظة: خُصم الإجمالي كاملاً عند إنشاء الطلب.
     *  - الدفع الإلكتروني: حُصِّل عبر البوابة وتم التحقق منه.
     * لذلك لا نخصم من المحفظة هنا مرة ثانية. الدفع النقدي لا يُسجَّل له عربون.
     */
    public function reserve(Order $order): void
    {
        if ($order->deposit_amount <= 0 || $order->deposit_status === 'paid') {
            return;
        }

        if ($order->payment_status !== 'paid') {
            return;
        }

        $order->deposit_status  = 'paid';
        $order->deposit_paid_at = now();
        $order->saveQuietly();
    }

    /**
     * تسجيل العربون كمدفوع بعد تحصيله فعلاً خارج المحفظة (بوابة دفع/تحويل بنكي مؤكَّد).
     */
    public function markPaid(Order $order): void
    {
        if ($order->deposit_amount <= 0 || $order->deposit_status === 'paid') {
            return;
        }

        $order->deposit_status  = 'paid';
        $order->deposit_paid_at = now();
        $order->saveQuietly();
    }

    /**
     * دفع العربون من محفظة العميل (طلب نقدي + deposit_method=wallet).
     * يجب استدعاؤه داخل DB::transaction. يرمي InvalidArgumentException عند عدم كفاية الرصيد.
     */
    public function payFromWallet(Order $order, User $user): void
    {
        $amount = (float) $order->deposit_amount;
        if ($amount <= 0 || $order->deposit_status === 'paid') {
            return;
        }

        $wallet = Wallet::where('user_id', $user->id)->lockForUpdate()->first();

        if (!$wallet || (float) $wallet->balance < $amount) {
            throw new \InvalidArgumentException(__('api.Insufficient_wallet_balance'));
        }

        $wallet->balance -= $amount;
        $wallet->save();

        WalletTransaction::create([
            'wallet_id'        => $wallet->id,
            'order_id'         => $order->id,
            'type'             => 'debit',
            'amount'           => $amount,
            'transaction_type' => 'deposit_paid',
            'related_user_id'  => $user->id,
            'description'      => __('dashboard.deposit_paid_for_order', [
                'order' => $order->order_number,
            ]),
        ]);

        $this->markPaid($order);
    }

    /**
     * استرداد العربون للعميل عند الإلغاء المبكر.
     */
    public function refundToCustomer(Order $order): void
    {
        if ($order->deposit_amount <= 0 || $order->deposit_status !== 'paid') {
            return;
        }

        DB::transaction(function () use ($order) {
            $user = $order->user;
            if (!$user) {
                return;
            }

            $wallet = $this->walletFor($user->id, 'client');

            $wallet->balance += $order->deposit_amount;
            $wallet->save();

            WalletTransaction::create([
                'wallet_id'        => $wallet->id,
                'order_id'         => $order->id,
                'type'             => 'credit',
                'amount'           => $order->deposit_amount,
                'transaction_type' => 'deposit_refunded',
                'related_user_id'  => $user->id,
                'description'      => __('dashboard.deposit_refunded_for_order', [
                    'order' => $order->order_number,
                ]),
            ]);

            $order->deposit_status      = 'refunded';
            $order->deposit_refunded_at = now();
            $order->saveQuietly();
        });
    }

    /**
     * إطلاق العربون لمحفظة التاجر عند التسليم الناجح.
     *
     * يُحوَّل للتاجر الأقل من (العربون، صافي مستحقاته) — صافي المستحقات = قيمة المنتجات بعد الخصم
     * ناقص عمولة المنصة. الباقي من المستحقات يُسوّى في CommissionService::settleOnDelivery.
     */
    public function releaseToTrader(Order $order): void
    {
        if ($order->deposit_amount <= 0 || $order->deposit_status !== 'paid' || !$order->trader_id) {
            return;
        }

        $amount = min((float) $order->deposit_amount, (float) $order->trader_net_amount);

        DB::transaction(function () use ($order, $amount) {
            if ($amount > 0) {
                $wallet = $this->walletFor($order->trader_id, 'trader');

                $wallet->balance += $amount;
                $wallet->save();

                WalletTransaction::create([
                    'wallet_id'        => $wallet->id,
                    'order_id'         => $order->id,
                    'type'             => 'credit',
                    'amount'           => $amount,
                    'transaction_type' => 'deposit_released',
                    'related_user_id'  => $order->trader_id,
                    'description'      => __('dashboard.deposit_released_for_order', [
                        'order' => $order->order_number,
                    ]),
                ]);
            }

            $order->deposit_status = 'released';
            $order->saveQuietly();
        });
    }

    /**
     * مصادرة العربون لصالح المنصة (إلغاء بعد بدء التجهيز).
     * المبلغ محتجز أصلاً لدى المنصة، فلا حركة محفظة؛ نغيّر الحالة إلى forfeited ونسجّل في اللوج.
     */
    public function forfeitToPlatform(Order $order): void
    {
        if ($order->deposit_amount <= 0 || $order->deposit_status !== 'paid') {
            return;
        }

        $order->deposit_status = 'forfeited';
        $order->saveQuietly();

        Log::info('Deposit forfeited to platform', [
            'order_id' => $order->id,
            'amount'   => $order->deposit_amount,
        ]);
    }

    private function walletFor(int $userId, string $role): Wallet
    {
        return Wallet::where('user_id', $userId)->lockForUpdate()->first()
            ?? Wallet::firstOrCreate(
                ['user_id' => $userId],
                ['balance' => 0, 'rolename' => $role]
            );
    }
}
