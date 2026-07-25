<?php

namespace App\Services;

use App\Models\{User, Order, LoyaltyPointTransaction, Setting};
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class LoyaltyPointsService
{
    public function isEnabled(): bool
    {
        return Setting::where('key', 'loyalty_points_enabled')->value('plain_value') == '1';
    }

    protected function earnRate(): float
    {
        return (float) (Setting::where('key', 'loyalty_points_earn_rate')->value('plain_value') ?? 1);
    }

    protected function redeemValue(): float
    {
        return (float) (Setting::where('key', 'loyalty_points_redeem_value')->value('plain_value') ?? 0.01);
    }

    public function minRedeemPoints(): int
    {
        return (int) (Setting::where('key', 'loyalty_points_min_redeem')->value('plain_value') ?? 0);
    }

    /**
     * ✅ حساب عدد النقاط المكتسبة من طلب.
     * الأساس المعتمد: (قيمة المنتجات - الخصم) — بدون التوصيل والضريبة،
     * لأنه المنطقي تجارياً إنك تكافئ العميل على قيمة "الشراء الفعلي"
     * مش على رسوم التوصيل. لو بدك تغير الأساس، هون المكان الوحيد
     * اللي بتحتاج تعدله.
     */
    public function calculateEarnedPoints(Order $order): int
    {
        if (!$this->isEnabled()) {
            return 0;
        }

        $base = max(0, $order->total_price - $order->discount_amount);

        return (int) floor($base * $this->earnRate());
    }

    /**
     * القيمة المالية لعدد معين من النقاط عند استبدالها
     */
    public function pointsToCurrency(int $points): float
    {
        return round($points * $this->redeemValue(), 2);
    }

    /**
     * ✅ أقصى عدد نقاط يقدر العميل يستخدمها بهذا الطلب —
     * محكوم برصيده الحالي وبقيمة الطلب نفسه (ما تخليه يخصم أكثر من قيمة الطلب).
     */
    public function maxRedeemablePoints(User $user, float $orderAmountBeforePoints): int
    {
        $redeemValue = $this->redeemValue();
        if ($redeemValue <= 0) {
            return 0;
        }

        $maxByOrderValue = (int) floor($orderAmountBeforePoints / $redeemValue);

        return max(0, min($user->loyalty_points_balance, $maxByOrderValue));
    }

    /**
     * ✅ يتحقق من صلاحية عدد نقاط طلب الاستبدال ويرجع قيمتها بالعملة.
     * يرمي استثناء لو الطلب غير صالح (رصيد غير كافي، أقل من الحد الأدنى..)
     */
    public function validateAndCalculateRedeemDiscount(User $user, int $requestedPoints, float $orderAmountBeforePoints): float
    {
        if ($requestedPoints <= 0) {
            return 0;
        }

        if (!$this->isEnabled()) {
            throw new InvalidArgumentException(__('api.Loyalty_Points_Disabled'));
        }

        if ($requestedPoints < $this->minRedeemPoints()) {
            throw new InvalidArgumentException(__('api.Points_Below_Minimum_Redeem', ['min' => $this->minRedeemPoints()]));
        }

        if ($requestedPoints > $user->loyalty_points_balance) {
            throw new InvalidArgumentException(__('api.Insufficient_Points_Balance'));
        }

        $discountValue = $this->pointsToCurrency($requestedPoints);

        if ($discountValue > $orderAmountBeforePoints) {
            throw new InvalidArgumentException(__('api.Points_Exceed_Order_Value'));
        }

        return $discountValue;
    }

    /**
     * ✅ إضافة نقاط لرصيد العميل (تُستدعى عند اكتمال/تسليم الطلب)
     */
    public function addPoints(User $user, int $points, ?Order $order, string $description): void
    {
        if ($points <= 0) {
            return;
        }

        DB::transaction(function () use ($user, $points, $order, $description) {
            $user->increment('loyalty_points_balance', $points);
            $user->refresh();

            LoyaltyPointTransaction::create([
                'user_id' => $user->id,
                'order_id' => $order?->id,
                'type' => 'earn',
                'points' => $points,
                'balance_after' => $user->loyalty_points_balance,
                'description' => $description,
            ]);
        });
    }

    /**
     * ✅ خصم نقاط من رصيد العميل (تُستدعى عند تأكيد الطلب باستخدام نقاط كخصم)
     */
    public function redeemPoints(User $user, int $points, ?Order $order, string $description): void
    {
        if ($points <= 0) {
            return;
        }

        if ($user->loyalty_points_balance < $points) {
            throw new InvalidArgumentException(__('api.Insufficient_Points_Balance'));
        }

        DB::transaction(function () use ($user, $points, $order, $description) {
            $user->decrement('loyalty_points_balance', $points);
            $user->refresh();

            LoyaltyPointTransaction::create([
                'user_id' => $user->id,
                'order_id' => $order?->id,
                'type' => 'redeem',
                'points' => -$points,
                'balance_after' => $user->loyalty_points_balance,
                'description' => $description,
            ]);
        });
    }
}
