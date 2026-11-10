<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Commission — سجل عمولة منصة لتاجر أو مندوب.
 *
 * لماذا جدول منفصل عن عمود commission_rate على users؟
 *  - يدعم تغيير النسبة في تاريخ لاحق مع الحفاظ على التاريخ.
 *  - يدعم النِسَب المؤقتة (effective_to).
 *  - يدعم تعليق يوضح سبب التغيير ومن أجراه.
 *
 * النسبة الفعّالة تُقرأ من User::currentCommission($role).
 */
class Commission extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'role',
        'rate',
        'effective_from',
        'effective_to',
        'is_active',
        'note',
        'set_by',
    ];

    protected $casts = [
        'rate' => 'float',
        'is_active' => 'boolean',
        'effective_from' => 'date',
        'effective_to' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function setBy()
    {
        return $this->belongsTo(User::class, 'set_by');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where('effective_from', '<=', now()->toDateString())
            ->where(function ($q) {
                $q->whereNull('effective_to')->orWhere('effective_to', '>=', now()->toDateString());
            });
    }

    public function scopeForRole($query, string $role)
    {
        return $query->where('role', $role);
    }
}
