<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * إضافة عمود الموافقة على الشروط لجدول المستخدمين (users).
 *
 * هذا الحقل يخدم:
 *  - السائق (Driver) حسب البند الرابع من مواصفات "وسيط المواشي"
 *  - التاجر (Trader) حسب البند الثالث
 * حيث يجب على المستخدم الموافقة على الشروط والأحكام قبل إتمام التسجيل.
 *
 * النوع boolean — true = وافق، false/default = لم يوافق.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'terms_accepted')) {
                $table->boolean('terms_accepted')
                    ->default(false)
                    ->after('is_blocked')
                    ->index();
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'terms_accepted')) {
                $table->dropIndex(['terms_accepted']);
                $table->dropColumn('terms_accepted');
            }
        });
    }
};
