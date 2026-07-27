<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // كود التسليم (4 أرقام) يُنشأ مع الطلب ويستخدمه السائق للتأكيد مع العميل
            $table->string('delivery_pin', 4)->nullable()->after('driver_id');
            // هل دفع العميل للسائق (تسجّل عند تأكيد التسليم)
            $table->boolean('payment_collected')->default(false)->after('delivery_pin');
            // ملاحظة إضافية من السائق
            $table->text('driver_note')->nullable()->after('payment_collected');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['delivery_pin', 'payment_collected', 'driver_note']);
        });
    }
};
