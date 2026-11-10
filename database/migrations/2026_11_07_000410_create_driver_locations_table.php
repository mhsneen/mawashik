<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * جدول driver_locations — لتتبع موقع المندوب الحي (Live Tracking).
 *
 * كل سجل = آخر موقع معروف لمندوب معيّن (unique on driver_id).
 *  - يُحدَّث من تطبيق المندوب كل بضع ثوانٍ أثناء التوصيل (POST /api/v1/driver/updateLocation).
 *  - يُقرأ من تطبيق العميل لعرض موقع المندوب على الخريطة في شاشة "تتبع الطلب".
 *
 * لا نخزّن سجلّ تاريخي كامل للمواقع (للخصصوصية + الأداء) — فقط آخر موقع.
 * لو احتجت تاريخ، يمكن إضافة جدول driver_location_history لاحقاً.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('driver_locations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('driver_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->decimal('lat', 10, 7);
            $table->decimal('lng', 10, 7);
            $table->decimal('heading', 5, 2)->nullable(); // اتجاه الحركة بالدرجات (0-360)
            $table->decimal('speed', 6, 2)->nullable();    // السرعة km/h
            $table->timestamp('located_at')->nullable();   // وقت آخر تحديث من الجهاز
            $table->timestamps();

            $table->index(['lat', 'lng']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('driver_locations');
    }
};
