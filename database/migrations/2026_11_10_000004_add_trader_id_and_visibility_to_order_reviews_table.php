<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * ربط تقييم الطلب بالتاجر (تقييم التاجر: 5 نجوم + تعليق) + إمكانية إخفاء التقييم من الإدارة.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_reviews', function (Blueprint $table) {
            $table->unsignedBigInteger('trader_id')->nullable()->after('order_id');
            $table->boolean('is_hidden')->default(false)->after('comment');
            $table->foreign('trader_id')->references('id')->on('users')->nullOnDelete();
            $table->index(['trader_id', 'is_hidden']);
        });

        // تعبئة التقييمات القديمة من الطلب نفسه
        DB::statement(
            'UPDATE order_reviews SET trader_id = '
            . '(SELECT orders.trader_id FROM orders WHERE orders.id = order_reviews.order_id) '
            . 'WHERE trader_id IS NULL'
        );
    }

    public function down(): void
    {
        Schema::table('order_reviews', function (Blueprint $table) {
            $table->dropForeign(['trader_id']);
            $table->dropIndex(['trader_id', 'is_hidden']);
            $table->dropColumn(['trader_id', 'is_hidden']);
        });
    }
};
