<?php

return [
    // === الطلبات ===
    'order_received_title'                  => 'تم استلام طلبك',
    'order_received_message'                => 'طلبك رقم :order_id تم استلامه بنجاح',
    'new_order_title'                       => 'طلب جديد',
    'new_order_message'                     => 'هناك طلب جديد رقم :order_id',
    'order_status_changed_title'            => 'تم تحديث حالة الطلب',
    'order_status_changed_message'          => 'طلبك رقم :order_id تغيرت حالته إلى :status.',
    'order_cancelled_message'               => 'قام العميل :client_name بإلغاء الطلب رقم #:order_id',
    'order_cancelled_title'                 => 'تم إلغاء الطلب',

    // === المندوبين ===
    'driver_order_assigned_title'           => 'طلب جديد لك',
    'driver_order_assigned_message'         => 'طلب جديد لتوصيله رقم #:order_id',

    // === التجار ===
    'trader_new_order_title'                => 'طلب جديد',
    'trader_new_order_message'              => 'لديك طلب جديد رقم #:order_id',
    'trader_approved_title'                 => 'تم اعتماد حسابك',
    'trader_approved_message'               => 'مرحباً، تم اعتماد حسابك في مواشي وسيط. يمكنك الآن البدء بالبيع.',
    'trader_rejected_title'                 => 'تم رفض حسابك',
    'trader_rejected_message'               => 'نأسف، تم رفض حسابك. السبب: :reason',
    'trader_suspended_title'                => 'تم إيقاف حسابك مؤقتاً',
    'trader_suspended_message'              => 'تم إيقاف حسابك وكل منتجاتك مؤقتاً. يرجى التواصل مع الإدارة.',
    'trader_reactivated_title'              => 'تم إعادة تفعيل حسابك',
    'trader_reactivated_message'             => 'تم إعادة تفعيل حسابك ومنتجاتك في مواشي وسيط.',

    // === المنتجات ===
    'product_approved_title'                => 'تم اعتماد منتجك',
    'product_approved_message'              => 'تم اعتماد منتجك ":name" وهو الآن متاح للعرض.',
    'product_rejected_title'                => 'تم رفض منتجك',
    'product_rejected_message'              => 'تم رفض منتجك ":name". السبب: :reason',
    'product_suspended_title'               => 'تم إيقاف منتجك',
    'product_suspended_message'             => 'تم إيقاف منتجك ":name" مؤقتاً من قبل الإدارة.',
    'product_reactivated_title'             => 'تم إعادة تفعيل منتجك',
    'product_reactivated_message'           => 'تم إعادة تفعيل منتجك ":name".',



    // ===== التاجر =====
    'trader_order_accepted_title'             => 'تم قبول الطلب',
    'trader_order_accepted_message'           => 'تم قبول الطلب #:order_id',
    'trader_order_preparing_title'             => 'الطلب قيد التجهيز',
    'trader_order_preparing_message'           => 'الطلب #:order_id قيد التجهيز',
    'trader_order_out_for_delivery_title'     => 'الطلب في الطريق',
    'trader_order_out_for_delivery_message'   => 'الطلب #:order_id في الطريق للعميل',
    'trader_order_delivered_title'            => 'تم تسليم الطلب',
    'trader_order_delivered_message'          => 'تم تسليم الطلب #:order_id بنجاح',
    'trader_order_cancelled_title'            => 'تم إلغاء الطلب',
    'trader_order_cancelled_message'          => 'تم إلغاء الطلب #:order_id',

    // ===== المندوب =====
    'driver_order_ready_title'                => 'طلب جاهز للاستلام',
    'driver_order_ready_message'              => 'الطلب #:order_id جاهز للاستلام من التاجر',
    'driver_out_for_delivery_title'           => 'انطلق للتوصيل',
    'driver_out_for_delivery_message'         => 'الطلب #:order_id في الطريق للعميل',
    'driver_order_delivered_title'            => 'تم التسليم',
    'driver_order_delivered_message'          => 'تم تسليم الطلب #:order_id بنجاح',
    'driver_order_cancelled_title'            => 'تم إلغاء الطلب',
    'driver_order_cancelled_message'         => 'تم إلغاء الطلب #:order_id',

    // ===== المنتجات =====
    'no_reason_provided'                      => 'لم يتم تحديد سبب',
];
