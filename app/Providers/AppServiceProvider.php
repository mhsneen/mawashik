<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use App\Models\Setting;
use App\Models\Textpage;
use App\Models\Order;
use App\Models\User;
use App\Observers\OrderObserver;
use App\Observers\UserObserver;
use Illuminate\Support\Facades\Gate;
use App\Services\Payments\PaymentGatewayManager;

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
        Gate::after(function ($user, $ability) {
            return $user->hasRole('admin'); // note this returns boolean
        });
        Order::observe(OrderObserver::class);
        User::observe(UserObserver::class);

        Paginator::useBootstrap();
        view()->composer('*', function ($view) {
            $view->with('store_logo', Setting::where('key', 'store_logo')->first());
        });
        view()->composer(
            'layouts.admin_dashboard',
            function ($view) {
                $view->with('menu_textpages', Textpage::all());
            }
        );
    }
}
