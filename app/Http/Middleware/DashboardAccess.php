<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DashboardAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::guard('admin')->user();

        if (!$user) {
            return redirect()->route('admin.login');
        }

        if ($user->hasAnyRole(['admin', 'employee'])) {
            return $next($request);
        }

        notify()->warning(
            __('dashboard.You_do_not_have_permission_to_access_the_control_panel'),
            __('dashboard.warning')
        );

        return redirect()->route('admin.login');
    }
}
