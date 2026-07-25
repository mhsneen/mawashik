<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    public function handle($request, Closure $next, $roles)
    {
        $user = Auth::guard('admin')->user();

        // Split roles by pipe or comma
        $rolesArray = array_map('trim', preg_split('/[|,]/', $roles));

        // Use Spatie's hasAnyRole method to check if user has any of the required roles
        if ($user && $user->hasAnyRole($rolesArray)) {
            return $next($request);
        }

        notify()->warning(__('dashboard.You_do_not_have_permission_to_access_the_control_panel'), __('dashboard.warning'));
        return redirect(route('admin.login'));
    }
}
