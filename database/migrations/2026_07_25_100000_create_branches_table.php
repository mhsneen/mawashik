<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * جدول الفروع - أساس نظام Multi-Branch.
 *
 * الحقول المترجَمة (الاسم، العنوان) لا تُخزَّن هنا، بل في جدول branch_translations
 * حسب اتفاقية حزمة astrotomic/laravel-translatable (راجع الميجريشن التالية).
 *
 * فلسفة التصميم: المتجر الحالي "فرع رئيسي واحد" (is_main = true) يُنشأ تلقائياً عبر BranchSeeder.
 * كل شيء في المتجر يستمر يشتغل بدون أي تغيير في التجربة، لكن البنية التحتية جاهزة بحيث إضافة
 * فرع ثاني لاحقاً = صف جديد في هذا الجدول + شاشة اختيار فرع، وليس إعادة هيكلة للمشروع.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('branches')) {
            return;
        }

        Schema::create('branches', function (Blueprint $table) {
            $table->id();
            $table->string('phone')->nullable();
            $table->decimal('lat', 10, 7)->nullable();
            $table->decimal('lng', 10, 7)->nullable();

            // فرع رئيسي واحد فقط يكون true - يُستخدم كقيمة افتراضية طالما لم يُفعَّل نظام
            // الفروع فعلياً (اختيار فرع من المستخدم/الأدمن).
            $table->boolean('is_main')->default(false);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('branches');
    }
};
