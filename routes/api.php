<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\SlideshowsApiController;
use App\Http\Controllers\Api\V1\TextpagesApiController;
use App\Http\Controllers\Api\V1\MessagescontactusApiController;
use App\Http\Controllers\Api\V1\SettingsApiController;
use App\Http\Controllers\Api\V1\DiscountCodeApiController;
use App\Http\Controllers\Api\V1\OnboardingsApiController;
use App\Http\Controllers\Api\V1\Client\ClientApiController;
use App\Http\Controllers\Api\V1\Auth\AuthCommonApiController;
use App\Http\Controllers\Api\V1\CategoriesApiController;
use App\Http\Controllers\Api\V1\HomeApiController;
use App\Http\Controllers\Api\V1\NotificationApiController;
use App\Http\Controllers\Api\V1\Client\WishlistApiController;
use App\Http\Controllers\Api\V1\Client\CartApiController;
use App\Http\Controllers\Api\V1\Client\OrderreviewsApiController;
use App\Http\Controllers\Api\V1\Client\DriverReviewsApiController;
use App\Http\Controllers\Api\V1\Client\OrderCancelReasonsApiController;
use App\Http\Controllers\Api\V1\Client\OrdersClientApiController;
use App\Http\Controllers\Api\V1\Client\AddressApiController;
use App\Http\Controllers\Api\V1\Client\LoyaltyPointsApiController;
use App\Http\Controllers\Api\V1\Client\PaymentApiController;
use App\Http\Controllers\Api\V1\BrandsApiController;



Route::group(['middleware' => ['guest:sanctum', 'api', 'apiChangeLanguage', 'apiCheckUse', 'checkMaintenance']], function () {


    #################################################### Start General   ####################################################
    Route::post('get_AboutApp', [TextpagesApiController::class, 'get_AboutApp']);
    Route::post('get_TermsConditions', [TextpagesApiController::class, 'get_TermsConditions']);
    Route::post('set_MessageContact', [MessagescontactusApiController::class, 'set_MessageContact']);
    Route::post('get_Slideshows', [SlideshowsApiController::class, 'get_Slideshows']);
    Route::post('get_Onboardings', [OnboardingsApiController::class, 'get_Onboardings']);
    Route::post('getsettings_added_tax', [SettingsApiController::class, 'getsettings_added_tax']);
    Route::post('getsettings_whattapp', [SettingsApiController::class, 'getsettings_whattapp']);
    Route::post('checkdiscountcode', [DiscountCodeApiController::class, 'checkdiscountcode']);
    Route::post('getsettings', [SettingsApiController::class, 'getsettings']);

    #################################################### End General   ####################################################


    #################################################### Start Client   ####################################################

      // ----- التسجيل -----
    Route::post('client_Register', [ClientApiController::class, 'register'])
        ->middleware('throttle:10,1');
    Route::post('check_active_code', [ClientApiController::class, 'check_active_code'])
        ->middleware('throttle:5,1'); // حماية من Brute Force على الكود

    Route::post('rsend_active_code', [ClientApiController::class, 'rsend_active_code'])
        ->middleware('throttle:3,86400');




    // ----- تسجيل الدخول -----
    Route::post('login', [AuthCommonApiController::class, 'login'])
        ->middleware('throttle:10,1');

    Route::post('check_login_code', [AuthCommonApiController::class, 'check_login_code'])
        ->middleware('throttle:5,1');

    Route::post('rsend_login_code', [AuthCommonApiController::class, 'rsend_login_code'])
        ->middleware('throttle:3,86400');

    // ----- نسيان كلمة المرور -----
    Route::post('foreget_Password', [AuthCommonApiController::class, 'foreget_Password'])
        ->middleware('throttle:3,86400');

    Route::post('foregetPassword_CheckCode', [AuthCommonApiController::class, 'foregetPassword_CheckCode'])
        ->middleware('throttle:5,1');

    Route::post('foregetPassword_ResetPassword', [AuthCommonApiController::class, 'foregetPassword_ResetPassword'])
        ->middleware('throttle:5,1');

    Route::post('get_Categories', [CategoriesApiController::class, 'index']);
    Route::post('get_CategoryDetails', [CategoriesApiController::class, 'show']); // <-- الجديد
   // العلامات التجارية 👇
    Route::post('get_Brands', [BrandsApiController::class, 'index']);
    Route::post('get_BrandDetails', [BrandsApiController::class, 'show']);
    Route::post('get_search_products', [HomeApiController::class, 'get_search_products']);
    Route::post('getHomeProduct', [HomeApiController::class, 'getHomeProduct']);
    Route::post('getProductsByCategory', [HomeApiController::class, 'getProductsByCategory']);
    Route::post('getProductDetails', [HomeApiController::class, 'getProductDetails']);
    Route::post('calculateCart', [CartApiController::class, 'calculateCart']);
    Route::post('getStoreStatus', [SettingsApiController::class, 'getStoreStatus']);
    Route::post('get_order_cancel_reasons', [OrderCancelReasonsApiController::class, 'index']);


    #################################################### End Client   ####################################################

});


