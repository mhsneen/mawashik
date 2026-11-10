<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * إزالة حقول الجغرافيا من جدول products.
 *
 * السبب:
 *  في تطبيق "وسيط المواشي"، المنتج يرتبط بالتاجر (trader_id)،
 *  والتاجر يملك موقعاً ثابتاً (store_lat/store_lng + country_id/city_id/area_id/neighborhood_id).
 *  لذلك المنتج يرث موقعه الجغرافي من التاجر عبر العلاقة $product->trader->store_lat ... إلخ.
 *
 *  لا حاجة لتكرار نفس الحقول على المنتج — هذا كان تكراراً وسبب تعقيداً في الصيانة.
 *
 *  ملاحظة: لا نحذف lat/lng من جدول users (التاجر يحتفظ بـ store_lat/store_lng)
 *  ولا من جدول addresses (للعميل عناوين متعددة بإحداثياتها).
 */
return new class extends Migration
{
    public function up(): void
    {
{
    Schema::table('products', function (Blueprint $table) {

        // 1. حذف Foreign Keys أولاً
        foreach (['city_id', 'area_id', 'neighborhood_id'] as $column) {
            if (Schema::hasColumn('products', $column)) {
                $table->dropForeign([$column]);
            }
        }

        // 2. حذف Index
        $index = 'products_city_id_area_id_is_active_index';

        if (DB::getSchemaBuilder()->hasIndex('products', $index)) {
            $table->dropIndex($index);
        }

        // 3. حذف الأعمدة
        $columns = [
            'city_id',
            'area_id',
            'neighborhood_id',
            'lat',
            'lng',
        ];

        foreach ($columns as $column) {
            if (Schema::hasColumn('products', $column)) {
                $table->dropColumn($column);
            }
        }
    });
}
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('city_id')->nullable()->after('trader_id')->constrained()->nullOnDelete();
            $table->foreignId('area_id')->nullable()->after('city_id')->constrained()->nullOnDelete();
            $table->foreignId('neighborhood_id')->nullable()->after('area_id')->constrained()->nullOnDelete();
            $table->decimal('lat', 10, 7)->nullable()->after('neighborhood_id');
            $table->decimal('lng', 10, 7)->nullable()->after('lat');
            $table->index(['city_id', 'area_id', 'is_active']);
        });
    }
};
