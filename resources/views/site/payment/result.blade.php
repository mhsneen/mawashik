<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $success ? __('api.Payment_Success') : __('api.Payment_Failed') }}</title>
    <style>
        body {
            margin: 0;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: -apple-system, Tahoma, Arial, sans-serif;
            background: #f7f8fa;
        }
        .box {
            text-align: center;
            padding: 32px;
        }
        .icon {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 16px;
            font-size: 36px;
            color: #fff;
            background: {{ $success ? '#2ecc71' : '#e74c3c' }};
        }
        h2 { margin: 0 0 8px; color: #222; }
        p { color: #666; }
    </style>
</head>
<body data-payment-status="{{ $success ? 'success' : 'failed' }}">
    <div class="box">
        <div class="icon">{{ $success ? '✓' : '✕' }}</div>
        <h2>{{ $message }}</h2>
        <p>{{ __('dashboard.Payment_Return_Close_Hint') }}</p>
    </div>
    <!--
        ملاحظة للتطبيق (Flutter/RN/Native WebView):
        استمع لتغيّر عنوان الصفحة أو راقب body[data-payment-status] لمعرفة نتيجة الدفع،
        ثم أغلق الـ WebView تلقائياً وحدّث حالة الطلب عبر استدعاء get order details API.
    -->
</body>
</html>
