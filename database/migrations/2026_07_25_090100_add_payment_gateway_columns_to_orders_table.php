<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // payment_method العمود موجود مسبقاً (اسم البوابة كنص حر) - نضيف تتبّع دقيق للحالة
            $table->string('payment_gateway_code')->nullable()->after('payment_method'); // hyperpay/stripe/...
            $table->string('payment_status')->default('pending')->after('payment_gateway_code'); // pending, paid, failed, refunded, cancelled
            $table->string('payment_reference')->nullable()->after('payment_status'); // checkoutId / session id / invoice id ...
            $table->timestamp('paid_at')->nullable()->after('payment_reference');
            $table->text('payment_gateway_response')->nullable()->after('paid_at'); // آخر رد خام من البوابة (JSON) لأغراض المراجعة والدعم الفني
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'payment_gateway_code',
                'payment_status',
                'payment_reference',
                'paid_at',
                'payment_gateway_response',
            ]);
        });
    }
};
