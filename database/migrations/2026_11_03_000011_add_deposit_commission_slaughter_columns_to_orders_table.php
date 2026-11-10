<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * إضافة أعمدة العربون + العمولات + الذبح على جدول orders:
 *
 *  - deposit_amount: قيمة العربون المدفوع عند إنشاء الطلب.
 *  - remaining_amount: المبلغ المتبقي الذي يُدفع عند الاستلام.
 *  - slaughter_total: إجمالي خدمة الذبح في الطلب.
 *  - commission_trader: قيمة عمولة المنصة من التاجر (محسوبة وقت إنشاء الطلب).
 *  - commission_driver: قيمة عمولة المندوب (محسوبة وقت التسليم).
 *  - pickup_type: استلام من التاجر أو توصيل للعميل.
 *  - trader_id: التاجر صاحب المنتج (في حال كان المنتج متعدد التجار).
 *
 * ملاحظة معمارية:
 *  حالياً نفترض طلب واحد = تاجر واحد (مطابق للمواصفات: العميل يطلب منتج من تاجر).
 *  لو لاحقاً صار سلة من تجار متعددين، يجب تقسيم الطلب لطلبات فرعية (split orders).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // 0 = توصيل للعميل، 1 = استلام من التاجر
            $table->enum('pickup_type', ['delivery', 'pickup'])->default('delivery')->after('delivery_type');

            // التاجر صاحب الطلب
            $table->foreignId('trader_id')->nullable()->after('pickup_type')->constrained('users')->nullOnDelete();

            // مبالغ تفصيلية
            $table->decimal('deposit_amount', 10, 2)->default(0)->after('final_price');
            $table->decimal('remaining_amount', 10, 2)->default(0)->after('deposit_amount');
            $table->decimal('slaughter_total', 10, 2)->default(0)->after('remaining_amount');

            // العمولات
            $table->decimal('commission_trader', 10, 2)->default(0)->after('slaughter_total');
            $table->decimal('commission_driver', 10, 2)->default(0)->after('commission_trader');

            // حالة العربون: paid / refunded / partial_refund
            $table->enum('deposit_status', ['none', 'paid', 'refunded', 'partial_refund'])
                ->default('none')
                ->after('commission_driver');

            $table->timestamp('deposit_paid_at')->nullable()->after('deposit_status');
            $table->timestamp('deposit_refunded_at')->nullable()->after('deposit_paid_at');

            // أعمدة تتبع سائق منزلق (ذهب للتاجر ثم العميل)
            $table->timestamp('driver_at_trader_at')->nullable()->after('out_for_delivery_at');
            $table->timestamp('driver_picked_up_at')->nullable()->after('driver_at_trader_at');

            // كود تسليم من التاجر (بالإضافة لـ delivery_pin الخاص بالعميل)
            $table->string('trader_pin', 6)->nullable()->after('delivery_pin');

            $table->index('trader_id');
            $table->index('deposit_status');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['trader_id']);
            $table->dropIndex(['deposit_status']);
            $table->dropColumn([
                'pickup_type',
                'trader_id',
                'deposit_amount',
                'remaining_amount',
                'slaughter_total',
                'commission_trader',
                'commission_driver',
                'deposit_status',
                'deposit_paid_at',
                'deposit_refunded_at',
                'driver_at_trader_at',
                'driver_picked_up_at',
                'trader_pin',
            ]);
        });
    }
};
