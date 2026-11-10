<?php

namespace App\Observers;

use App\Models\User;
use App\Models\Wallet;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UserObserver
{
    public function created(User $user): void
    {
        try {
            // نتحقق أن نوع المستخدم يستحق محفظة (عميل/تاجر/مندوب)
            $walletableTypes = ['client', 'trader', 'driver'];

            if (!in_array($user->user_type, $walletableTypes)) {
                return; // الموظفون والأدمن لا يحتاجون محفظة
            }

            // firstOrCreate لتفادي التكرار
            Wallet::firstOrCreate(
                ['user_id' => $user->id],
                ['balance' => 0, 'currency' => 'SAR']
            );
        } catch (\Throwable $e) {
            Log::error("Failed to create wallet for user {$user->id}: {$e->getMessage()}");
        }
    }

    public function updated(User $user): void
    {
        //
    }

    public function deleted(User $user): void
    {
        // اختياري: عند حذف المستخدم نوقف المحفظة (soft delete) أو نحذفها
        // ما ننصح بحذفها فعلياً للحفاظ على السجلات المالية
    }

    public function restored(User $user): void {}
    public function forceDeleted(User $user): void {}
}
