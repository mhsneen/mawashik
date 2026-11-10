<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * ❗ إصلاح حرج:
 * الكود القديم لم يكن يحتوي على transaction_type ولا related_user_id في $fillable،
 * رغم أن الميغريشن 2026_11_03_000012 أضاف العمودين فعلياً في قاعدة البيانات.
 * النتيجة: أي استدعاء WalletTransaction::create([...]) يحاول تعبئة هذه الحقول
 * يتم تجاهله بصمت (mass-assignment protection)، فتبقى كل الحركات بـ transaction_type='order_payment'
 * (القيمة الافتراضية في الـ DB)، ولا تُربط بأي مستخدم.
 *
 * هذا كان يكسر:
 *  - TraderWalletApiController::balance() — استعلام pending_settlement يرجع 0 دائماً.
 *  - TraderWalletApiController::transactions() — فلترة transaction_type لا تطابق أي شيء.
 *  - CommissionService — عمولات التجار والمندوبين لا تُسجّل بنوعها الصحيح.
 *  - تقارير لوحة التحكم المالية (لو تفلتر حسب transaction_type).
 */
class WalletTransaction extends Model
{
    protected $fillable = [
        'wallet_id',
        'order_id',
        'type',                  // credit | debit | refund
        'amount',
        'description',
        'transaction_type',      // ✅ أُضيف — أصناف الحركة (commission_trader, deposit_paid, ...)
        'related_user_id',       // ✅ أُضيف — من الجهة المرتبطة (التاجر/المندوب)
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * الجهة المرتبطة بالحركة (تاجر أو مندوب) — تستخدم لربط العمولات.
     */
    public function relatedUser()
    {
        return $this->belongsTo(User::class, 'related_user_id');
    }


    public function getTypeTranslatedAttribute()
    {
        return match ($this->type) {
            'credit' => __('dashboard.Credit'),
            'debit'  => __('dashboard.Debit'),
            'refund' => __('dashboard.Refund'),
            default   => $this->type,
        };
    }

    public function getTypeColorAttribute()
    {
        return match ($this->type) {
            'credit' => 'green',
            'debit'  => 'red',
            'refund' => 'orange',
            default  => 'gray',
        };
    }


    public function getAmountSignedAttribute()
    {
        return match ($this->type) {
            'credit', 'refund' => ' + ' . number_format($this->amount, 2),
            'debit' => ' - ' . number_format($this->amount, 2),
            default => number_format($this->amount, 2),
        };
    }

    /**
     * ✅ نطاق استعلام جديد لتسهيل الفلترة حسب نوع الحركة.
     * مثال: WalletTransaction::ofType('commission_trader')->where('related_user_id', $trader->id)->sum('amount');
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('transaction_type', $type);
    }

    /**
     * ✅ نطاق استعلام جديد: فقط حركات مستخدم محدد (تاجر/مندوب).
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->where('related_user_id', $userId);
    }
}
