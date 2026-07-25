# سجل التصحيحات - نظام بوابات الدفع

تم فحص الربط البرمجي للبوابات الثمانية مقابل التوثيق الرسمي لكل مزوّد، والتصحيحات التالية طُبّقت:

## 1. Stripe — خطأ حرج (كان سيمنع كل عملية دفع)
`app/Services/Payments/Gateways/StripeGateway.php`
- تم حذف باراميتر `currency` من المستوى الأعلى عند إنشاء `checkout/sessions`. هذا الباراميتر غير
  موجود في هذا الـ endpoint، وStripe يرفض أي باراميتر غير معروف بخطأ `Received unknown parameter`.
  العملة الصحيحة موجودة أصلاً داخل `line_items[0][price_data][currency]` ولم تُمس.

## 2. Tabby — خطأ حرج (يمنع التحقق والـ Webhook بصمت)
`app/Services/Payments/Gateways/TabbyGateway.php`
- رد إنشاء الجلسة يحتوي على معرّفين مختلفين: `id` (Session ID) و`payment.id` (Payment ID الفعلي).
  الكود كان يخزّن `id` كمرجع للطلب، بينما `verify()` والـ Webhook يحتاجان `payment.id` فعلياً عبر
  `GET /api/v2/payments/{id}`. تم تصحيحه لتخزين `payment.id`.
- تمت إضافة `buyer_history` (موصى بها من Tabby لتحسين معدل قبول الطلب في وضع Live).

## 3. MyFatoorah — خطأ منطقي في العملة
`app/Services/Payments/Gateways/MyFatoorahGateway.php`
- منطق تحديد العملة في `verify()` كان غير مرتبط فعلياً بعملة الفاتورة. تم تصحيحه لاستخدام
  `Data.InvoiceDisplayCurrencyIso` مع افتراضي `SAR`.

## 4. PayPal — تحديث لأسلوب حديث (الطريقة القديمة لا تزال تعمل لكنها Deprecated)
`app/Services/Payments/Gateways/PayPalGateway.php`
- تم الانتقال من `application_context` (مُعلَّم Deprecated رسمياً) إلى
  `payment_source.paypal.experience_context`.
- تبعاً لذلك، رابط التوجيه في رد بايبال يتغيّر اسمه من `rel: approve` إلى `rel: payer-action` عند
  تمرير `payment_source.paypal` صراحة - تم تحديث منطق البحث عن الرابط ليدعم الاثنين معاً.

## 5. Tamara — تناقض في العملة بين عناصر الطلب والمجموع
`app/Services/Payments/Gateways/TamaraGateway.php`
- عناصر الطلب (`items[].unit_price/total_amount`) كانت مثبّتة دائماً على `SAR` بينما المجموع
  الكلي (`total_amount`) يأخذ عملة الطلب الفعلية. تم توحيدهما على عملة الطلب نفسها.

---

## بوابات لم تحتج أي تعديل (كانت مطابقة تماماً للتوثيق الرسمي)
- **HyperPay**: نقاط الوصول، الاستيثاق، ومعادلة أكواد النجاح `/^(000\.000\.|000\.100\.1|000\.[36])/`
  مطابقة حرفياً لتوثيق OPPWA/HyperPay.
- **PayTabs**: `/payment/request` و`/payment/query` وترويسة Authorization مطابقة لـ API v2.
- **Moyasar**: `/v1/invoices` مع Basic Auth والمبلغ بالهللة (minor units) مطابق تماماً.

## توصية عامة قبل الإطلاق
شغّل كل بوابة على وضع **Test** أولاً وجرّب دورة دفع كاملة (نجاح + فشل + Webhook) قبل التحويل لـ Live،
كما هو موضّح في `README_PAYMENTS.md`.
