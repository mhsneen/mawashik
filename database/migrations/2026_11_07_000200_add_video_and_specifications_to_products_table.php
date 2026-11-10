<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * إضافة حقول الفيديو والمواصفات والموقع الإضافي على المنتجات.
 *
 * وفق البند الثالث من مواصفات "وسيط المواشي" شاشة إضافة منتج للتاجر تحتاج:
 *  - الاسم ✅ (موجود في product_translations)
 *  - التصنيف ✅ (موجود)
 *  - الصور ✅ (موجود في imagesstore)
 *  - الفيديو إن وُجد (نضيف video_url)
 *  - الوصف ✅ (موجود في product_translations)
 *  - السعر ✅ (موجود netPrice)
 *  - المواصفات (نضيف specifications كـ JSON)
 *  - الموقع (يُورَّث من التاجر — لا حقل على المنتج)
 *  - خيار الذبح وسعره ✅ (موجود)
 *  - الحالة ✅ (موجودة is_active + product_status)
 *
 * ملاحظة: specifications حقل JSON يدعم مواصفات متعددة كـ {key: value, key2: value2}.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // رابط فيديو المنتج (YouTube/Vimeo/MP4) — اختياري
            if (!Schema::hasColumn('products', 'video_url')) {
                $table->string('video_url')->nullable()->after('image');
            }

            // المواصفات كـ JSON (مثال: {"origin": "سعودي", "age": "2 سنة", "weight": "120kg"})
            if (!Schema::hasColumn('products', 'specifications')) {
                $table->json('specifications')->nullable()->after('video_url');
            }
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $columns = [];
            if (Schema::hasColumn('products', 'video_url')) {
                $columns[] = 'video_url';
            }
            if (Schema::hasColumn('products', 'specifications')) {
                $columns[] = 'specifications';
            }
            if (!empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};
