<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AutoLogoutEmployeeInactive
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        if (auth()->guard('admin')->check() && auth()->guard('admin')->user()->is_active == 0) {
            notify()->warning(__('dashboard.Your_account_has_been_suspended'),__('dashboard.warning'));

            auth()->guard('admin')->logout();
            return redirect(route('admin.login'));
        }

        return $next($request);
    }
}
