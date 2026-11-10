<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * تبسيط جداول المناطق الجغرافية:
 *
 * 1) countries: حذف iso_code و phone_code — لا نحتاج كود الدولة في تطبيق محلي،
 *    التطبيق يتعامل مع أرقام الجوال كما هي مدخلة من المستخدم.
 *
 * 2) cities / areas / neighborhoods: حذف lat و lng — المناطق الإدارية هرمية
 *    (تُعرف بعلاقتها بالأب)، أما الإحداثيات الفعلية فتُخزَّن على المستخدم/التاجر
 *    فقط (store_lat/store_lng على جدول users).
 *
 * هذا التبسيط:
 *  - يقلل التكرار (لا نسجل إحداثيات بلّد وبلد، حي وحي).
 *  - يجعل الإدارة أبسط: المدير يضيف "حي العليا" باسمه فقط دون قلق عن الإحداثيات.
 *  - يترك الإحداثيات الحقيقية في مكانها الصحيح: على المستخدم/التاجر/العنوان.
 */
return new class extends Migration
{
    public function up(): void
    {
        // 1) إزالة iso_code و phone_code من countries
        Schema::table('countries', function (Blueprint $table) {
            if (Schema::hasColumn('countries', 'iso_code')) {
                $table->dropColumn('iso_code');
            }
            if (Schema::hasColumn('countries', 'phone_code')) {
                $table->dropColumn('phone_code');
            }
        });

        // 2) إزالة lat و lng من cities
        Schema::table('cities', function (Blueprint $table) {
            if (Schema::hasColumn('cities', 'lat')) {
                $table->dropColumn('lat');
            }
            if (Schema::hasColumn('cities', 'lng')) {
                $table->dropColumn('lng');
            }
        });

        // 3) إزالة lat و lng من areas
        Schema::table('areas', function (Blueprint $table) {
            if (Schema::hasColumn('areas', 'lat')) {
                $table->dropColumn('lat');
            }
            if (Schema::hasColumn('areas', 'lng')) {
                $table->dropColumn('lng');
            }
        });

        // 4) إزالة lat و lng من neighborhoods
        Schema::table('neighborhoods', function (Blueprint $table) {
            if (Schema::hasColumn('neighborhoods', 'lat')) {
                $table->dropColumn('lat');
            }
            if (Schema::hasColumn('neighborhoods', 'lng')) {
                $table->dropColumn('lng');
            }
        });
    }

    public function down(): void
    {
        Schema::table('countries', function (Blueprint $table) {
            $table->string('iso_code', 3)->nullable()->after('id');
            $table->string('phone_code', 5)->nullable()->after('iso_code');
        });

        Schema::table('cities', function (Blueprint $table) {
            $table->decimal('lat', 10, 7)->nullable()->after('country_id');
            $table->decimal('lng', 10, 7)->nullable()->after('lat');
        });

        Schema::table('areas', function (Blueprint $table) {
            $table->decimal('lat', 10, 7)->nullable()->after('city_id');
            $table->decimal('lng', 10, 7)->nullable()->after('lat');
        });

        Schema::table('neighborhoods', function (Blueprint $table) {
            $table->decimal('lat', 10, 7)->nullable()->after('area_id');
            $table->decimal('lng', 10, 7)->nullable()->after('lat');
        });
    }
};
