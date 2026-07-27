<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\WalletTransaction;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// تنظيف الطلبات المعلّقة (status_id = 1) الأقدم من ساعة
// مع إرجاع المخزون واسترداد المبلغ قبل الحذف لمنع تسريب المخزون
Schedule::call(function () {
    // نجلب الطلبات مع عناصرها لتقليل الاستعلامات
    $orders = Order::with('items')
        ->where('status_id', 1)
        ->where('created_at', '<', now()->subHour())
        ->get();

    foreach ($orders as $order) {
        DB::transaction(function () use ($order) {
            // 1. إرجاع الكميات للمخزون
            foreach ($order->items as $item) {
                if ($item->variant_id) {
                    $variant = ProductVariant::find($item->variant_id);
                    if ($variant && $variant->track_quantity) {
                        $variant->increment('quantity', $item->quantity);
                    }
                } else {
                    $product = Product::find($item->product_id);
                    if ($product && $product->track_quantity) {
                        $product->increment('quantity', $item->quantity);
                    }
                }
            }

            // 2. استرداد المبلغ للمحفظة إذا كان الطلب مدفوعاً مسبقاً
            if ($order->payment_status === 'paid') {
                $user = $order->user;
                if ($user) {
                    $wallet = $user->wallet;
                    $amount = $order->final_price;

                    $wallet->balance += $amount;
                    $wallet->save();

                    WalletTransaction::create([
                        'wallet_id' => $wallet->id,
                        'order_id' => $order->id,
                        'type' => 'credit',
                        'amount' => $amount,
                        'description' => 'استرداد مبلغ الطلب #' . $order->id . ' (إلغاء تلقائي لعدم إكمال الطلب)',
                    ]);
                }
            }

            // 3. تحديث الحالة إلى ملغي (6) وحفظ بهدوء لتفادي تكرار إشعارات Observer
            $order->status_id = 6;
            $order->saveQuietly();

            // 4. حذف الطلب ناعياً (Soft Delete)
            $order->delete();
        });
    }
})->hourly()
  ->name('cleanup:pending-orders')
  ->withoutOverlapping();
