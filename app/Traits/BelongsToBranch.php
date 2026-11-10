<?php

namespace App\Traits;

use App\Models\Branch;
use App\Services\Branches\BranchContext;

/**
 * Trait اختياري لأي Model يحتاج لاحقاً يرتبط بفرع (مثال: Order, Employee).
 *
 * غير مُفعّل على أي موديل حالياً. لما يُطلب فرع فعلي، فعّله على الموديل المطلوب بسطر واحد:
 *
 *   class Order extends Model {
 *       use BelongsToBranch;
 *   }
 *
 * وبإمكانك عندها استدعاء Order::currentBranch() لفلترة الطلبات حسب الفرع الحالي تلقائياً،
 * أو ->branch للوصول لعلاقة الفرع مباشرة. لا تفعّله الآن على شيء - فقط جاهز عند الحاجة.
 */
trait BelongsToBranch
{
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function scopeCurrentBranch($query)
    {
        return $query->where('branch_id', BranchContext::currentId());
    }

    public function scopeForBranch($query, int $branchId)
    {
        return $query->where('branch_id', $branchId);
    }

    protected static function bootBelongsToBranch(): void
    {
        static::creating(function ($model) {
            if (empty($model->branch_id)) {
                $model->branch_id = BranchContext::currentId();
            }
        });
    }
}
