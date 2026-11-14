<?php

namespace App\Services;

use App\Models\{Commission, Order, Setting, User, Wallet, WalletTransaction};
use Illuminate\Support\Facades\DB;

/**
 * CommissionService — خدمة العمولات.
 *
 * العمولات هنا هي عمولات المنصة المفروضة على الطرفين (البند 8 من المتطلبات):
 *  1) عمولة التاجر: نسبة مئوية من قيمة المنتجات + الذبح بعد الخصم (بدون التوصيل والضريبة).
 *  2) عمولة المندوب: نسبة مئوية من رسوم التوصيل، تُخصم من أجر المندوب.
 *
 * مصدر النسبة:
 *  - أولوية 1: عمود commission_rate على المستخدم (لو محدد).
 *  - أولوية 2: سجل Commission نشط في جدول commissions.
 *  - أولوية 3: الإعداد الافتراضي من settings.
 *
 * هذا يعطي الأدمن مرونة: نسبة افتراضية + تجاوز لكل مستخدم.
 */
class CommissionService
{
    public function isEnabled(): bool
    {
        return Setting::where('key', 'commission_active')->value('plain_value') == '1';
    }

    /**
     * نسبة عمولة التاجر (مثال: 5.00 = 5%).
     */
    public function rateForTrader(User $trader): float
    {
        // 1) العمود على المستخدم مباشرة
        if (!is_null($trader->commission_rate)) {
            return (float) $trader->commission_rate;
        }

        // 2) سجل Commission نشط
        $active = $trader->currentCommission('trader');
        if ($active) {
            return (float) $active->rate;
        }

        // 3) الإعداد الافتراضي
        return (float) (Setting::where('key', 'commission_trader_default')->value('plain_value') ?? 0);
    }

    /**
     * نسبة عمولة المندوب.
     */
    public function rateForDriver(User $driver): float
    {
        if (!is_null($driver->driver_commission_rate)) {
            return (float) $driver->driver_commission_rate;
        }

        $active = $driver->currentCommission('driver');
        if ($active) {
            return (float) $active->rate;
        }

        return (float) (Setting::where('key', 'commission_driver_default')->value('plain_value') ?? 0);
    }

    /**
     * حساب عمولة التاجر لطلب.
     * الأساس: total_price - discount_amount (قيمة المنتجات الفعلية بعد الخصم).
     */
    public function calculateForTrader(Order $order): float
    {
        if (!$this->isEnabled() || !$order->trader_id) {
            return 0;
        }

        $trader = $order->trader;
        if (!$trader) {
            return 0;
        }

        $base = max(0, $order->total_price - $order->discount_amount);

        return round($base * ($this->rateForTrader($trader) / 100), 2);
    }

    /**
     * حساب عمولة المندوب لطلب.
     * الأساس: رسوم التوصيل (delivery_price).
     */
    public function calculateForDriver(Order $order): float
    {
        if (!$this->isEnabled() || !$order->driver_id) {
            return 0;
        }

        $driver = $order->driver;
        if (!$driver) {
            return 0;
        }

        // عمولة المندوب على رسوم التوصيل فقط
        $base = (float) $order->delivery_price;

        return round($base * ($this->rateForDriver($driver) / 100), 2);
    }

