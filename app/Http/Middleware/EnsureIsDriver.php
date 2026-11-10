<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Traits\ApiTrait;

/**
 * التحقق أن المستخدم المسجّل له دور "driver"
 * يُستخدم لحماية endpoints الخاصة بالسائق
 */
class EnsureIsDriver
{
    use ApiTrait;

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user || !$user->hasRole('driver')) {
            return response()->json([
                'status'  => false,
                'code'    => 403,
                'message' => __('api.Api_Unauthenticated'),
            ], 403);
        }

        return $next($request);
    }
}
