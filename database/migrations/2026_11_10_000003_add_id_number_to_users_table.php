<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * رقم الهوية للتاجر والمندوب (مطلوب في شاشتي التسجيل حسب المتطلبات: "رقم الهوية").
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('id_number', 20)->nullable()->after('mobile');
            $table->index('id_number');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['id_number']);
            $table->dropColumn('id_number');
        });
    }
};