    /**
     * تسوية العمولات عند التسليم الناجح (status_id = 5):
     *
     *  التاجر:
     *   - طلب مدفوع عبر المنصة (إلكتروني/محفظة): المنصة تحتفظ بالمبلغ، فيُضاف لمحفظة التاجر
     *     صافي مستحقاته (المنتجات بعد الخصم - العمولة) ناقص ما أُطلق له مسبقاً كعربون.
     *   - طلب نقدي: التاجر/المندوب استلم المبلغ نقداً، فتُخصم عمولة المنصة فقط من محفظة التاجر.
     *
     *  المندوب:
     *   - عمولة المنصة تُخصم من محفظته (أجر التوصيل يُضاف في DriverOrdersApiController::recordDriverEarning).
     *
     * ملاحظة: يجب استدعاء DepositService::releaseToTrader قبل هذه الدالة.
     */
    public function settleOnDelivery(Order $order): void
    {
        DB::transaction(function () use ($order) {
            // 1) التاجر
            if ($order->trader_id) {
                $trader = User::find($order->trader_id);

                if ($trader) {
                    $wallet = $trader->wallet()->lockForUpdate()->first()
                        ?? Wallet::firstOrCreate(
                            ['user_id' => $trader->id],
                            ['balance' => 0, 'rolename' => 'trader']
                        );

                    $net = (float) $order->trader_net_amount;

                    // الكاش مع توصيل: المندوب هو من حصّل المبلغ (ويسلّمه للمنصة)، فالتاجر لم يستلم نقداً
                    // ويُسوّى له صافي مستحقاته من المنصة. الكاش مع استلام من التاجر: التاجر حصّل بنفسه.
                    $traderCollectedCash = $order->payment_method === 'cash' && $order->delivery_type !== 'delivery';

                    if (!$traderCollectedCash) {
                        $alreadyReleased = $order->deposit_status === 'released'
                            ? min((float) $order->deposit_amount, $net)
                            : 0.0;

                        $credit = round($net - $alreadyReleased, 2);

                        if ($credit > 0) {
                            $wallet->balance += $credit;
                            $wallet->save();

                            WalletTransaction::create([
                                'wallet_id'        => $wallet->id,
                                'order_id'         => $order->id,
                                'type'             => 'credit',
                                'amount'           => $credit,
                                'transaction_type' => 'settlement',
                                'related_user_id'  => $trader->id,
                                'description'      => __('dashboard.trader_net_for_order', ['order' => $order->order_number]),
                            ]);
                        }
                    } elseif ($order->commission_trader > 0) {
                        $wallet->balance -= $order->commission_trader;
                        $wallet->save();

                        WalletTransaction::create([
                            'wallet_id'        => $wallet->id,
                            'order_id'         => $order->id,
                            'type'             => 'debit',
                            'amount'           => $order->commission_trader,
                            'transaction_type' => 'commission_trader',
                            'related_user_id'  => $trader->id,
                            'description'      => __('dashboard.platform_commission_for_order', ['order' => $order->order_number]),
                        ]);
                    }
                }
            }

            // 2) المندوب: خصم عمولة المنصة من أجر التوصيل
            if ($order->driver_id && $order->commission_driver > 0) {
                $driver = User::find($order->driver_id);
                if ($driver) {
                    $wallet = $driver->wallet()->lockForUpdate()->first()
                        ?? Wallet::firstOrCreate(
                            ['user_id' => $driver->id],
                            ['balance' => 0, 'rolename' => 'driver']
                        );

                    $wallet->balance -= $order->commission_driver;
                    $wallet->save();

                    WalletTransaction::create([
                        'wallet_id'        => $wallet->id,
                        'order_id'         => $order->id,
                        'type'             => 'debit',
                        'amount'           => $order->commission_driver,
                        'transaction_type' => 'commission_driver',
                        'related_user_id'  => $driver->id,
                        'description'      => __('dashboard.driver_commission_for_order', ['order' => $order->order_number]),
                    ]);
                }
            }
        });
    }

    /**
     * تعيين نسبة عمولة جديدة لتاجر/مندوب.
     * يُنشئ سجل Commission جديد ويُنهي القديم (effective_to = اليوم).
     */
    public function setRate(User $user, string $role, float $rate, ?string $note = null, ?int $setBy = null): Commission
    {
        return DB::transaction(function () use ($user, $role, $rate, $note, $setBy) {
            // إنهاء النسبة الحالية
            Commission::where('user_id', $user->id)
                ->where('role', $role)
                ->where('is_active', true)
                ->whereNull('effective_to')
                ->update(['effective_to' => now()->toDateString()]);

            return Commission::create([
                'user_id' => $user->id,
                'role' => $role,
                'rate' => $rate,
                'effective_from' => now()->toDateString(),
                'effective_to' => null,
                'is_active' => true,
                'note' => $note,
                'set_by' => $setBy,
            ]);
        });
    }
}
