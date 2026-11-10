<?php

namespace App\Services;

use App\Models\{AdminNotification, Order, Product, User, WalletWithdrawalRequest};

class AdminNotifier
{
    public static function newOrder(Order $order): void
    {
        AdminNotification::create([
            'type'  => 'new_order',
            'title' => __('dashboard.new_order_notification_title'),
            'body'  => __('dashboard.new_order_notification_body', ['order_id' => $order->order_number]),
            'url'   => route('orders.show', $order->id),
            'icon'  => 'bx-cart-add',
            'color' => 'success',
            'notifiable_type' => Order::class,
            'notifiable_id'   => $order->id,
        ]);
    }

    public static function orderPaidOnline(Order $order): void
    {
        AdminNotification::create([
            'type'  => 'order_paid_online',
            'title' => __('dashboard.order_paid_notification_title'),
            'body'  => __('dashboard.order_paid_notification_body', ['order_id' => $order->order_number]),
            'url'   => route('orders.show', $order->id),
            'icon'  => 'bx-credit-card',
            'color' => 'info',
            'notifiable_type' => Order::class,
            'notifiable_id'   => $order->id,
        ]);
    }

    public static function orderDelivered(Order $order): void
    {
        AdminNotification::create([
            'type'  => 'order_delivered',
            'title' => __('dashboard.order_delivered_notification_title'),
            'body'  => __('dashboard.order_delivered_notification_body', ['order_id' => $order->order_number]),
            'url'   => route('orders.show', $order->id),
            'icon'  => 'bx-check-circle',
            'color' => 'success',
            'notifiable_type' => Order::class,
            'notifiable_id'   => $order->id,
        ]);
    }
    
    
        public static function orderCancelled(Order $order, ?string $reason = null): void
    {
        $body = __('dashboard.order_cancelled_notification_body', ['order_id' => $order->order_number]);
        if ($reason) {
            $body .= " - " . __('dashboard.reason') . ": {$reason}";
        }

        AdminNotification::create([
            'type'            => 'order_cancelled',
            'title'           => __('dashboard.order_cancelled_notification_title'),
            'body'            => $body,
            'url'             => route('orders.show', $order->id),
            'icon'            => 'bx bx-x-circle',
            'color'           => 'danger',
            'notifiable_type' => Order::class,
            'notifiable_id'   => $order->id,
        ]);
    }

    public static function withdrawalRequested(WalletWithdrawalRequest $request): void
    {
        // ✅ إصلاح: ربط الإشعار بدور المستخدم (سائق أو تاجر) واختيار الرابط الصحيح.
        // الكود القديم كان يوجّه دائماً لصفحة سحب السائقين حتى لو كان الطلب من تاجر.
        $user = $request->user;
        $userName = $user?->name ?? '';
        $isTrader = $user && $user->hasRole('trader');

        // ملاحظة: حالياً فقط محفظة السائقين لها صفحة طلبات سحب (walletsdrivers.withdrawalRequests).
        // محفظة التجار (walletstraders) ليس لها صفحة موافقات بعد.
        // لذلك نوجّه التاجر لصفحة حركات محفظته المحددة، والأدمن يرى الطلبات هناك.
        $url = $isTrader && $request->wallet_id
            ? route('walletstraders.transactions', ['id' => $request->wallet_id])
            : route('walletsdrivers.withdrawalRequests');

        AdminNotification::create([
            'type'  => 'withdrawal_request',
            'title' => __('dashboard.withdrawal_request_notification_title'),
            'body'  => __('dashboard.withdrawal_request_notification_body', [
                'driver' => $userName, // ❗ متغيّر الترجمة اسمه driver لكنه يصلح لكل المستخدمين
                'amount' => number_format($request->amount, 2),
            ]),
            'url'   => $url,
            'icon'  => 'bx-money-withdraw',
            'color' => 'warning',
            'notifiable_type' => WalletWithdrawalRequest::class,
            'notifiable_id'   => $request->id,
        ]);
    }

    // === مواشي وسيط ===

    /**
     * تاجر جديد سجّل: بانتظار الاعتماد.
     */
    public static function newTraderPendingApproval(User $trader): void
    {
        AdminNotification::create([
            'type'  => 'trader_pending',
            'title' => __('dashboard.trader_pending_notification_title'),
            'body'  => __('dashboard.trader_pending_notification_body', ['name' => $trader->name]),
            'url'   => route('traders.pending'),
            'icon'  => 'bx-user-plus',
            'color' => 'warning',
            'notifiable_type' => User::class,
            'notifiable_id'   => $trader->id,
        ]);
    }

    /**
     * منتج جديد من تاجر بانتظار الاعتماد.
     */
    public static function newProductPendingApproval(Product $product): void
    {
        AdminNotification::create([
            'type'  => 'product_pending',
            'title' => __('dashboard.product_pending_notification_title'),
            'body'  => __('dashboard.product_pending_notification_body', ['id' => $product->id]),
            'url'   => route('products-approvals.pending'),
            'icon'  => 'bx-package',
            'color' => 'warning',
            'notifiable_type' => Product::class,
            'notifiable_id'   => $product->id,
        ]);
    }

    /**
     * مندوب جديد سجّل: بانتظار الاعتماد.
     */
    public static function newDriverPendingApproval(User $driver): void
    {
        AdminNotification::create([
            'type'  => 'driver_pending',
            'title' => __('dashboard.driver_pending_notification_title'),
            'body'  => __('dashboard.driver_pending_notification_body', ['name' => $driver->name]),
            'url'   => route('drivers.pending'),
            'icon'  => 'bx-car',
            'color' => 'warning',
            'notifiable_type' => User::class,
            'notifiable_id'   => $driver->id,
        ]);
    }
}
