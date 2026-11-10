<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * إضافة نوع الحركة لجدول wallet_transactions
 * ليتم التمييز بين: عمولة، عربون، استرداد عربون، تسوية، إيداع، دفع طلب.
 *
 * هذا ضروري للتقارير المالية في لوحة التحكم:
 *  - تقرير العمولات (commission_trader / commission_driver)
 *  - تقرير العربون (deposit / deposit_refund)
 *  - تسويات المندوب (driver_settlement)
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('wallet_transactions', function (Blueprint $table) {
            $table->enum('transaction_type', [
                'order_payment',        // دفع طلب من المحفظة
                'order_refund',         // استرداد بعد إلغاء
                'commission_trader',    // عمولة تستحق للتاجر
                'commission_driver',    // عمولة تستحق للمندوب
                'deposit_paid',         // عربون محجوز
                'deposit_refunded',     // عربون مُسترد للعميل
                'deposit_forfeited',    // عربون مُحتجز للمنصة (إلغاء متأخر)
                'deposit_released',     // عربون رُفض للمحفظة عند التسليم
                'withdrawal_request',   // طلب سحب
                'withdrawal_approved',  // سحب معتمد
                'withdrawal_rejected',   // سحب مرفوض (يُعاد للمحفظة)
                'admin_deposit',        // إيداع يدوي من الأدمن
                'admin_deduction',      // خصم يدوي
                'settlement',           // تسوية عامة
            ])->default('order_payment')->after('type');

            // معرف الجهة المرتبطة (التاجر / المندوب) لربط العمولة بطرفها
            $table->foreignId('related_user_id')->nullable()->after('order_id')->constrained('users')->nullOnDelete();

            $table->index(['wallet_id', 'transaction_type']);
            $table->index('related_user_id');
        });
    }

    public function down(): void
    {
        Schema::table('wallet_transactions', function (Blueprint $table) {
            $table->dropIndex(['wallet_id', 'transaction_type']);
            $table->dropIndex(['related_user_id']);
            $table->dropColumn(['transaction_type', 'related_user_id']);
        });
    }
};