Route::group(['middleware' => ['auth:sanctum', 'api', 'apiCheckUse', 'apiChangeLanguage', 'ApiAutoLogoutUserInactive', 'checkMaintenance']], function () {

    Route::post('updatePassword', [AuthCommonApiController::class, 'updatePassword']);
    Route::post('destroyaccount', [AuthCommonApiController::class, 'destroy']);
    Route::post('logout', [AuthCommonApiController::class, 'logout']);
    Route::post('get_ClientInfo', [ClientApiController::class, 'get_ClientInfo']);
    Route::post('client_Update', [ClientApiController::class, 'client_Update']);


    #################################################### Start Wishlist   ####################################################

    Route::post('store_Wishlist', [WishlistApiController::class, 'store']);
    Route::post('get_Wishlist', [WishlistApiController::class, 'index']);
    Route::post('delete_Wishlist', [WishlistApiController::class, 'destroy']);

    #################################################### End Wishlist   ####################################################

    #################################################### Start Notifications   ####################################################

    Route::post('get_Notifications', [NotificationApiController::class, 'index']);
    Route::post('delete_Notifications', [NotificationApiController::class, 'destroy']);
    Route::post('getUnreadCount_Notifications', [NotificationApiController::class, 'getUnreadCount']);
    Route::post('markAllAsRead_Notifications', [NotificationApiController::class, 'markAllAsRead']);

    #################################################### End Notifications   ####################################################



    #################################################### Start Carts   ####################################################
    Route::post('AddCart', [CartApiController::class, 'store']);
    #################################################### End Carts   ####################################################

    #################################################### Start Addresses   ####################################################
    Route::post('get_Addresses', [AddressApiController::class, 'index']);
    Route::post('add_Address', [AddressApiController::class, 'store']);
    Route::post('update_Address/{id}', [AddressApiController::class, 'update']);
    Route::post('delete_Address/{id}', [AddressApiController::class, 'destroy']);
    Route::post('setDefault_Address/{id}', [AddressApiController::class, 'setDefault']);
    Route::post('calculateDeliveryPrice', [AddressApiController::class, 'calculateDeliveryPrice']);
    #################################################### End Addresses   ####################################################


    #################################################### Start Reviews   ####################################################
    Route::post('reviewStore', [OrderreviewsApiController::class, 'store']);
    Route::post('reviewDriver', [DriverReviewsApiController::class, 'store']);
    #################################################### End Reviews   ####################################################

    #################################################### Start Cancell Order   ####################################################
    Route::post('client_Cancell_Order', [OrdersClientApiController::class, 'client_Cancell_Order']);
    #################################################### End Cancell Order   ####################################################


    #################################################### Start Client Orders   ####################################################
    Route::post('client_Current_Orders', [OrdersClientApiController::class, 'client_Current_Orders']);
    Route::post('client_End_Orders', [OrdersClientApiController::class, 'client_End_Orders']);
    Route::post('client_Cancelled_Orders', [OrdersClientApiController::class, 'client_Cancelled_Orders']);
    #################################################### End Client Orders   ####################################################


    Route::post('getLoyaltyPointsBalance', [LoyaltyPointsApiController::class, 'getBalance']);
    Route::post('getLoyaltyPointsHistory', [LoyaltyPointsApiController::class, 'getHistory']);

    #################################################### Start Payments   ####################################################
    Route::post('get_payment_methods', [PaymentApiController::class, 'get_payment_methods']);
    Route::post('initiate_payment', [PaymentApiController::class, 'initiate_payment']);
    #################################################### End Payments   ####################################################

});

