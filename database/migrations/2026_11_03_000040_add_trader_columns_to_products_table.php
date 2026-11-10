<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * إضافة أعمدة التاجر على products:
 *  - trader_id: صاحب المنتج (التاجر الذي يبيع)
 *  - is_approved: نشر المنتج يخضع لاعتماد الأدمن (مطابق للمواصفات: منتجات التاجر تخضع للمراجعة)
 *  - product_status: pending / approved / rejected / suspended
 *
 * ملاحظة: نضيف foreignId لـ trader_id بعد إنشاء العمود بـ migration سابق؟
 *  -> لا، نضيفه هنا فقط للحفاظ على الترتيب المنطقي.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('trader_id')->nullable()->after('id')->constrained('users')->nullOnDelete();

            $table->enum('product_status', ['pending', 'approved', 'rejected', 'suspended'])
                ->default('pending')
                ->after('trader_id')
                ->index();

            $table->timestamp('approved_at')->nullable()->after('product_status');
            $table->timestamp('rejected_at')->nullable()->after('approved_at');
            $table->string('rejection_reason')->nullable()->after('rejected_at');

            $table->index('trader_id');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['trader_id']);
            $table->dropIndex(['product_status']);
            $table->dropColumn([
                'trader_id',
                'product_status',
                'approved_at',
                'rejected_at',
                'rejection_reason',
            ]);
        });
    }
};
