<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * إضافة branch_id لجدول الطلبات - العمود Nullable وبدون أي قيد يمنع النظام الحالي من الشغل.
 * BranchSeeder سيملأه تلقائياً بالفرع الرئيسي لكل الطلبات الموجودة، فلا يوجد طلب "بدون فرع".
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('orders') || Schema::hasColumn('orders', 'branch_id')) {
            return;
        }

        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('branch_id')
                ->nullable()
                ->after('id')
                ->constrained('branches')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('orders') || !Schema::hasColumn('orders', 'branch_id')) {
            return;
        }

        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['branch_id']);
            $table->dropColumn('branch_id');
        });
    }
};
