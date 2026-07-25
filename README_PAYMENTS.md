# نظام بوابات الدفع (Plugin) - دليل التركيب والربط

تم بناء نظام دفع بمبدأ "الإضافات/Plugins": 8 بوابات دفع جاهزة برمجياً، تُفعَّل وتُهيَّأ بالكامل من
لوحة تحكم المتجر بدون أي تعديل على الكود. البوابات المدعومة:

**HyperPay, PayTabs, Moyasar, MyFatoorah, Stripe, PayPal, Tabby, Tamara**

---

## 1) فكرة العمارة (Architecture)

```
config/payment_gateways.php     كتالوج البوابات (الاسم + الحقول المطلوبة لكل بوابة)
app/Models/PaymentMethod.php    جدول payment_methods: تفعيل/تعطيل + إعدادات كل بوابة (مشفّرة)
app/Services/Payments/
    Contracts/PaymentGatewayContract.php   العقد الموحّد: initiate() / verify() / handleWebhook()
    DTO/GatewayInitResult.php, GatewayVerifyResult.php
    AbstractPaymentGateway.php              أدوات مشتركة (قراءة الإعدادات، بناء روابط الرجوع)
    Gateways/{HyperPay,PayTabs,Moyasar,MyFatoorah,Stripe,PayPal,Tabby,Tamara}Gateway.php
    PaymentGatewayManager.php               يقرر أي Driver يشتغل حسب ما هو مفعّل من لوحة التحكم
```

كل بوابة تُنفّذ نفس الواجهة الثلاثية، فإضافة بوابة جديدة مستقبلاً = كلاس جديد + سطر في الكتالوج،
بدون لمس أي كود آخر (Strategy / Driver Pattern الكلاسيكي).

كل البوابات (حتى التي تعتمد على Widget مثل HyperPay) موحَّدة خلف نفس المخرج: إما `redirect_url`
يفتحه التطبيق داخل WebView، أو بيانات `extra` (مثل checkoutId الخاص بـ HyperPay) لعرض الويدجت.
هذا يلغي الحاجة لأي كود Native خاص بكل بوابة داخل التطبيق.

---

## 2) خطوات التركيب على مشروعك

1. انسخ الملفات المرفقة فوق مجلد مشروعك (نفس المسارات تماماً: app/, config/, database/,
   resources/, routes/, lang/) - لن تحذف أي شيء موجود عندك، فقط تضيف/تعدّل ما يخص الدفع.

2. شغّل الميجريشن:
   ```
   php artisan migrate
   ```
   هذا سينشئ جدول `payment_methods` ويضيف أعمدة الدفع لجدول `orders`
   (payment_gateway_code, payment_status, payment_reference, paid_at, payment_gateway_response).

3. سجّل صفوف البوابات الثمانية (كلها معطّلة افتراضياً حتى يفعّلها الأدمن):
   ```
   php artisan db:seed --class=PaymentMethodSeeder
   ```
   ملاحظة: أول فتح لصفحة "طرق الدفع" بلوحة التحكم يقوم بنفس المزامنة تلقائياً حتى لو نسيت هذه الخطوة.

4. أضف صلاحية `Payment_Methods` للأدمن (تمت إضافتها تلقائياً في PermissionSeeder، شغّلها إذا
   المشروع شغّال مسبقاً):
   ```
   php artisan db:seed --class=PermissionSeeder
   ```
   ثم فعّل هذه الصلاحية لدور الأدمن/الموظف من شاشة الأدوار كالمعتاد.

5. امسح الكاش:
   ```
   php artisan config:clear && php artisan route:clear && php artisan view:clear
   ```

---

## 3) لوحة التحكم

- **طرق الدفع** (تظهر تحت قسم الإعدادات في القائمة الجانبية بعد منح الصلاحية):
  - جدول بكل البوابات الثمانية + مفتاح تفعيل/تعطيل سريع (AJAX) لكل بوابة.
  - زر "إعدادات" يفتح نموذج يحتوي فقط على الحقول التي تحتاجها تلك البوابة تحديداً
    (Entity ID لهايبر باي، Secret Key لسترايب... إلخ) + اختيار وضع تجريبي/حقيقي (Test/Live).
  - أي بوابة لا يمكن تفعيلها إلا بعد إدخال بياناتها (تحقق في الكونترولر).
  - المفاتيح السرية تُخزَّن **مشفّرة** في قاعدة البيانات (Laravel `encrypted:array` cast).

---

## 4) الربط مع تطبيق الموبايل (API)

### أ) عرض طرق الدفع المفعّلة (شاشة الدفع في السلة)
```
POST /api/get_payment_methods      (Header: Authorization: Bearer {token})
```
الرد:
```json
{
  "status": true, "code": 200, "message": "...",
  "data": [
    {"code": "stripe", "name": "Stripe", "mode": "test"},
    {"code": "tabby",  "name": "Tabby",  "mode": "live"}
  ]
}
```
اعرض هذه القائمة فقط - أي بوابة غير مفعّلة من لوحة التحكم لن تظهر هنا تلقائياً.

