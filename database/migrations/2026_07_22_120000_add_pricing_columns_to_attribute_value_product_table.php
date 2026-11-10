<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * هذه الأعمدة تُستخدم فقط للقيم من نوع "إضافة/Modifier"
     * (يعني قيم تابعة لخاصية is_variation = false).
     * لو القيمة تابعة لخاصية variation، تبقى هذه الأعمدة فارغة
     * لأن السعر/الكمية بتصير عن طريق product_variants بدلها.
     */
    public function up(): void
    {
        Schema::table('attribute_value_product', function (Blueprint $table) {
            $table->decimal('price', 10, 2)->default(0)->after('attribute_value_id');
            $table->integer('quantity')->nullable()->after('price'); // null = غير محدود
            $table->boolean('is_default')->default(false)->after('quantity');
            $table->unsignedInteger('sort_order')->default(0)->after('is_default');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attribute_value_product', function (Blueprint $table) {
            $table->dropColumn(['price', 'quantity', 'is_default', 'sort_order']);
        });
    }
};
