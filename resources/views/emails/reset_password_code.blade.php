<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <title>{{ __('api.Password Recovery Verification Code') }}</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            color: #212529;
            margin: 0;
            padding: 0;
        }
        .email-wrapper {
            max-width: 600px;
            margin: 40px auto;
            background: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            direction: rtl;
        }
        .email-header {
            background-color: #007bff;
            padding: 20px;
            text-align: center;
            color: #ffffff;
            font-size: 20px;
        }
        .email-body {
            padding: 30px;
            text-align: center;
        }
        .code-box {
            display: inline-block;
            background: #f1f1f1;
            border: 1px dashed #007bff;
            font-size: 28px;
            padding: 15px 30px;
            margin: 20px 0;
            color: #007bff;
            letter-spacing: 5px;
            border-radius: 6px;
        }
        .email-footer {
            text-align: center;
            font-size: 13px;
            color: #6c757d;
            padding: 20px;
            background: #f8f9fa;
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="email-header">
            {{ __('api.Password Reset') }} - {{ $storeName }}
        </div>
        <div class="email-body">
            <p>{{ __('api.You have requested a password reset') }}</p>
            <p>{{ __('api.Your verification code is') }}</p>
            <div class="code-box">{{ $code }}</div>
            <p>{{ __('api.Please enter this code in the app to reset your password') }}</p>
            <p>{{ __('api.If you did not request a password reset you can safely ignore this message') }}</p>
        </div>
        <div class="email-footer">
            &copy; {{ date('Y') }} {{ __('api.All rights reserved') }} {{ $storeName }}
        </div>
    </div>
</body>
</html>