### ب) بدء عملية الدفع
بعد إنشاء الطلب بالـ API الحالي عندك (AddCart/إنشاء Order) بنفس آلية `payment_method` الموجودة سابقاً:
```
POST /api/initiate_payment      (Header: Authorization: Bearer {token})
Body: order_id=123&gateway=stripe
```
الرد:
```json
{
  "status": true, "code": 200, "message": "...",
  "data": {
    "checkout_url": "https://checkout.stripe.com/pay/cs_test_...",
    "reference": "cs_test_...",
    "extra": {}
  }
}
```
- **الحالة العامة (Stripe/PayPal/PayTabs/Moyasar/MyFatoorah/Tabby/Tamara):** افتح `checkout_url` في WebView داخل التطبيق مباشرة.
- **حالة HyperPay فقط:** `checkout_url` يكون `null`، وتجد بدلاً منه في `extra`:
  `checkout_id` و `widget_script` - يفتح التطبيق صفحة HTML بسيطة تحمّل هذا السكربت وتعرض
  ويدجت إدخال البطاقة (هذا هو أسلوب عمل HyperPay الرسمي "Copy&Pay"، وليس رابط استضافة جاهز).

### ج) معرفة نتيجة الدفع
بعد إتمام العميل للدفع، البوابة تُرجعه لصفحة داخلية (`/api/payments/return/{gateway}`) تعرض له
رسالة نجاح/فشل بسيطة (resources/views/site/payment/result.blade.php) - راقب التطبيق تغيّر الصفحة
أو النص الظاهر بها، ثم أغلق الـ WebView واستدعِ API تفاصيل الطلب الحالي عندك لعرض الحالة النهائية
(عمود `payment_status` على الطلب: pending / paid / failed).

بالتوازي، كل بوابة تُرسل **Webhook** مباشرةً لسيرفرك فور اكتمال الدفع (أوثق وأضمن من انتظار رجوع
المستخدم، خاصة مع Tabby/Tamara التي تراجع الطلب لاحقاً). يجب تسجيل رابط الـ webhook التالي في
لوحة كل بوابة دفع (تجده عادة تحت "Webhook URL" أو "Notification URL" أو "IPN"):

```
{APP_URL}/api/payments/webhook/{gateway}
```
مثال: `https://yourdomain.com/api/payments/webhook/stripe`

هذا الرابط عام (بدون توكن) لأن البوابة نفسها من يستدعيه، والتحقق من الأمان يتم داخل كل Driver
(توقيع Stripe، إعادة الاستعلام Server-to-Server لدى Moyasar/PayTabs/MyFatoorah/Tabby/Tamara، أو
Notification Token في Tamara).

---

## 5) إدخال بيانات كل بوابة - ملخص الحقول المطلوبة

| البوابة     | الحقول                                            |
|-------------|-----------------------------------------------------|
| HyperPay    | Entity ID, Access Token, API Base URL (test/live)   |
| PayTabs     | Profile ID, Server Key, Region Base URL             |
| Moyasar     | Secret Key, Publishable Key                         |
| MyFatoorah  | Api Token                                            |
| Stripe      | Publishable Key, Secret Key, Webhook Signing Secret |
| PayPal      | Client ID, Client Secret                            |
| Tabby       | Secret Key, Public Key, Merchant Code               |
| Tamara      | API Token, Notification Token                       |

كل هذه القيم تُدخَل من لوحة التحكم مباشرة (طرق الدفع → إعدادات كل بوابة)، **وليس من ملف .env** -
هذا هو ما يجعل النظام "بلجن" فعلياً: أي مشرف يقدر يفعّل/يعدّل بياناته بدون وصول للسيرفر.

---

## 6) ملاحظات مهمة قبل الإطلاق الفعلي

- شغّل كل بوابة أولاً بوضع **Test** وجرّب عملية دفع كاملة (نجاح + فشل) قبل التحويل لـ Live.
- تفاصيل بعض نقاط الـ API (خصوصاً حقول Tabby/Tamara/PayTabs الدقيقة كالعنوان والمدينة) قد
  تحتاج تعديلاً بسيطاً حسب بيانات العميل الفعلية عندك في جدول addresses - الكود حالياً يرسل
  قيماً افتراضية آمنة حيث لا تتوفر البيانات.
  راجع روابط التوثيق الرسمية الظاهرة أسفل كل نموذج إعدادات في اللوحة.
- كل رد خام من البوابة يُخزَّن في `orders.payment_gateway_response` لأغراض الدعم الفني والمراجعة.
- منطق نقاط الولاء / تغيير حالة الطلب تلقائياً بعد الدفع (مثال: نقل الطلب لحالة "قيد التجهيز")
  لم يُضَف عمداً - أضِفه داخل `PaymentApiController::applyVerifyResult()` حسب تدفق عملك التجاري
  (على الأرجح تريد استدعاء نفس المنطق الموجود بالفعل في `OrderObserver`).
