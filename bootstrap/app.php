<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Exceptions\ThrottleRequestsException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        using: function () {
            Route::middleware('web')
                ->group(base_path('routes/web.php'));

            Route::middleware('web')
                ->group(base_path('routes/admin.php'));

            Route::prefix('api/v1')
                ->middleware('api')
                ->group(base_path('routes/api.php'));
        },
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {

        $middleware->encryptCookies(except: [
            'ckCsrfToken',
        ]);
        $middleware->validateCsrfTokens(except: [
            'ckfinder/*',
        ]);
        $middleware->redirectTo(function ($request) {
            if (! $request->expectsJson()) {
                if (in_array('auth:admin', $request->route()->middleware()))
                    return route('admin.login');
                if (in_array('auth:web', $request->route()->middleware()))
                    return route('site.login');
            }
        });

        $middleware->redirectUsersTo(function (Request $request, Closure $next, string ...$guards) {

            $guards = [];

            if (in_array('auth:admin', $request->route()->middleware())) {
                $guards[] = 'admin';
            } else {
                $guards[] = 'web'; // أو 'web' حسب إعداداتك
            }

            foreach ($guards as $guard) {
                if (Auth::guard($guard)->check()) {
                    if ($guard == 'admin') {
                        return redirect('/admin');
                    } else {
                        return redirect('/');
                    }
                }
            }

            return $next($request);
        });



        $middleware->alias([
            /**** OTHER MIDDLEWARE ALIASES ****/
            'localize'                => \Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRoutes::class,
            'localizationRedirect'    => \Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter::class,
            'localeSessionRedirect'   => \Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect::class,
            'localeCookieRedirect'    => \Mcamara\LaravelLocalization\Middleware\LocaleCookieRedirect::class,
            'localeViewPath'          => \Mcamara\LaravelLocalization\Middleware\LaravelLocalizationViewPath::class,
            'AutoLogoutEmployeeInactive' => \App\Http\Middleware\AutoLogoutEmployeeInactive::class,
            'dashboardAccess' => \App\Http\Middleware\DashboardAccess::class,
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
            'apiCheckUse' => \App\Http\Middleware\ApiCheckUse::class,
            'apiChangeLanguage' => \App\Http\Middleware\ApiChangeLanguage::class,
            'ApiCheckBlockedUser' => \App\Http\Middleware\ApiCheckBlockedUser::class,
            'CheckRole' => \App\Http\Middleware\CheckRole::class,
            'checkMaintenance' => \App\Http\Middleware\CheckMaintenanceMode::class,
            'ensureIsDriver' => \App\Http\Middleware\EnsureIsDriver::class,
            'ensureIsTrader' => \App\Http\Middleware\EnsureIsTrader::class,

        ]);
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (AuthenticationException $e, Request $request) {

            if ($request->is('api/*')) {
                return response()->json([
                    'status' => false,
                    'code' => 403,
                    'message' => 'Unauthorized Token Invalid '
                ]);
            }
        });

        $exceptions->render(function (ThrottleRequestsException $e, Illuminate\Http\Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'status' => false,
                    'code' => 429,
                    'message' => __('api.You_have_exceeded_the_number_of_requests_allowed_please_try_again_later')
                ]);
            }
        });
    })->create();
