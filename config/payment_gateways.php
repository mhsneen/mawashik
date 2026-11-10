<?php

/**
 * كتالوج بوابات الدفع المدعومة في المتجر (نظام Plugin).
 *
 * كل بوابة معرّفة بـ:
 *  - driver        : الكلاس المسؤول عن التنفيذ (implements PaymentGatewayContract)
 *  - name_ar/name_en
 *  - logo          : مسار شعار اختياري (public/assets/dashboard/img/payments/{code}.png)
 *  - fields        : الحقول التي تظهر ديناميكياً في لوحة التحكم وتُحفظ داخل عمود config (مشفّر)
 *  - has_mode      : هل تحتاج اختيار وضع تجريبي/حقيقي (Test/Live)
 *  - docs_url      : رابط توثيق البوابة الرسمي (للمرجع فقط)
 *
 * لإضافة بوابة جديدة مستقبلاً: أضف عنصر هنا + كلاس Gateway جديد في
 * app/Services/Payments/Gateways ثم سجّله في PaymentGatewayManager::$map
 */

return [

    'hyperpay' => [
        'driver'   => \App\Services\Payments\Gateways\HyperPayGateway::class,
        'name_ar'  => 'هايبر باي - HyperPay',
        'name_en'  => 'HyperPay',
        'has_mode' => true,
        'fields'   => [
            'entity_id'    => ['label_ar' => 'Entity ID', 'label_en' => 'Entity ID', 'type' => 'text', 'required' => true],
            'access_token' => ['label_ar' => 'Access Token (Bearer)', 'label_en' => 'Access Token (Bearer)', 'type' => 'password', 'required' => true],
            'base_url'     => ['label_ar' => 'رابط API (test/live)', 'label_en' => 'API Base URL', 'type' => 'text', 'required' => true, 'placeholder' => 'https://eu-test.oppwa.com'],
        ],
        'docs_url' => 'https://hyperpay.docs.oppwa.com/',
    ],

    'paytabs' => [
        'driver'   => \App\Services\Payments\Gateways\PayTabsGateway::class,
        'name_ar'  => 'باي تابس - PayTabs',
        'name_en'  => 'PayTabs',
        'has_mode' => true,
        'fields'   => [
            'profile_id' => ['label_ar' => 'Profile ID', 'label_en' => 'Profile ID', 'type' => 'text', 'required' => true],
            'server_key' => ['label_ar' => 'Server Key', 'label_en' => 'Server Key', 'type' => 'password', 'required' => true],
            'region_url' => ['label_ar' => 'رابط المنطقة (Region Base URL)', 'label_en' => 'Region Base URL', 'type' => 'text', 'required' => true, 'placeholder' => 'https://secure.paytabs.sa'],
        ],
        'docs_url' => 'https://site.paytabs.com/en/developers/',
    ],

    'moyasar' => [
        'driver'   => \App\Services\Payments\Gateways\MoyasarGateway::class,
        'name_ar'  => 'ميسر - Moyasar',
        'name_en'  => 'Moyasar',
        'has_mode' => true,
        'fields'   => [
            'secret_key'      => ['label_ar' => 'Secret Key', 'label_en' => 'Secret Key', 'type' => 'password', 'required' => true],
            'publishable_key' => ['label_ar' => 'Publishable Key', 'label_en' => 'Publishable Key', 'type' => 'text', 'required' => false],
        ],
        'docs_url' => 'https://docs.moyasar.com/',
    ],

    'myfatoorah' => [
        'driver'   => \App\Services\Payments\Gateways\MyFatoorahGateway::class,
        'name_ar'  => 'ماي فاتورة - MyFatoorah',
        'name_en'  => 'MyFatoorah',
        'has_mode' => true,
        'fields'   => [
            'api_key' => ['label_ar' => 'Api Token', 'label_en' => 'Api Token', 'type' => 'password', 'required' => true],
        ],
        'docs_url' => 'https://docs.myfatoorah.com/',
    ],

    'stripe' => [
        'driver'   => \App\Services\Payments\Gateways\StripeGateway::class,
        'name_ar'  => 'سترايب - Stripe',
        'name_en'  => 'Stripe',
        'has_mode' => true,
        'fields'   => [
            'publishable_key' => ['label_ar' => 'Publishable Key', 'label_en' => 'Publishable Key', 'type' => 'text', 'required' => false],
            'secret_key'      => ['label_ar' => 'Secret Key', 'label_en' => 'Secret Key', 'type' => 'password', 'required' => true],
            'webhook_secret'  => ['label_ar' => 'Webhook Signing Secret', 'label_en' => 'Webhook Signing Secret', 'type' => 'password', 'required' => false],
        ],
        'docs_url' => 'https://stripe.com/docs/api',
    ],

    'paypal' => [
        'driver'   => \App\Services\Payments\Gateways\PayPalGateway::class,
        'name_ar'  => 'باي بال - PayPal',
        'name_en'  => 'PayPal',
        'has_mode' => true,
        'fields'   => [
            'client_id'     => ['label_ar' => 'Client ID', 'label_en' => 'Client ID', 'type' => 'text', 'required' => true],
            'client_secret' => ['label_ar' => 'Client Secret', 'label_en' => 'Client Secret', 'type' => 'password', 'required' => true],
        ],
        'docs_url' => 'https://developer.paypal.com/docs/api/overview/',
    ],

    'tabby' => [
        'driver'   => \App\Services\Payments\Gateways\TabbyGateway::class,
        'name_ar'  => 'تابي - Tabby',
        'name_en'  => 'Tabby',
        'has_mode' => true,
        'fields'   => [
            'secret_key'    => ['label_ar' => 'Secret Key', 'label_en' => 'Secret Key', 'type' => 'password', 'required' => true],
            'public_key'    => ['label_ar' => 'Public Key', 'label_en' => 'Public Key', 'type' => 'text', 'required' => false],
            'merchant_code' => ['label_ar' => 'Merchant Code', 'label_en' => 'Merchant Code', 'type' => 'text', 'required' => true],
        ],
        'docs_url' => 'https://developers.tabby.ai/',
    ],

    'tamara' => [
        'driver'   => \App\Services\Payments\Gateways\TamaraGateway::class,
        'name_ar'  => 'تمارا - Tamara',
        'name_en'  => 'Tamara',
        'has_mode' => true,
        'fields'   => [
            'api_token'          => ['label_ar' => 'API Token', 'label_en' => 'API Token', 'type' => 'password', 'required' => true],
            'notification_token' => ['label_ar' => 'Notification Token', 'label_en' => 'Notification Token', 'type' => 'password', 'required' => false],
        ],
        'docs_url' => 'https://docs.tamara.co/',
    ],

];
