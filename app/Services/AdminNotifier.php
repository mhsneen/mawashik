<?php

namespace App\Services;

use App\Models\{AdminNotification, Order, WalletWithdrawalRequest, User};

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
        AdminNotification::create([
            'type'  => 'withdrawal_request',
            'title' => __('dashboard.withdrawal_request_notification_title'),
            'body'  => __('dashboard.withdrawal_request_notification_body', [
                'driver' => $request->user->name ?? '',
                'amount' => number_format($request->amount, 2),
            ]),
            'url'   => route('walletsdrivers.withdrawalRequests'),
            'icon'  => 'bx-money-withdraw',
            'color' => 'warning',
            'notifiable_type' => WalletWithdrawalRequest::class,
            'notifiable_id'   => $request->id,
        ]);
    }


}
