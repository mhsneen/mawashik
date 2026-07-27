<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Models\Order;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// تنظيف الطلبات المعلّقة (status_id = 1) الأقدم من ساعة
// يمنع تراكم "Zombie Orders" في قاعدة البيانات
Schedule::call(function () {
    Order::where('status_id', 1)
        ->where('created_at', '<', now()->subHour())
        ->delete();
})->hourly()
  ->name('cleanup:pending-orders')
  ->withoutOverlapping();
