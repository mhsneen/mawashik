<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use App\Models\Setting;
use App\Models\Textpage;
use App\Models\Order;
use App\Models\User;
use App\Models\Product;
use App\Observers\OrderObserver;
use App\Observers\UserObserver;
use App\Observers\ProductObserver;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use App\Services\Payments\PaymentGatewayManager;
use Illuminate\Http\Resources\Json\JsonResource;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(PaymentGatewayManager::class, function ($app) {
            return new PaymentGatewayManager();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void

    {
         // عشان ما يضيفش data مرتين مع الـ ApiTrait
    JsonResource::withoutWrapping();
        Gate::after(function ($user, $ability) {
            return $user->hasRole('admin'); // note this returns boolean
        });
        Order::observe(OrderObserver::class);
        User::observe(UserObserver::class);
       Product::observe(ProductObserver::class);


        Paginator::useBootstrap();
        view()->composer('*', function ($view) {
            $storeLogo = null;

            if (Schema::hasTable('settings')) {
                try {
                    $storeLogo = Setting::where('key', 'store_logo')->first();
                } catch (\Throwable $e) {
                    $storeLogo = null;
                }
            }

            $view->with('store_logo', $storeLogo);
        });

        view()->composer(
            'layouts.admin_dashboard',
            function ($view) {
                $menuTextpages = collect();

                if (Schema::hasTable('textpages')) {
                    try {
                        $menuTextpages = Textpage::all();
                    } catch (\Throwable $e) {
                        $menuTextpages = collect();
                    }
                }

                $view->with('menu_textpages', $menuTextpages);
            }
        );
    }
}
