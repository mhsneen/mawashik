<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * جدول العمولات الديناميكي:
 * يسمح بتعيين نسبة عمولة مختلفة لكل تاجر أو مندوب، وتتبع تاريخها.
 *
 * لماذا جدول منفصل بدلاً من عمود واحد على users؟
 *  - يمكّن الأدمن من تغيير النسبة في تاريخ لاحق مع الحفاظ على تاريخ القديم.
 *  - يدعم النسبة الافتراضية من الإعدادات، مع تجاوزها لكل مستخدم.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('commissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // الدور الذي تنطبق عليه هذه النسبة: تاجر أو مندوب
            $table->enum('role', ['trader', 'driver']);

            // نسبة العمولة كنسبة مئوية (مثال: 5.00 = 5%)
            $table->decimal('rate', 5, 2);

            // تاريخ بدء نفاذ هذه النسبة (للأرشفة التاريخية)
            $table->date('effective_from')->default(now());

            // تاريخ انتهاء النسبة (إن وجد) — يسمح بنِسَب مؤقتة
            $table->date('effective_to')->nullable();

            $table->boolean('is_active')->default(true);

            $table->string('note')->nullable();
            $table->foreignId('set_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();

            $table->index(['user_id', 'role', 'is_active']);
            $table->index('effective_from');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('commissions');
    }
};
