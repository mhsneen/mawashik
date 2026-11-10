<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * خدمة الذبح على عناصر الطلب:
 * snapshot لاختيار العميل في وقت الطلب (يُحفظ كقيمة فعلية ولا يتأثر بتغيير سعر الذبح لاحقاً).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->boolean('slaughter_selected')->default(false)->after('total_price');
            $table->decimal('slaughter_price', 10, 2)->default(0)->after('slaughter_selected');
            $table->decimal('slaughter_total', 10, 2)->default(0)->after('slaughter_price');
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn(['slaughter_selected', 'slaughter_price', 'slaughter_total']);
        });
    }
};
