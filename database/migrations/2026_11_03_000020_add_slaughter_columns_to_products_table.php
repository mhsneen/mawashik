<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * خدمة الذبح على المنتجات:
 *  - slaughter_available: هل يدعم المنتج خدمة الذبح؟
 *  - slaughter_price: سعر الذبح الاختياري (إن كان null يستخدم السعر الافتراضي من الإعدادات)
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->boolean('slaughter_available')->default(false)->after('is_active');
            $table->decimal('slaughter_price', 10, 2)->nullable()->after('slaughter_available');

            // إشارة إن كان الذبح إلزامياً (مثال: حيوان حي يجب ذبحه قبل التسليم)
            $table->boolean('slaughter_required')->default(false)->after('slaughter_price');

            $table->index('slaughter_available');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['slaughter_available']);
            $table->dropColumn(['slaughter_available', 'slaughter_price', 'slaughter_required']);
        });
    }
};
