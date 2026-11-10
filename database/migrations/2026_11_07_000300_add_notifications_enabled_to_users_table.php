<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * إضافة عمود notifications_enabled لجدول المستخدمين.
 *
 * - يسمح لكل مستخدم بالتحكم بتلقي الإشعارات (FCM, Email, SMS).
 * - القيمة الافتراضية true (يمكن للمستخدم تعطيلها من إعدادات الحساب).
 *
 * يُستخدم في:
 *  - FCMServices قبل إرسال إشعار: التحقق من $user->notifications_enabled
 *  - SmsService قبل إرسال SMS
 *  - EmailService قبل إرسال بريد
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'notifications_enabled')) {
                $table->boolean('notifications_enabled')
                    ->default(true)
                    ->after('is_blocked')
                    ->index();
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'notifications_enabled')) {
                $table->dropIndex(['notifications_enabled']);
                $table->dropColumn('notifications_enabled');
            }
        });
    }
};
