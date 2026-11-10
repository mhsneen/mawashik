<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * إضافة أعمدة المندوب: صورة الهوية + صورة الرخصة + حالة اعتماد منفصلة للسائق.
 *
 * السائق يحتاج اعتماد إداري قبل أن يُسمح له بقبول الطلبات (حسب البند الرابع من المواصفات).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('driver_status', ['pending', 'approved', 'rejected', 'suspended'])
                ->nullable()
                ->after('admin_status_note')
                ->index();

            $table->decimal('driver_commission_rate', 5, 2)->nullable()->after('driver_status');

            $table->string('id_image')->nullable()->after('driver_commission_rate');
            $table->string('license_image')->nullable()->after('id_image');

            $table->timestamp('driver_approved_at')->nullable()->after('license_image');
            $table->timestamp('driver_rejected_at')->nullable()->after('driver_approved_at');
            $table->string('driver_status_note')->nullable()->after('driver_rejected_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['driver_status']);
            $table->dropColumn([
                'driver_status',
                'driver_commission_rate',
                'id_image',
                'license_image',
                'driver_approved_at',
                'driver_rejected_at',
                'driver_status_note',
            ]);
        });
    }
};
