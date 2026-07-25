<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;

class DiscountCode extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'discount_percentage',
        'start_date',
        'end_date',
        'usage_type',
        'max_usage',
        'usage_count',
        'is_active',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'is_active' => 'boolean',
    ];


    public function getStatusAttribute()
    {
        $today = Carbon::today();

        if ($this->is_active == 1) {
            return '<span class="label text-info d-flex">
                <div class="dot-label bg-info ml-1"></div>' . __('dashboard.Stopped') . '</span>';
        }

        if ($today->lt($this->start_date)) {
            return '<span class="label text-warning d-flex">
                <div class="dot-label bg-warning ml-1"></div>' . __('dashboard.Pending') . '</span>';
        }

        // إذا انتهت صلاحيته
        if ($today->gt($this->end_date)) {
            return '<span class="label text-danger d-flex">
                <div class="dot-label bg-danger ml-1"></div>' . __('dashboard.Finished') . '</span>';
        }

        // لو عدد مرات الاستخدام محدود وتجاوز الحد
        if (($this->usage_type === 'limited' || $this->usage_type === 'once') && $this->max_usage !== null) {
            if ($this->usage_count >= $this->max_usage) {
                return '<span class="label text-danger d-flex">
                <div class="dot-label bg-danger ml-1"></div>' . __('dashboard.Finished') . '</span>';
            }
        }

        return '<span class="label text-success d-flex">
                <div class="dot-label bg-success ml-1"></div>' . __('dashboard.Active') . '</span>';
    }

    public function scopeFilterDashbordDiscountCodes($query, array $filters)
    {
        $query->when($filters['search_status'] ?? false, function ($query, $search) {
            $today = Carbon::today();

            switch ($search) {
                case 'Stopped':
                    $query->where('is_active', 1);
                    break;

                case 'Pending':
                    $query->whereDate('start_date', '>', $today)->where('is_active', '!=', 1);
                    break;

                case 'Finished':
                    $query->where(function ($q) use ($today) {
                        $q->whereDate('end_date', '<', $today)
                            ->orWhere(function ($sub) {
                                $sub->whereIn('usage_type', ['limited', 'once'])
                                    ->whereNotNull('max_usage')
                                    ->whereColumn('usage_count', '>=', 'max_usage');
                            });
                    });
                    break;

                case 'Active':
                    $query->whereDate('start_date', '<=', $today)
                        ->whereDate('end_date', '>=', $today)
                        ->where(function ($q) {
                            $q->whereNotIn('usage_type', ['limited', 'once'])
                                ->orWhereColumn('usage_count', '<', 'max_usage');
                        })
                        ->where('is_active', '!=', 1);
                    break;
            }
        });
    }

    // العلاقة مع الاستخدامات
    public function usages()
    {
        return $this->hasMany(DiscountCodeUsage::class, 'discount_code_id');
    }

    // تحقق من صلاحية الكوبون
    public function isValid()
    {
        $now = Carbon::now();


        if ($this->is_active == 1) return false;
        if (($this->usage_type === 'limited' || $this->usage_type === 'once') && $this->max_usage !== null) {
            if ($this->usage_count >= $this->max_usage) {
                return false;
            }
        }
        if ($now->lt($this->start_date) || $now->gt($this->end_date)) return false;

        return true;
    }

    // حساب قيمة الخصم على مبلغ معين
    public function calculateDiscount($amount)
    {
        if (!$this->isValid()) return 0;

        return ($amount * $this->discount_percentage) / 100;
    }

    // زيادة عدد الاستخدام
    public function incrementUsage()
    {
        $this->increment('usage_count');
    }
}
