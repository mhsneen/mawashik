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
            $table->timestamp('prepared_at')->nullable()->after('status_id'); //تسجل وقت ما الطلب صار "تم التجهيز" (status 3)
            $table->timestamp('out_for_delivery_at')->nullable()->after('prepared_at');//تسجل وقت ما السائق استلم الطلب وطلع فيه (status 4).
            $table->timestamp('delivered_at')->nullable()->after('out_for_delivery_at');//تسجل وقت التسليم الفعلي
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['prepared_at', 'out_for_delivery_at', 'delivered_at']);
        });
    }
};
