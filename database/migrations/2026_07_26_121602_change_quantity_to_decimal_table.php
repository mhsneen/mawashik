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
        // جدول المنتجات
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('quantity', 10, 2)->nullable()->change();
        });

        // جدول الفيرنتات
        Schema::table('product_variants', function (Blueprint $table) {
            $table->decimal('quantity', 10, 2)->nullable()->change();
        });

        // جدول العلاقة (attribute_value_product) - pivot quantity
        Schema::table('attribute_value_product', function (Blueprint $table) {
            $table->decimal('quantity', 10, 2)->nullable()->change();
        });

        // جدول OrderItem
        Schema::table('order_items', function (Blueprint $table) {
            $table->decimal('quantity', 10, 2)->default(1)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
       Schema::table('products', function (Blueprint $table) {
            $table->integer('quantity')->nullable()->change();
        });
        Schema::table('product_variants', function (Blueprint $table) {
            $table->integer('quantity')->nullable()->change();
        });
        Schema::table('attribute_value_product', function (Blueprint $table) {
            $table->integer('quantity')->nullable()->change();
        });
        Schema::table('order_items', function (Blueprint $table) {
            $table->integer('quantity')->default(1)->change();
        });
    }
};
