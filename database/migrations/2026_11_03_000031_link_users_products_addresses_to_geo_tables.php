<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * ربط المستخدمين بالمناطق الجغرافية:
 *  - العميل: حي/منطقة/مدينة افتراضية
 *  - التاجر: موقع المتجر (حي/منطقة/مدينة) — تستخدم لتصفية المنتجات حسب المنطقة
 *  - المندوب: نطاق عمله المفضل (حي/منطقة/مدينة) — يستخدم في إسناد الطلبات
 */
return new class extends Migration
{
    public function up(): void
    {
            /*Schema::table('users', function (Blueprint $table) {
            $table->foreignId('country_id')->nullable()->after('branch_id')->constrained()->nullOnDelete();
            $table->foreignId('city_id')->nullable()->after('country_id')->constrained()->nullOnDelete();
            $table->foreignId('area_id')->nullable()->after('city_id')->constrained()->nullOnDelete();
            $table->foreignId('neighborhood_id')->nullable()->after('area_id')->constrained()->nullOnDelete();

            $table->index(['city_id', 'area_id']);
        });*/

        // ربط المنتجات بالحي (للظهور على الخريطة داخل نطاق حي معين)
        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('city_id')->nullable()->after('trader_id')->constrained()->nullOnDelete();
            $table->foreignId('area_id')->nullable()->after('city_id')->constrained()->nullOnDelete();
            $table->foreignId('neighborhood_id')->nullable()->after('area_id')->constrained()->nullOnDelete();

            // إحداثيات المنتج على الخريطة (من متجر التاجر، أو مخصصة)
            $table->decimal('lat', 10, 7)->nullable()->after('neighborhood_id');
            $table->decimal('lng', 10, 7)->nullable()->after('lat');

            $table->index(['city_id', 'area_id', 'is_active']);
        });

        // ربط العنوان بالحي/المنطقة/المدينة
        Schema::table('addresses', function (Blueprint $table) {
            $table->foreignId('city_id')->nullable()->after('user_id')->constrained()->nullOnDelete();
            $table->foreignId('area_id')->nullable()->after('city_id')->constrained()->nullOnDelete();
            $table->foreignId('neighborhood_id')->nullable()->after('area_id')->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('addresses', function (Blueprint $table) {
            $table->dropColumn(['city_id', 'area_id', 'neighborhood_id']);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['city_id', 'area_id', 'is_active']);
            $table->dropColumn(['city_id', 'area_id', 'neighborhood_id', 'lat', 'lng']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['city_id', 'area_id']);
            $table->dropColumn(['country_id', 'city_id', 'area_id', 'neighborhood_id']);
        });
    }
};
