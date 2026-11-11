<?php

use Illuminate\Support\Facades\Route;
use \Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use App\Http\Controllers\Dashboard\LoginController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Dashboard\ProfileController;
use App\Http\Controllers\Dashboard\ForgotAdminsPasswordController;
use App\Http\Controllers\Dashboard\SettingsController;
use App\Http\Controllers\Dashboard\SocialmedialinksController;
use App\Http\Controllers\Dashboard\OnboardingsController;
use App\Http\Controllers\Dashboard\TextpagesController;
use App\Http\Controllers\Dashboard\MessagescontactusController;
use App\Http\Controllers\Dashboard\SlideshowsController;
use App\Http\Controllers\Dashboard\BrandsController;
use App\Http\Controllers\Dashboard\AttributesController;
use App\Http\Controllers\Dashboard\AttributeValueController;
use App\Http\Controllers\Dashboard\CategoriesController;
use App\Http\Controllers\Dashboard\CategorySlidersController;
use App\Http\Controllers\Dashboard\DiscountCodeController;
use App\Http\Controllers\Dashboard\ProductsController;
use App\Http\Controllers\Dashboard\ImagestoreController;
use App\Http\Controllers\Dashboard\ClientsController;
use App\Http\Controllers\Dashboard\OrderstatusesController;
use App\Http\Controllers\Dashboard\OrderCancelReasonsController;
use App\Http\Controllers\Dashboard\OrderController;
use App\Http\Controllers\Dashboard\NotificationController;
use App\Http\Controllers\Dashboard\ReportController;
use App\Http\Controllers\Dashboard\EmployeesController;
use App\Http\Controllers\Dashboard\DriverController;
use App\Http\Controllers\Dashboard\PaymentMethodsController;
use App\Http\Controllers\Dashboard\UnitsController;
use App\Http\Controllers\Dashboard\AdminNotificationController;
use App\Http\Controllers\Dashboard\WalletClientController;
use App\Http\Controllers\Dashboard\WalletDriverController;
use App\Http\Controllers\Dashboard\WalletTraderController;

// === مواشي وسيط: Controllers لوحة التحكم ===
use App\Http\Controllers\Dashboard\TradersController;
use App\Http\Controllers\Dashboard\CommissionsController;
use App\Http\Controllers\Dashboard\DepositsController;
use App\Http\Controllers\Dashboard\ReviewsController;
use App\Http\Controllers\Dashboard\AdminWalletController;
use App\Http\Controllers\Dashboard\NotificationTemplatesController;
use App\Http\Controllers\Dashboard\SlaughtersController;
use App\Http\Controllers\Dashboard\GeoAreasController;
use App\Http\Controllers\Dashboard\ProductsApprovalsController;


