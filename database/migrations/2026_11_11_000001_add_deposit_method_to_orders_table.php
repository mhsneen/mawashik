<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * طريقة دفع العربون: cash (يُحصَّل مع الطلب نقداً) | online (بنكي/بوابة) | wallet (من المحفظة).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('deposit_method', 10)->nullable()->after('deposit_status');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('deposit_method');
        });
    }
};
