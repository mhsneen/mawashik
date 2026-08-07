<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiCheckBlockedUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // التحقق إذا كان المستخدم مسجل دخوله وحسابه محظور
        if (auth()->guard('sanctum')->check() && auth()->guard('sanctum')->user()->is_blocked == 1) {

            $user = $request->user();

            // ✅ حذف جميع التوكنات الخاصة بالمستخدم (من كل الأجهزة)
            $user->tokens()->delete();

            return response()->json([
                'status' => false,
                'code' => 403,
                'message' => __('api.account_blocked') // رسالة الحظر الجديدة
            ]);
        }

        return $next($request);
    }
}