// ============================================================
// =================== Driver API Routes ======================
// ============================================================
// السائق يستخدم نفس login/logout/updatePassword من الـ Auth العام
// الفرق: الرد يحتوي role = "driver" فيوجّه التطبيق للواجهة الصحيحة
// ============================================================

use App\Http\Controllers\Api\V1\Driver\DriverOrdersApiController;
use App\Http\Controllers\Api\V1\Driver\DriverProfileApiController;

Route::group([
    'prefix'     => 'driver',
    'middleware' => ['auth:sanctum', 'api', 'apiCheckUse', 'apiChangeLanguage', 'ensureIsDriver', 'checkMaintenance'],
], function () {

    #################################################### Profile ####################################################
    Route::post('get_DriverInfo',         [DriverProfileApiController::class, 'get_DriverInfo']);
    Route::post('driver_Update',          [DriverProfileApiController::class, 'driver_Update']);
    #################################################### Wallet ####################################################
    Route::post('getWalletBalance',       [DriverProfileApiController::class, 'getWalletBalance']);
    Route::post('getWalletTransactions',  [DriverProfileApiController::class, 'getWalletTransactions']);
    #################################################### Orders ####################################################
    // الطلبيات المعيّنة للسائق (جاهزة للاستلام أو قيد التوصيل)
    Route::post('my_Orders',              [DriverOrdersApiController::class, 'my_Orders']);
    // الطلبيات قيد التوصيل (status=4) — تُعرض فيها delivery_pin
    Route::post('in_Delivery_Orders',     [DriverOrdersApiController::class, 'in_Delivery_Orders']);
    // تفاصيل طلب معيّن
    Route::post('order_Details',          [DriverOrdersApiController::class, 'order_Details']);
    // قبول الطلب (3 → 4)
    Route::post('accept_Order',           [DriverOrdersApiController::class, 'accept_Order']);
    // تأكيد التسليم بكود (4 → 5)
    Route::post('deliver_Order',          [DriverOrdersApiController::class, 'deliver_Order']);
    // سجل الطلبيات المنتهية
    Route::post('order_History',          [DriverOrdersApiController::class, 'order_History']);
    #################################################### End Driver ####################################################
});

// ============================================================
// =================== Payment Callbacks =======================
// ============================================================
// روابط عامة (بدون توكن) يستدعيها المتصفح (return) أو سيرفر بوابة الدفع مباشرة (webhook)
// سجّل رابط الـ webhook التالي في لوحة كل بوابة دفع:
//   {APP_URL}/api/payments/webhook/{gateway}   مثال: /api/payments/webhook/stripe
// ============================================================

Route::group(['middleware' => ['api'], 'prefix' => 'payments'], function () {
    Route::get('return/{gateway}', [PaymentApiController::class, 'payment_return'])->name('api.payments.return');
    Route::match(['get', 'post'], 'webhook/{gateway}', [PaymentApiController::class, 'payment_webhook'])->name('api.payments.webhook');
});
