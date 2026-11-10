<?php

namespace App\Observers;

use App\Models\Product;
use App\Services\FCMServices;
use Illuminate\Support\Facades\Log;

class ProductObserver
{
    public function updated(Product $product): void
    {
        // === 1) تفعيل المنتج (من معلق إلى نشط) ===
        if ($product->isDirty('is_active') && $product->is_active === true) {
            $this->notifyTraderProductApproved($product);
        }

        // === 2) رفض/تعليق المنتج (من نشط إلى معلق) ===
        if ($product->isDirty('is_active') && $product->is_active === false) {
            $oldValue = $product->getOriginal('is_active');

            // لو كان فعالاً وأصبح معلقاً = رفض/إيقاف
            if ($oldValue === true) {
                $this->notifyTraderProductRejected($product);
            }
        }

        // === 3) رفض طلب إنشاء المنتج (الحالة = rejected) ===
        if ($product->isDirty('status') && $product->status === 'rejected') {
            $this->notifyTraderProductRejected($product);
        }

        // === 4) اعتماد المنتج ===
        if ($product->isDirty('status') && $product->status === 'approved') {
            $this->notifyTraderProductApproved($product);
        }
    }

    private function notifyTraderProductApproved(Product $product): void
    {
        $trader = $product->trader;
        if (!$trader || !$trader->device_token) {
            return;
        }

        try {
            app(FCMServices::class)->sendMsg(
                $trader->id,
                $trader->device_token,
                __('notifications.product_approved_title'),
                __('notifications.product_approved_message', ['product_name' => $product->name]),
                [
                    'type'       => 'product_status',
                    'product_id' => (string) $product->id,
                    'status'     => 'approved',
                ]
            );
        } catch (\Throwable $e) {
            Log::error("FCM product approved failed: product={$product->id} error={$e->getMessage()}");
        }
    }

    private function notifyTraderProductRejected(Product $product): void
    {
        $trader = $product->trader;
        if (!$trader || !$trader->device_token) {
            return;
        }

        try {
            app(FCMServices::class)->sendMsg(
                $trader->id,
                $trader->device_token,
                __('notifications.product_rejected_title'),
                __('notifications.product_rejected_message', [
                    'product_name' => $product->name,
                    'reason'       => $product->rejection_reason ?? __('notifications.no_reason_provided'),
                ]),
                [
                    'type'       => 'product_status',
                    'product_id' => (string) $product->id,
                    'status'     => 'rejected',
                ]
            );
        } catch (\Throwable $e) {
            Log::error("FCM product rejected failed: product={$product->id} error={$e->getMessage()}");
        }
    }
}
