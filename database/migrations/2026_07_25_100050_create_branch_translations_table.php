<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * جدول ترجمات الفرع حسب اتفاقية astrotomic/laravel-translatable:
 * اسم الجدول = {اسم_الجدول_الأصلي بالمفرد}_translations، ومفتاح أجنبي {اسم_المفرد}_id.
 *
 * كل صف = ترجمة فرع واحد بلغة واحدة (locale). إضافة لغة جديدة مستقبلاً = صف جديد فقط،
 * بدون أي تعديل على المخطط (schema).
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('branch_translations')) {
            return;
        }

        Schema::create('branch_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches')->cascadeOnDelete();
            $table->string('locale')->index();

            $table->string('name');
            $table->text('address')->nullable();

            $table->unique(['branch_id', 'locale']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('branch_translations');
    }
};
