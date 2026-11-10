<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * إضافة أعمدة التاجر على جدول users:
 *  - trader_status: حالة اعتماد التاجر (pending / approved / rejected / suspended)
 *  - commission_rate: نسبة العمولة على مبيعات التاجر (إن كانت فارغة تستخدم الإعداد الافتراضي)
 *  - commercial_register: السجل التجاري
 *  - store_lat / store_lng: موقع متجر التاجر على الخريطة
 *  - store_address: نص العنوان
 *
 * ملاحظة: رخصة + هوية المندوب تُضاف في migration منفصل خاص بالمندوب
 * (2026_11_03_000002_add_driver_license_to_users_table) لفصل المسؤوليات.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('trader_status', ['pending', 'approved', 'rejected', 'suspended'])
                ->nullable()
                ->after('is_blocked')
                ->index();

            $table->decimal('commission_rate', 5, 2)->nullable()->after('trader_status');

            $table->string('commercial_register', 100)->nullable()->after('commission_rate');

            $table->decimal('store_lat', 10, 7)->nullable()->after('commercial_register');
            $table->decimal('store_lng', 10, 7)->nullable()->after('store_lat');
            $table->string('store_address')->nullable()->after('store_lng');

            // التاريخ الفعلي للاعتماد (يخدم التقارير)
            $table->timestamp('approved_at')->nullable()->after('store_address');
            $table->timestamp('rejected_at')->nullable()->after('approved_at');

            // سبب الرفض/الإيقاف (للمسؤول يوضح سبب القرار)
            $table->string('admin_status_note')->nullable()->after('rejected_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['trader_status']);
            $table->dropColumn([
                'trader_status',
                'commission_rate',
                'commercial_register',
                'store_lat',
                'store_lng',
                'store_address',
                'approved_at',
                'rejected_at',
                'admin_status_note',
            ]);
        });
    }
};
