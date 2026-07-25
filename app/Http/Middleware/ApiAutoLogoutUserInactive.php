<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Traits\ApiTrait;
class ApiAutoLogoutUserInactive
{
    use ApiTrait;
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if (auth()->guard('sanctum')->check() && auth()->guard('sanctum')->user()->is_active == 0) {
            $request->user()->currentAccessToken()->delete();
            return response()->json([
                'status' => false,
                'code' => 403,
                'message' => __('dashboard.Your_account_has_been_suspended')
            ]);
        }

        return $next($request);
    }
}
