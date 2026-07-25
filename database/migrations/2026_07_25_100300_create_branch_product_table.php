<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * جدول pivot يربط المنتجات بالفروع - يسمح لاحقاً بمخزون/سعر مستقل لكل فرع لنفس المنتج،
 * دون تكرار المنتج نفسه ودون أي تعديل على جدول products الحالي.
 *
 * غير مُستخدم حالياً (المتجر فرع واحد) - جاهز فقط لليوم اللي يُطلب فيه مخزون منفصل بالفروع.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('products') || Schema::hasTable('branch_product')) {
            return;
        }

        Schema::create('branch_product', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->unsignedInteger('stock_quantity')->nullable(); // null = يتبع المخزون العام
            $table->decimal('price_override', 10, 2)->nullable();  // null = يتبع سعر المنتج الأساسي
            $table->boolean('is_available')->default(true);
            $table->timestamps();

            $table->unique(['branch_id', 'product_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('branch_product');
    }
};
