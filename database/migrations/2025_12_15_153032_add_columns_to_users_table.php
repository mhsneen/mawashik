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
        Schema::table('users', function (Blueprint $table) {
            $table->string('mobile')->after('name')->unique();
            $table->string('image')->nullable();
            $table->boolean('is_active');
            $table->longtext('device_token')->nullable();
            $table->longtext('desktop_token')->nullable();
            $table->softDeletes($column = 'deleted_at', $precision = 0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('mobile');
            $table->dropColumn('image');
            $table->dropColumn('is_active');
            $table->dropColumn('device_token');
            $table->dropColumn('desktop_token');
            $table->dropColumn('deleted_at');
        });
    }
};