Route::group(['middleware' => ['auth:admin', 'AutoLogoutEmployeeInactive', 'dashboardAccess']], function () {
    Route::any('/ckfinder/connector', '\CKSource\CKFinderBridge\Controller\CKFinderController@requestAction')
        ->name('ckfinder_connector');

    Route::any('/ckfinder/browser', '\CKSource\CKFinderBridge\Controller\CKFinderController@browserAction')
        ->name('ckfinder_browser');
});
Route::group(
    [
        'prefix' => LaravelLocalization::setLocale(),
        'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath']
    ],
    function () {

        Route::group(['namespace' => 'Dashboard', 'middleware' => 'guest', 'prefix' => 'admin'], function () {
            Route::get('login', [LoginController::class, 'login'])->name('admin.login');
            Route::post('login', [LoginController::class, 'postlogin'])->name('admin.postlogin');
            Route::get('ForgotPassword', [ForgotAdminsPasswordController::class, 'getEmail'])->name('admin.forgotpassword');
            Route::post('ForgotPassword', [ForgotAdminsPasswordController::class, 'postEmail'])->name('admin.forgotpassword.postEmail');
            Route::get('reset-password/{token}', [ForgotAdminsPasswordController::class, 'getPassword'])->name('admin.reset-password');
            Route::post('reset-password', [ForgotAdminsPasswordController::class, 'updatePassword'])->name('admin.reset-password.update');
        });
        Route::group(['middleware' => ['auth:admin', 'AutoLogoutEmployeeInactive', 'dashboardAccess'], 'prefix' => 'admin'], function () {
            Route::get('/', [DashboardController::class, 'index'])->name('admin.dashboard');

            Route::get('logout', [LoginController::class, 'logout'])->name('admin.logout');
            Route::get('editpassword', [ProfileController::class, 'editePassword'])->name('edit.password');
            Route::put('updatepassword', [ProfileController::class, 'updatePassword'])->name('update.password');

            #################################################### Profile route  ####################################################

            Route::group(['prefix' => 'profile'], function () {
                Route::get('edit', [ProfileController::class, 'editProfile'])->name('edit.profile');
                Route::put('update', [ProfileController::class, 'updateProfile'])->name('update.profile');
            });
            ####################################################  End Profile   ####################################################



            #################################################### Settings route  ####################################################
            Route::group(['prefix' => 'settings'], function () {
                Route::get('generalsettings', [SettingsController::class, 'generalsettings'])->name('edit.generalsettings');
                Route::post('updategeneralsettings', [SettingsController::class, 'updategeneralsettings'])->name('update.generalsettings');

                Route::get('generalstoresettings', [SettingsController::class, 'generalstoresettings'])->name('edit.generalstoresettings');
                Route::post('updatestoregeneralsettings', [SettingsController::class, 'updatestoregeneralsettings'])->name('update.updatestoregeneralsettings');

                Route::get('generalpolicystore', [SettingsController::class, 'generalpolicystore'])->name('edit.generalpolicystore');
                Route::post('updategeneralpolicystore', [SettingsController::class, 'updategeneralpolicystore'])->name('update.updategeneralpolicystore');

                Route::get('generaltimesdayswork', [SettingsController::class, 'generaltimesdayswork'])->name('edit.generaltimesdayswork');
                Route::post('updategeneraltimesdayswork', [SettingsController::class, 'updategeneraltimesdayswork'])->name('update.updategeneraltimesdayswork');

                Route::get('generaldeliverysettings', [SettingsController::class, 'generaldeliverysettings'])->name('edit.generaldeliverysettings');
                Route::post('updategeneraldeliverysettings', [SettingsController::class, 'updategeneraldeliverysettings'])->name('update.updategeneraldeliverysettings');

                Route::get('generalverificationmethodsettings', [SettingsController::class, 'generalverificationmethodsettings'])->name('edit.generalverificationmethodsettings');
                Route::post('updategeneralverificationmethodsettings', [SettingsController::class, 'updategeneralverificationmethodsettings'])->name('update.updategeneralverificationmethodsettings');

          Route::get('generalloyaltypointssettings', [SettingsController::class, 'generalloyaltypointssettings'])->name('edit.generalloyaltypointssettings');
                Route::post('updateloyaltypointssettings', [SettingsController::class, 'updateloyaltypointssettings'])->name('update.updateloyaltypointssettings');


            });
            ####################################################  End Settings   ####################################################

            #################################################### Payment Methods route  ####################################################
            Route::group(['prefix' => 'payment-methods'], function () {
                Route::get('/', [PaymentMethodsController::class, 'index'])->name('paymentmethods');
                Route::get('{code}/edit', [PaymentMethodsController::class, 'edit'])->name('paymentmethods.edit');
                Route::put('{code}', [PaymentMethodsController::class, 'update'])->name('paymentmethods.update');
                Route::post('{code}/toggle', [PaymentMethodsController::class, 'toggle'])->name('paymentmethods.toggle');
            });
            ####################################################  End Payment Methods   ####################################################


            #################################################### Socialmedialinks route  ####################################################
            Route::prefix('socialmedialinks')->controller(SocialmedialinksController::class)->group(function () {
                Route::get('/', 'index')->name('socialmedialinks');
                Route::get('create',  'create')->name('socialmedialinks.create');
                Route::post('store',  'store')->name('socialmedialinks.store');
                Route::get('edit/{id}',  'edit')->name('socialmedialinks.edit');
                Route::post('update/{id}',  'update')->name('socialmedialinks.update');
                Route::get('delete/{id}',  'destroy')->name('socialmedialinks.delete');
                Route::post('DeleteAll', 'deleteAll')->name('socialmedialinks.deleteAll');
            });
            #################################################### Socialmedialinks route  ####################################################


            #################################################### Onboardings route  ####################################################
            Route::prefix('onboardings')->controller(OnboardingsController::class)->group(function () {
                Route::get('/', 'index')->name('onboardings');
                Route::get('create',  'create')->name('onboardings.create');
                Route::post('store',  'store')->name('onboardings.store');
                Route::get('edit/{id}',  'edit')->name('onboardings.edit');
                Route::post('update/{id}',  'update')->name('onboardings.update');
                Route::get('delete/{id}',  'destroy')->name('onboardings.delete');
                Route::post('DeleteAll', 'deleteAll')->name('onboardings.deleteAll');
            });
            #################################################### Onboardings route  ####################################################



            #################################################### TextPages route  ####################################################
            Route::group(['prefix' => 'textpages'], function () {
                Route::get('/', [TextpagesController::class, 'index'])->name('textpages');
                Route::get('create', [TextpagesController::class, 'create'])->name('textpages.create');
                Route::post('store', [TextpagesController::class, 'store'])->name('textpages.store');
                Route::get('edit/{id}', [TextpagesController::class, 'edit'])->name('textpages.edit');
                Route::post('update/{id}', [TextpagesController::class, 'update'])->name('textpages.update');
                Route::get('edittext/{slug}', [TextpagesController::class, 'edittext'])->name('textpages.edittext');
                Route::post('updatetext/{id}', [TextpagesController::class, 'updatetext'])->name('textpages.updatetext');
                Route::get('delete/{id}', [TextpagesController::class, 'destroy'])->name('textpages.delete');
                Route::post('DeleteAll', [TextpagesController::class, 'deleteAll'])->name('textpages.deleteAll');
            });
            #################################################### End TextPages   ####################################################


            #################################################### Contact Us route  ####################################################
            Route::prefix('messagescontactus')->controller(MessagescontactusController::class)->group(function () {
                Route::get('/', 'index')->name('messagescontactus');
                Route::get('show/{id}', 'show')->name('messagescontactus.show');
                Route::get('delete/{id}', 'destroy')->name('messagescontactus.delete');
                Route::post('DeleteAll', 'deleteAll')->name('messagescontactus.deleteAll');
            });
            #################################################### End Contact Us   ####################################################


            #################################################### Slideshows route  ####################################################

            Route::prefix('slideshows')->controller(SlideshowsController::class)->group(function () {
                Route::get('/', 'index')->name('slideshows');
                Route::get('create',  'create')->name('slideshows.create');
                Route::post('store',  'store')->name('slideshows.store');
                Route::get('edit/{id}',  'edit')->name('slideshows.edit');
                Route::post('update/{id}',  'update')->name('slideshows.update');
                Route::get('delete/{id}',  'destroy')->name('slideshows.delete');
                Route::post('DeleteAll', 'deleteAll')->name('slideshows.deleteAll');
            });

            #################################################### Slideshows route  ####################################################


            #################################################### Brands route  ####################################################
            Route::prefix('brands')->controller(BrandsController::class)->group(function () {
                Route::get('/', 'index')->name('brands');
                Route::get('create',  'create')->name('brands.create');
                Route::post('store',  'store')->name('brands.store');
                Route::get('edit/{id}',  'edit')->name('brands.edit');
                Route::post('update/{id}',  'update')->name('brands.update');
                Route::get('delete/{id}',  'destroy')->name('brands.delete');
                Route::post('DeleteAll', 'deleteAll')->name('brands.deleteAll');
            });
            #################################################### Brands route  ####################################################


            #################################################### Units route  ####################################################
            Route::prefix('units')->controller(UnitsController::class)->group(function () {
                Route::get('/', 'index')->name('units');
                Route::get('create',  'create')->name('units.create');
                Route::post('store',  'store')->name('units.store');
                Route::get('edit/{id}',  'edit')->name('units.edit');
                Route::post('update/{id}',  'update')->name('units.update');
                Route::get('delete/{id}',  'destroy')->name('units.delete');
                Route::post('DeleteAll', 'deleteAll')->name('units.deleteAll');
            });
            #################################################### Units route  ####################################################

           #################################################### Attributes route  ####################################################
            Route::prefix('attributes')->controller(AttributesController::class)->group(function () {
                Route::get('/', 'index')->name('attributes');
                Route::get('create',  'create')->name('attributes.create');
                Route::post('store',  'store')->name('attributes.store');
                Route::get('edit/{id}',  'edit')->name('attributes.edit');
                Route::post('update/{id}',  'update')->name('attributes.update');
                Route::get('delete/{id}',  'destroy')->name('attributes.delete');
                Route::post('DeleteAll', 'deleteAll')->name('attributes.deleteAll');
            });
            #################################################### Attributes route  ####################################################


            #################################################### AttributeValue route  ####################################################
            Route::prefix('attributevalues')->controller(AttributeValueController::class)->group(function () {
                Route::get('/{attribute}', 'index')->name('attributevalues');
                Route::get('create/{attribute}',  'create')->name('attributevalues.create');
                Route::post('store/{attribute}',  'store')->name('attributevalues.store');
                Route::get('edit/{id}',  'edit')->name('attributevalues.edit');
                Route::post('update/{id}',  'update')->name('attributevalues.update');
                Route::get('delete/{id}',  'destroy')->name('attributevalues.delete');
                Route::post('DeleteAll', 'deleteAll')->name('attributevalues.deleteAll');
            });
            #################################################### AttributeValue route  ####################################################


            #################################################### Categories route  ####################################################
            Route::prefix('categories')->controller(CategoriesController::class)->group(function () {
                Route::get('/', 'index')->name('categories');
                Route::get('create',  'create')->name('categories.create');
                Route::post('store',  'store')->name('categories.store');
                Route::get('edit/{id}',  'edit')->name('categories.edit');
                Route::post('update/{id}',  'update')->name('categories.update');
                Route::get('delete/{id}',  'destroy')->name('categories.delete');
                Route::post('DeleteAll', 'deleteAll')->name('categories.deleteAll');
            });
            #################################################### Categories route  ####################################################

            #################################################### CategorySlider route  ####################################################
            Route::prefix('categorysliders')->controller(CategorySlidersController::class)->group(function () {
                Route::get('/{category}', 'index')->name('categorysliders');
                Route::get('create/{category}',  'create')->name('categorysliders.create');
                Route::post('store/{category}',  'store')->name('categorysliders.store');
                Route::get('edit/{id}',  'edit')->name('categorysliders.edit');
                Route::post('update/{id}',  'update')->name('categorysliders.update');
                Route::get('delete/{id}',  'destroy')->name('categorysliders.delete');
                Route::post('DeleteAll', 'deleteAll')->name('categorysliders.deleteAll');
            });
            #################################################### CategorySlider route  ####################################################

            #################################################### DiscountCodes route  ####################################################
            Route::prefix('discountcodes')->controller(DiscountCodeController::class)->group(function () {
                Route::get('/',  'index')->name('discountcodes');
                Route::get('create',  'create')->name('discountcodes.create');
                Route::post('store',  'store')->name('discountcodes.store');
                Route::get('edit/{id}',  'edit')->name('discountcodes.edit');
                Route::post('update/{id}',  'update')->name('discountcodes.update');
                Route::get('delete/{id}',  'destroy')->name('discountcodes.delete');
                Route::post('DeleteAll',  'deleteAll')->name('discountcodes.deleteAll');
                Route::get('user_usage/{id}',  'user_usage')->name('discountcodes.user_usage');
            });
            #################################################### End DiscountCodes   ####################################################

    #################################################### Products route  ####################################################
            Route::prefix('products')->controller(ProductsController::class)->group(function () {
                Route::get('/',  'index')->name('products');
                Route::get('create',  'create')->name('products.create');
                Route::post('store',  'store')->name('products.store');
                Route::get('edit/{id}',  'edit')->name('products.edit');
                Route::post('update/{id}',  'update')->name('products.update');
                Route::get('delete/{id}', 'destroy')->name('products.delete');
                Route::post('DeleteAll', 'deleteAll')->name('products.deleteAll');
                Route::get('editdescription/{id}', 'editdescription')->name('products.editdescription');
                Route::post('updatedescription', 'updatedescription')->name('products.updatedescription');
                Route::get('images/{id}', 'productImages')->name('products.images');
                Route::get('editattribute/{id}', 'editattribute')->name('products.editattribute');
                Route::post('updateattribute/{product}', 'updateattribute')->name('products.updateattribute');
                Route::get('/product/{id}/attributes', 'getProductAttributes');
                Route::get('editVariants/{id}', 'editVariants')->name('products.editVariants');
                Route::post('updateVariants/{product}', 'updateVariants')->name('products.updateVariants');
                Route::get('editModifiers/{id}', 'editModifiers')->name('products.editModifiers');
                Route::post('updateModifiers/{product}', 'updateModifiers')->name('products.updateModifiers');

                # === مواشي وسيط (البند الثالث): إيقاف/تفعيل + إدارة المخزون ===
                Route::post('toggleActive/{id}', 'toggleActive')->name('products.toggleActive');
                Route::post('updateStock/{id}',  'updateStock')->name('products.updateStock');
            });
            #################################################### Products route  ####################################################

            #################################################### Imagesstore route  ####################################################

            Route::prefix('imagesstore')->controller(ImagestoreController::class)->group(function () {
                Route::post('storeproduct', 'storeproduct')->name('imagesstore.storeproduct');
                Route::post('delete', 'destroy')->name('imagesstore.delete');
            });
            #################################################### End Imagesstore  ####################################################



            #################################################### Clients route  ####################################################
            Route::prefix('clients')->controller(ClientsController::class)->group(function () {
                Route::get('/',  'index')->name('clients');
                Route::get('create',  'create')->name('clients.create');
                Route::post('store',  'store')->name('clients.store');
                Route::get('edit/{id}',  'edit')->name('clients.edit');
                Route::post('update/{id}',  'update')->name('clients.update');
                Route::get('delete/{id}', 'destroy')->name('clients.delete');
                Route::post('DeleteAll', 'deleteAll')->name('clients.deleteAll');
                Route::post('sendingmessage/{id}',  'sendingmessage')->name('clients.sendingmessage');
                Route::post('changepassword/{id}',  'changepassword')->name('clients.changepassword');
                Route::post('changeactive/{id}',  'changeactive')->name('clients.ChangeActive');
                 Route::post('changeblock/{id}',  'ChangeBlock')->name('clients.ChangeBlock');
                Route::get('show/{id}',  'show')->name('clients.show');
                Route::get('Export_Excel', 'export_excel')->name('clients.export_excel');
            });
            #################################################### End Clients   ####################################################

            #################################################### Orderstatuses route  ####################################################
            Route::prefix('orderstatuses')->controller(OrderstatusesController::class)->group(function () {
                Route::get('/',  'index')->name('orderstatuses');
                Route::get('create',  'create')->name('orderstatuses.create');
                Route::post('store',  'store')->name('orderstatuses.store');
                Route::get('edit/{id}',  'edit')->name('orderstatuses.edit');
                Route::post('update/{id}',  'update')->name('orderstatuses.update');
                Route::get('delete/{id}',  'destroy')->name('orderstatuses.delete');
                Route::post('DeleteAll',  'deleteAll')->name('orderstatuses.deleteAll');
            });
            #################################################### End Orderstatuses   ####################################################


            #################################################### OrderCancelReasons route  ####################################################
            Route::prefix('ordercancelreasons')->controller(OrderCancelReasonsController::class)->group(function () {
                Route::get('/', 'index')->name('ordercancelreasons');
                Route::get('create',  'create')->name('ordercancelreasons.create');
                Route::post('store',  'store')->name('ordercancelreasons.store');
                Route::get('edit/{id}',  'edit')->name('ordercancelreasons.edit');
                Route::post('update/{id}',  'update')->name('ordercancelreasons.update');
                Route::get('delete/{id}',  'destroy')->name('ordercancelreasons.delete');
                Route::post('DeleteAll', 'deleteAll')->name('ordercancelreasons.deleteAll');
            });
            #################################################### OrderCancelReasons route  ####################################################


            #################################################### Orders route  ####################################################
            Route::prefix('orders')->controller(OrderController::class)->group(function () {
                Route::get('/',  'index')->name('orders');
                Route::get('show/{id}', 'show')->name('orders.show');
                Route::get('delete/{id}', 'destroy')->name('orders.delete');
                Route::post('DeleteAll', 'deleteAll')->name('orders.deleteAll');
                Route::post('changeorder_status',  'changeorder_status')->name('orders.changeorder_status');
                Route::post('changeorder_payment_status',  'changeorder_payment_status')->name('orders.changeorder_payment_status');
                Route::get('invoice/{id}/{pdf?}', 'invoice')->name('orders.invoice');
                Route::get('Export_Excel', 'export_excel')->name('orders.export_excel');
                // تعيين / إلغاء تعيين السائق
                Route::post('assign_driver',   'assignDriver')->name('orders.assign_driver');
                Route::post('unassign_driver', 'unassignDriver')->name('orders.unassign_driver');
            });
            #################################################### Orders route  ####################################################

            #################################################### Notifications route  ####################################################
            Route::prefix('notifications')->controller(NotificationController::class)->group(function () {
                Route::get('create',  'create')->name('notifications.create');
                Route::post('store',  'store')->name('notifications.store');
            });
            #################################################### End Notifications   ####################################################

            #################################################### Reports route  ####################################################
            Route::prefix('reports')->controller(ReportController::class)->group(function () {
                Route::get('salesReport',  'salesReport')->name('reports.sales');
                Route::get('export_sales_excel', 'export_sales_excel')->name('reports.export_sales_excel');
                Route::get('product_purchased',  'product_purchased')->name('reports.product_purchased');
            });
            #################################################### End Reports   ####################################################
            #################################################### Employees route  ####################################################
            Route::prefix('employees')->controller(EmployeesController::class)->group(function () {
                Route::get('/',  'index')->name('employees');
                Route::get('create',  'create')->name('employees.create');
                Route::post('store',  'store')->name('employees.store');
                Route::get('edit/{id}',  'edit')->name('employees.edit');
                Route::post('update/{id}',  'update')->name('employees.update');
                Route::get('delete/{id}', 'destroy')->name('employees.delete');
                Route::post('DeleteAll', 'deleteAll')->name('employees.deleteAll');
                Route::post('changepassword/{id}',  'changepassword')->name('employees.changepassword');
            });
            #################################################### End Employees   ####################################################

            #################################################### Drivers route  ####################################################
            Route::prefix('drivers')->controller(DriverController::class)->group(function () {
                Route::get('/',  'index')->name('drivers');
                Route::get('pending',  'pending')->name('drivers.pending');
                Route::get('create',  'create')->name('drivers.create');
                Route::post('store',  'store')->name('drivers.store');
                Route::get('edit/{id}',  'edit')->name('drivers.edit');
                Route::post('update/{id}',  'update')->name('drivers.update');
                Route::get('delete/{id}', 'destroy')->name('drivers.delete');
                Route::post('DeleteAll', 'deleteAll')->name('drivers.deleteAll');
                Route::post('sendingmessage/{id}',  'sendingmessage')->name('drivers.sendingmessage');
                Route::post('changepassword/{id}',  'changepassword')->name('drivers.changepassword');
                Route::post('changeactive/{id}',  'changeactive')->name('drivers.ChangeActive');
                Route::post('changeblock/{id}',  'ChangeBlock')->name('drivers.ChangeBlock');
                Route::get('show/{id}',  'show')->name('drivers.show');
                Route::get('Export_Excel', 'export_excel')->name('drivers.export_excel');

                # === اعتماد / رفض / إيقاف / إعادة تفعيل / عمولة السائق (البند الرابع) ===
                Route::post('{id}/approve',          'approve')->name('drivers.approve');
                Route::post('{id}/reject',           'reject')->name('drivers.reject');
                Route::post('{id}/suspend',          'suspend')->name('drivers.suspend');
                Route::post('{id}/reactivate',       'reactivate')->name('drivers.reactivate');
                Route::post('{id}/updateCommission',  'updateCommission')->name('drivers.updateCommission');
            });
            #################################################### End Drivers   ####################################################


            #################################################### Admin Notifications route  ####################################################
            Route::prefix('admin-notifications')->controller(AdminNotificationController::class)->group(function () {
                Route::get('/', 'fetch')->name('admin.notifications.fetch');
                Route::post('/mark-read', 'markAllRead')->name('admin.notifications.markRead');
            });
            #################################################### End Admin Notifications route  ####################################################



            #################################################### Wallets drivers route  ####################################################
Route::prefix('walletsdrivers')->controller(WalletDriverController::class)->group(function () {
    Route::get('/', 'index')->name('walletsdrivers');
    Route::get('{id}/transactions', 'transactions')->name('walletsdrivers.transactions');
    Route::get('withdrawal-requests', 'withdrawalRequests')->name('walletsdrivers.withdrawalRequests');
    Route::post('withdrawal-requests/{id}/approve', 'approveWithdrawal')->name('walletsdrivers.approveWithdrawal');
    Route::post('withdrawal-requests/{id}/reject', 'rejectWithdrawal')->name('walletsdrivers.rejectWithdrawal');
    Route::post('{id}/settle', 'manualSettlement')->name('walletsdrivers.settle');
});
#################################################### End Wallets drivers route  ####################################################



#################################################### Wallets clients route  ####################################################
Route::prefix('walletsclients')->controller(WalletClientController::class)->group(function () {
    Route::get('/', 'index')->name('walletsclients');
    Route::get('{id}/transactions', 'transactions')->name('walletsclients.transactions');
});
#################################################### End Wallets clients route  ####################################################



#################################################### Wallets traders route  ####################################################
Route::prefix('walletstraders')->controller(WalletTraderController::class)->group(function () {
    Route::get('/', 'index')->name('walletstraders');
    Route::get('{id}/transactions', 'transactions')->name('walletstraders.transactions');
});
#################################################### End Wallets traders route  ####################################################



        #################################################### مواشي وسيط: مسارات التجار + العمولات + العربون + الذبح + الجغرافيا ####################################################

        // ====== التجار ======
        Route::prefix('traders')->controller(TradersController::class)->group(function () {
            Route::get('/', 'index')->name('traders.index');
            Route::get('/pending', 'pending')->name('traders.pending');
            Route::get('/create', 'create')->name('traders.create');
            Route::post('/store', 'store')->name('traders.store');
            Route::get('/{id}/edit', 'edit')->name('traders.edit');
            Route::post('/{id}/update', 'update')->name('traders.update');
            Route::get('/{id}/delete', 'destroy')->name('traders.delete');
            Route::get('/{id}', 'show')->name('traders.show');
            Route::post('/{id}/approve', 'approve')->name('traders.approve');
            Route::post('/{id}/reject', 'reject')->name('traders.reject');
            Route::post('/{id}/suspend', 'suspend')->name('traders.suspend');
            Route::post('/{id}/reactivate', 'reactivate')->name('traders.reactivate');
            Route::post('/{id}/updateCommission', 'updateCommission')->name('traders.updateCommission');
            Route::post('/{id}/setCommissionRate', 'setCommissionRate')->name('traders.setCommissionRate');
        });

        // ====== اعتماد منتجات التجار ======
        Route::prefix('products-approvals')->controller(ProductsApprovalsController::class)->group(function () {
            Route::get('/pending', 'pending')->name('products-approvals.pending');
            Route::post('/{id}/approve', 'approve')->name('products-approvals.approve');
            Route::post('/{id}/reject', 'reject')->name('products-approvals.reject');
            Route::post('/{id}/suspend', 'suspend')->name('products-approvals.suspend');
            Route::post('/{id}/reactivate', 'reactivate')->name('products-approvals.reactivate');
        });

        // ====== العمولات ======
        Route::prefix('commissions')->controller(CommissionsController::class)->group(function () {
            Route::get('/', 'index')->name('commissions.index');
            Route::post('/updateDefaults', 'updateDefaults')->name('commissions.updateDefaults');
            Route::post('/setRate', 'setRate')->name('commissions.setRate');
            Route::get('/report', 'report')->name('commissions.report');
        });

        // ====== العربون ======
        Route::prefix('deposits')->controller(DepositsController::class)->group(function () {
            Route::get('/settings', 'settings')->name('deposits.settings');
            Route::post('/updateSettings', 'updateSettings')->name('deposits.updateSettings');
            Route::get('/report', 'report')->name('deposits.report');
        });

        // ====== التقييمات ======
        Route::prefix('reviews')->controller(ReviewsController::class)->group(function () {
            Route::get('/', 'index')->name('reviews');
            Route::post('{type}/{id}/toggle', 'toggle')->whereIn('type', ['order', 'driver'])->name('reviews.toggle');
            Route::post('{type}/{id}/delete', 'destroy')->whereIn('type', ['order', 'driver'])->name('reviews.delete');
        });

        // ====== محفظة الإدارة ======
        Route::get('admin-wallet', [AdminWalletController::class, 'index'])->name('adminwallet');

        // ====== قوالب الإشعارات ======
        Route::prefix('notification-templates')->controller(NotificationTemplatesController::class)->group(function () {
            Route::get('/', 'index')->name('notificationtemplates');
            Route::post('{key}', 'update')->name('notificationtemplates.update');
            Route::post('{key}/reset', 'reset')->name('notificationtemplates.reset');
        });

        // ====== خدمة الذبح ======
        Route::prefix('slaughters')->controller(SlaughtersController::class)->group(function () {
         Route::get('/settings', 'settings')->name('slaughters.settings');
    Route::post('/settings/update', 'updateSettings')->name('slaughters.settings.update');
    Route::get('/report', 'report')->name('slaughters.report');
        });

        // ====== المناطق الجغرافية ======
        Route::prefix('geo')->controller(GeoAreasController::class)->group(function () {
            // الدول
            Route::get('/countries', 'countriesIndex')->name('geo.countries.index');
            Route::post('/countries', 'countriesStore')->name('geo.countries.store');
            Route::put('/countries/{id}', 'countriesUpdate')->name('geo.countries.update');
            Route::delete('/countries/{id}', 'countriesDestroy')->name('geo.countries.destroy');
            // المدن
            Route::get('/cities', 'citiesIndex')->name('geo.cities.index');
            Route::post('/cities', 'citiesStore')->name('geo.cities.store');
            Route::put('/cities/{id}', 'citiesUpdate')->name('geo.cities.update');
            Route::delete('/cities/{id}', 'citiesDestroy')->name('geo.cities.destroy');
            // المناطق
            Route::get('/areas', 'areasIndex')->name('geo.areas.index');
            Route::post('/areas', 'areasStore')->name('geo.areas.store');
            Route::delete('/areas/{id}', 'areasDestroy')->name('geo.areas.destroy');
            // الأحياء
            Route::get('/neighborhoods', 'neighborhoodsIndex')->name('geo.neighborhoods.index');
            Route::post('/neighborhoods', 'neighborhoodsStore')->name('geo.neighborhoods.store');
            Route::delete('/neighborhoods/{id}', 'neighborhoodsDestroy')->name('geo.neighborhoods.destroy');
        });

        #################################################### End مواشي وسيط ####################################################


        });
    },





);
