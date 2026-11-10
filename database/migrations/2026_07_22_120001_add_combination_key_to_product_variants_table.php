<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * combination_key = القيم (attribute_value_id) المكوّنة لهذا الـ variant
     * مرتبة ومفصولة بـ "_" مثلاً: "5_12" — تُستخدم كمفتاح فريد لكل تركيبة
     * ضمن نفس المنتج، بدل الاعتماد على استعلامات whereHas معقدة.
     */
    public function up(): void
    {
        Schema::table('product_variants', function (Blueprint $table) {
            $table->string('combination_key')->nullable()->after('product_id');
            $table->unique(['product_id', 'combination_key'], 'pv_product_combo_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_variants', function (Blueprint $table) {
            $table->dropUnique('pv_product_combo_unique');
            $table->dropColumn('combination_key');
        });
    }
};
