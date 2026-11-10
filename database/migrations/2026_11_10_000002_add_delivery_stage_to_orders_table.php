<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * مراحل توصيل المندوب (البند: شاشة حالات التوصيل):
 *  accepted → heading_to_trader → picked_up → out_for_delivery → delivered
 *
 * status_id يبقى 3 (جاهز) حتى "الخروج للتوصيل" فيصير 4، ثم 5 عند التسليم.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('delivery_stage', 30)->nullable()->after('driver_picked_up_at');
            $table->index(['driver_id', 'status_id']);
            $table->index(['trader_id', 'status_id']);
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['driver_id', 'status_id']);
            $table->dropIndex(['trader_id', 'status_id']);
            $table->dropColumn('delivery_stage');
        });
    }
};
