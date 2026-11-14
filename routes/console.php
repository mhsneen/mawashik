<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Wallet;
use App\Models\WalletTransaction;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// تنظيف الطلبات المعلّقة (بانتظار الدفع الإلكتروني status_id = 7) الأقدم من ساعة
/*Schedule::call(function () {
    $orders = Order::with('items')
        ->where('status_id', 7)
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

            // 2. استرداد المبلغ للمحفظة
            if ($order->payment_status === 'paid') {
                $user = $order->user;
                if ($user) {
                    // ✅ استخدام firstOrCreate لتجنب خطأ null إذا لم تكن المحفظة موجودة
                    $wallet = Wallet::firstOrCreate(
                        ['user_id' => $user->id],
                        ['balance' => 0, 'rolename' => 'client']
                    );

                    $amount = $order->final_price;

                    $wallet->balance += $amount;
                    $wallet->save();

                    WalletTransaction::create([
                        'wallet_id' => $wallet->id,
                        'order_id' => $order->id,
                        'type' => 'credit',
                        'amount' => $amount,
                        'description' => 'استرداد مبلغ الطلب #' . $order->order_number . ' (إلغاء تلقائي لعدم إكمال الدفع)',
                    ]);
                }
            }

            // 3. تحديث الحالة إلى ملغي (6)
            $order->status_id = 6;
            $order->payment_status = 'failed';
            $order->saveQuietly();

            // 4. حذف الطلب ناعياً (Soft Delete)
            $order->delete();
        });
    }
})->hourly()
  ->name('cleanup:pending-orders')
  ->withoutOverlapping();*/

// إعادة تعيين الطلبات للسائقين إذا لم يردوا خلال 20 دقيقة
Schedule::call(function () {
    $service = new \App\Services\DriverAssignmentService();

    $staleOrderIds = \App\Models\OrderDriverOffer::where('status', 'pending')
        ->where('offered_at', '<', now()->subMinutes(20))
        ->pluck('order_id')
        ->unique();

    foreach ($staleOrderIds as $orderId) {
        $order = \App\Models\Order::find($orderId);
        if (!$order || $order->driver_id || $order->status_id != 3) {
            continue;
        }

        \App\Models\OrderDriverOffer::where('order_id', $orderId)
            ->where('status', 'pending')
            ->where('offered_at', '<', now()->subMinutes(20))
            ->update(['status' => 'expired', 'responded_at' => now()]);

        $stillPending = \App\Models\OrderDriverOffer::where('order_id', $orderId)->where('status', 'pending')->exists();
        if (!$stillPending) {
            $nextBatch = \App\Models\OrderDriverOffer::where('order_id', $orderId)->max('batch_number') + 1;
            // ✅ هنا سيقوم تلقائياً باستخدام الترتيب الجديد (الأعلى تقييماً)
            $service->broadcastToAvailableDrivers($order, $nextBatch);
        }
    }
})->everyTwoMinutes()->name('expire:driver-offers')->withoutOverlapping();
