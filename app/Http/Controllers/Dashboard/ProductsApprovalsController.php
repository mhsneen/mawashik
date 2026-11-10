<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\{FCMServices, AdminNotifier};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * ProductsApprovalsController — اعتماد منتجات التجار قبل نشرها.
 *
 * يتبع منطق المراجعة المذكور في المواصفات:
 *  - التاجر ينشئ المنتج بحالة pending.
 *  - الأدمن يراجع ويوافق/يرفض/يوقف.
 *
 * كل عملية تُخطر التاجر عبر FCM (لو لديه device_token)
 * + إشعار داخل التطبيق (يُسجّل في admin_notifications لأغراض المتابعة).
 */
class ProductsApprovalsController extends Controller
{
    public function __construct(
        protected FCMServices $fcm
    ) {}

    public function pending(Request $request)
    {
        $products = Product::pending()
            ->with(['trader:id,name', 'category.translations'])
            ->orderBy('id', 'asc')
            ->paginate(20);

        return view('dashboard.products.pending', compact('products'));
    }

    /**
     * اعتماد المنتج:
     *  - تحديث الحالة + تاريخ الاعتماد.
     *  - تفعيل المنتج تلقائياً (is_active = 1).
     *  - إشعار التاجر عبر FCM.
     */
    public function approve(Request $request, $id)
    {
        try {
            $product = Product::with('trader')->findOrFail($id);

            if ($product->product_status === 'approved') {
                return redirect()->back()->with('info', __('dashboard.Product_Already_Approved'));
            }

            $product->update([
                'product_status'    => 'approved',
                'is_active'         => 1,
                'approved_at'       => now(),
                'rejected_at'       => null,
                'rejection_reason'  => null,
            ]);

            // إشعار FCM للتاجر
            $this->notifyTrader(
                $product->trader,
                __('notifications.product_approved_title'),
                __('notifications.product_approved_message', ['name' => $product->translate(app()->getLocale())?->name ?? ''])
            );

            return redirect()->back()->with('success', __('dashboard.Product_Approved'));
        } catch (\Exception $ex) {
            Log::error('Product approve failed: ' . $ex->getMessage());
            return redirect()->back()->with('error', __('dashboard.Error_DB'));
        }
    }

    /**
     * رفض المنتج:
     *  - تحديث الحالة + تاريخ الرفض + سبب الرفض.
     *  - إيقاف المنتج (is_active = 0).
     *  - إشعار التاجر بالسبب عبر FCM.
     */
    public function reject(Request $request, $id)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ], [], [
            'rejection_reason' => __('dashboard.Rejection_Reason'),
        ]);

        try {
            $product = Product::with('trader')->findOrFail($id);

            $product->update([
                'product_status'    => 'rejected',
                'is_active'         => 0,
                'rejected_at'       => now(),
                'approved_at'       => null,
                'rejection_reason'  => $request->rejection_reason,
            ]);

            // إشعار FCM للتاجر بالسبب
            $this->notifyTrader(
                $product->trader,
                __('notifications.product_rejected_title'),
                __('notifications.product_rejected_message', [
                    'name'   => $product->translate(app()->getLocale())?->name ?? '',
                    'reason' => $request->rejection_reason,
                ])
            );

            return redirect()->back()->with('success', __('dashboard.Product_Rejected'));
        } catch (\Exception $ex) {
            Log::error('Product reject failed: ' . $ex->getMessage());
            return redirect()->back()->with('error', __('dashboard.Error_DB'));
        }
    }

    /**
     * إيقاف المنتج مؤقتاً:
     *  - تحديث الحالة إلى suspended.
     *  - إيقاف المنتج (is_active = 0).
     *  - إشعار التاجر عبر FCM.
     */
    public function suspend(Request $request, $id)
    {
        try {
            $product = Product::with('trader')->findOrFail($id);

            $product->update([
                'product_status' => 'suspended',
                'is_active'      => 0,
            ]);

            // إشعار FCM للتاجر
            $this->notifyTrader(
                $product->trader,
                __('notifications.product_suspended_title'),
                __('notifications.product_suspended_message', ['name' => $product->translate(app()->getLocale())?->name ?? ''])
            );

            return redirect()->back()->with('success', __('dashboard.Product_Suspended'));
        } catch (\Exception $ex) {
            Log::error('Product suspend failed: ' . $ex->getMessage());
            return redirect()->back()->with('error', __('dashboard.Error_DB'));
        }
    }

    /**
     * إعادة تفعيل منتج موقوف/مرفوض.
     */
    public function reactivate(Request $request, $id)
    {
        try {
            $product = Product::with('trader')->findOrFail($id);

            $product->update([
                'product_status'   => 'approved',
                'is_active'         => 1,
                'approved_at'       => now(),
                'rejected_at'       => null,
                'rejection_reason'  => null,
            ]);

            $this->notifyTrader(
                $product->trader,
                __('notifications.product_reactivated_title'),
                __('notifications.product_reactivated_message', ['name' => $product->translate(app()->getLocale())?->name ?? ''])
            );

            return redirect()->back()->with('success', __('dashboard.Product_Reactivated_Success'));
        } catch (\Exception $ex) {
            Log::error('Product reactivate failed: ' . $ex->getMessage());
            return redirect()->back()->with('error', __('dashboard.Error_DB'));
        }
    }

    /**
     * إرسال إشعار FCM للتاجر + تسجيل log للأخطاء.
     */
    private function notifyTrader($trader, string $title, string $body): void
    {
        if (!$trader || !$trader->device_token) {
            return;
        }

        try {
            $this->fcm->sendMsg($trader->id, $trader->device_token, $title, $body);
        } catch (\Throwable $e) {
            Log::warning("FCM product notification failed: " . $e->getMessage());
        }
    }
}
