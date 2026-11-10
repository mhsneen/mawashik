<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('otp_codes', function (Blueprint $table) {
           $table->id();
            $table->morphs('verifiable'); // verifiable_id و verifiable_type (يربط الكود بالمستخدم أو أي موديل آخر)
            $table->string('purpose'); // الهدف: 'registration', 'login', 'password_reset'
            $table->string('method');  // طريقة الإرسال: 'sms', 'whatsapp', 'email'
            $table->string('contact'); // رقم الجوال أو الإيميل المستهدف
            $table->string('code');    // الرمز (4 خانات)
            $table->boolean('is_used')->default(false); // هل تم استخدامه؟
            $table->timestamp('expires_at'); // تاريخ الانتهاء
             // عداد محاولات التحقق الفاشلة لكل كود — لمنع Brute Force
            $table->unsignedTinyInteger('attempts')->default(0);

            // فهرس مركّب يسرّع بحث OtpService::verify() و generateAndSend()
            $table->index(['verifiable_id', 'verifiable_type', 'purpose', 'is_used'], 'otp_codes_lookup_index');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('otp_codes');
    }
};
