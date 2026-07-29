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
// تنظيف الطلبات المعلّقة (بانتظار الدفع الإلكتروني status_id = 7) الأقدم من ساعة
// مع إرجاع المخزون واسترداد المبلغ قبل الإلغاء لمنع تسريب المخزون
Schedule::call(function () {
    // ✅ التعديل هنا: نجلب الطلبات التي حالتها 7 (بانتظار الدفع) فقط
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

                // ✅ ملاحظة برمجية: إذا كنت تخصم مخزون "الإضافات" (Modifiers) في AddCart
                // يجب عليك إرجاعها هنا أيضاً بنفس الطريقة التي شرحناها سابقاً.
            }

            // 2. استرداد المبلغ للمحفظة
            // (هذا يفيد لو كان الدفع عبر المحفظة الداخلية للمتجر وتعطل شيء ما)
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
                        'description' => 'استرداد مبلغ الطلب #' . $order->id . ' (إلغاء تلقائي لعدم إكمال الدفع الإلكتروني)',
                    ]);
                }
            }

            // 3. تحديث الحالة إلى ملغي (6)
            // استخدمنا saveQuietly لتفادي تكرار إشعارات Observer (مثل إرسال إشعار إلغاء للمستخدم)
            $order->status_id = 6;
            $order->payment_status = 'failed'; // ✅ تحديث حالة الدفع فشل
            $order->saveQuietly();

            // 4. حذف الطلب ناعياً (Soft Delete)
            // (ملاحظة: قد تفضل عدم حذفه تماماً لترك سجل للطلبات المرفوضة، لكن إذا كانت سياستك الحذف فلا بأس)
            $order->delete();
        });
    }
})->hourly()
  ->name('cleanup:pending-orders')
  ->withoutOverlapping();
