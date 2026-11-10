<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * إضافة حقول إضافية للمتجر/المندوب على جدول users:
 *
 * 1) banner_image   — صورة بانر كبيرة للمتجر (تعرض في صفحة "حوش الأصيل")
 * 2) whatsapp_number — رقم واتساب منفصل (لو null يستخدم mobile)
 * 3) is_available    — تفعّيل/إيقاف استقبال الطلبات للمندوب (toggle في الرئيسية)
 *
 * مصممة لتطبيق "وسيط المواشي" حسب شاشات HTML.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'banner_image')) {
                $table->string('banner_image')->nullable()->after('image');
            }

            if (!Schema::hasColumn('users', 'whatsapp_number')) {
                $table->string('whatsapp_number', 30)->nullable()->after('mobile');
            }

            if (!Schema::hasColumn('users', 'is_available')) {
                $table->boolean('is_available')->default(true)->after('is_blocked')->index();
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'banner_image')) {
                $table->dropColumn('banner_image');
            }
            if (Schema::hasColumn('users', 'whatsapp_number')) {
                $table->dropColumn('whatsapp_number');
            }
            if (Schema::hasColumn('users', 'is_available')) {
                $table->dropIndex(['is_available']);
                $table->dropColumn('is_available');
            }
        });
    }
};
