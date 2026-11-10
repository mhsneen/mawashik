<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ادفع الآن</title>
    <style>
        body { font-family: sans-serif; background-color: #f8fafc; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .card { background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); width: 100%; max-width: 400px; }
        h3 { text-align: center; color: #1E293B; margin-bottom: 20px; }
    </style>
    <!-- ✅ سكربت HyperPay Widget -->
    <script src="https://eu-test.oppwa.com/v1/paymentWidgets.js?checkoutId={{ $checkoutId }}"></script>
</head>
<body>
    <div class="card">
        <h3>أدخل بيانات البطاقة</h3>
        
        <!-- ✅✅✅ تم حذف data-brands لكي تعرض البوابة كل البطاقات المتاحة تلقائياً ✅✅✅ -->
        <form action="{{ $resultUrl }}" class="paymentWidgets"></form>
        
    </div>
</body>
</html>