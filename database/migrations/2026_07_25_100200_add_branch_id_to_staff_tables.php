<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * إضافة branch_id لجدول الموظفين/الأدمن (لتحديد أي موظف تابع لأي فرع لاحقاً).
 *
 * بما أن اسم الجدول قد يختلف حسب مشروعك (employees / admins / staff)، الميجريشن يفحص الأسماء
 * الشائعة تلقائياً ويضيف العمود لأي جدول موجود فعلاً منها فقط - لن يفشل إن لم يجد أياً منها.
 *
 * إن كان اسم جدول الموظفين عندك مختلفاً، عدّل المصفوفة $candidateTables أدناه قبل التشغيل.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('users', 'branch_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->foreignId('branch_id')
                    ->nullable()
                    ->after('id')
                    ->constrained('branches')
                    ->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'branch_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropForeign(['branch_id']);
                $table->dropColumn('branch_id');
            });
        }
    }
};
