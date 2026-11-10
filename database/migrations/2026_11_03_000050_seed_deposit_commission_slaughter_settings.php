<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * إضافة إعدادات العربون + العمولات + الذبح إلى جدول settings (key-value).
 * تستخدمها الخدمات: DepositService / CommissionService / SlaughterService.
 *
 * لإضافة الإعدادات تُستخدم seeder لضمان وجودها بأي بيئة.
 */
return new class extends Migration
{
    public function up(): void
    {
        // إعدادات العربون
        \App\Models\Setting::setMany([
            'deposit_active'        => '1',
            'deposit_percentage'    => '15.00',
            'deposit_min_amount'    => '0',
            'deposit_max_amount'    => '0',       // 0 = لا حد أقصى
            'deposit_refund_before' => 'preparing', // قبل بدء التجهيز فقط
            'deposit_forfeit_after' => 'preparing', // بعد بدء التجهيز تُحجز للمنصة
        ]);

        // إعدادات العمولات الافتراضية
        \App\Models\Setting::setMany([
            'commission_trader_default'  => '5.00',   // 5% من قيمة البيع
            'commission_driver_default'  => '10.00',  // 10% من رسوم التوصيل
            'commission_active'          => '1',
        ]);

        // إعدادات خدمة الذبح
        \App\Models\Setting::setMany([
            'slaughter_service_active'    => '1',
            'slaughter_default_price'      => '50.00',
            'slaughter_min_price'          => '0',
            'slaughter_max_price'          => '0',
        ]);

        // إعدادات الخريطة / Google Maps
        \App\Models\Setting::setMany([
            'google_maps_api_key'         => '',
            'google_maps_default_lat'     => '24.7136',   // الرياض
            'google_maps_default_lng'     => '46.6753',
            'google_maps_default_zoom'    => '12',
            'maps_browse_radius_km'       => '20',
        ]);
    }

    public function down(): void
    {
        \App\Models\Setting::whereIn('key', [
            'deposit_active', 'deposit_percentage', 'deposit_min_amount',
            'deposit_max_amount', 'deposit_refund_before', 'deposit_forfeit_after',
            'commission_trader_default', 'commission_driver_default', 'commission_active',
            'slaughter_service_active', 'slaughter_default_price', 'slaughter_min_price', 'slaughter_max_price',
            'google_maps_api_key', 'google_maps_default_lat', 'google_maps_default_lng',
            'google_maps_default_zoom', 'maps_browse_radius_km',
        ])->delete();
    }
};
