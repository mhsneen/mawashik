<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('unit_id')
                ->nullable()
                ->after('quantity')
                ->constrained('units')
                ->nullOnDelete();
        });

        // السماح بترك الكمية فارغة = مخزون غير محدود / غير متابَع
        DB::statement('ALTER TABLE products MODIFY quantity INT NULL DEFAULT NULL');
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['unit_id']);
            $table->dropColumn('unit_id');
        });

        DB::statement('ALTER TABLE products MODIFY quantity INT NOT NULL DEFAULT 1');
    }
};
