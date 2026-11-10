<?php

namespace App\Services;

use App\Models\{Order, Setting, User, Wallet, WalletTransaction};
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * DepositService — خدمة العربون (Deposit).
 *
 * المسؤوليات:
 *  - حساب قيمة العربون لطلب جديد (نسبة من الإجمالي بعد الخصم).
 *  - التحقق من تفعيل النظام.
 *  - حجز العربون عند إنشاء الطلب.
 *  - استرداد العربون عند الإلغاء.
 *  - إطلاق العربون للتاجر عند التسليم الناجح.
 *  - مصادرة العربون للمنصة عند الإلغاء المتأخر.
 *
 * ❗ إصلاحات هذا الملف:
 *  1) خطأ إملائي: transaction_transaction_type → transaction_type (السطر 87).
 *  2) إزالة $user->model->wallet() الإشارة لـ model غير الموجودة (السطر 75).
 *  3) إصلاح $wallet->setting->save() → $wallet->save() (السطر 119).
 *  4) إصلاح WalletTransaction::create الناقص في refundToCustomer (كانت تنشئ حركة بـ wallet_id فقط!).
 *  5) تحديث deposit_status للطلب بعد الاسترداد.
 *  6) إضافة transaction_type لكل حركة عربون.
 *  7) إضافة related_user_id للربط بالعميل صاحب العربون.
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
     * الأساس: المبلغ المتبقي بعد الخصم (لا يشمل التوصيل ولا الضريبة ولا الذبح).
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

        // ✅ إلغاء الحد الأدنى والأقصى - العربون = نسبة مئوية فقط
        $amount = round($base * ($this->percentage() / 100), 2);

        $remaining = max(0, $order->final_price - $amount);

        return ['amount' => $amount, 'remaining' => $remaining];
    }

    /**
     * حجز العربون من محفظة العميل.
     * أو تسجيله كمدفوع نقداً (cash) عند استلام المنتج.
     */
    public function reserve(Order $order): void
    {
        if ($order->deposit_amount <= 0) {
            return;
        }

        DB::transaction(function () use ($order) {
            $order->deposit_status = 'paid';
            $order->deposit_paid_at = now();
            $order->saveQuietly();

            // إذا الدفع من المحفظة، نخصم من رصيد العميل ونُسجّل الحركة
            if (in_array($order->payment_method, ['wallet', 'wallet_and_cash'], true)) {
                $user = $order->user;
                if ($user) {
                    // ✅ إصلاح: كان $user->model->wallet() — model غير موجودة
                    $wallet = $user->wallet()->lockForUpdate()->first()
                        ?? Wallet::firstOrCreate(
                            ['user_id' => $user->id],
                            ['balance' => 0, 'rolename' => 'client']
                        );

                    if ($wallet && $wallet->balance >= $order->deposit_amount) {
                        $wallet->balance -= $order->deposit_amount;
                        $wallet->save();

                        WalletTransaction::create([
                            'wallet_id'        => $wallet->id,
                            'order_id'         => $order->id,
                            'type'             => 'debit',
                            'amount'           => $order->deposit_amount,
                            // ✅ إصلاح: كان transaction_transaction_type (typo) فكان يُتجاهل بصمت
                            'transaction_type' => 'deposit_paid',
                            'related_user_id'  => $user->id, // ✅ ربط بالعميل
                            'description'      => __('dashboard.deposit_reserved_for_order', ['order' => $order->order_number]),
                        ]);
                    } else {
                        throw new \RuntimeException("Insufficient wallet balance for deposit.");
                    }
                }
            }
            // للدفع الإلكتروني (بوابة): العربون يأتي مع العملية، لا حاجة لحركة محفظة هنا.
            // للدفع كاش عند الاستلام: العربون يُسجّل فقط كـ "مستحق"، يُحصّل عند التسليم.
        });
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
            if ($user) {
                $wallet = $user->wallet()->lockForUpdate()->first()
                    ?? Wallet::firstOrCreate(
                        ['user_id' => $user->id],
                        ['balance' => 0, 'rolename' => 'client']
                    );

                $wallet->balance += $order->deposit_amount;
                // ✅ إصلاح: كان $wallet->setting->save() — setting ليست علاقة على Wallet
                $wallet->save();

                // ✅ إصلاح حرج: الكود القديم كان ينشئ حركة بـ wallet_id فقط!
                // كانت تُخزَّن كحركة بلا type, amount, description — بيانات مكسورة.
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

                // ✅ تحديث حالة العربون في الطلب
                $order->deposit_status = 'refunded';
                $order->deposit_refunded_at = now();
                $order->saveQuietly();
            }
        });
    }
}
