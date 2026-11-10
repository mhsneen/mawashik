<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // كم نقطة اتحسبت واتضافت للعميل مقابل هذا الطلب (بعد التسليم)
            $table->unsignedInteger('earned_points')->default(0)->after('delivered_at');
            // كم نقطة استخدمها العميل كخصم بهذا الطلب
            $table->unsignedInteger('redeemed_points')->default(0)->after('earned_points');
            // القيمة المالية لهذا الخصم (محسوبة وقت الطلب، snapshot ثابت)
            $table->decimal('redeemed_points_value', 10, 2)->default(0)->after('redeemed_points');
            // يمنع إضافة النقاط أكثر من مرة لنفس الطلب لو صار أي تحديث إضافي
            $table->timestamp('points_awarded_at')->nullable()->after('redeemed_points_value');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['earned_points', 'redeemed_points', 'redeemed_points_value', 'points_awarded_at']);
        });
    }
};
