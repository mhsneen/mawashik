<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Traits\ApiTrait;

/**
 * EnsureIsDriver — يسمح فقط للمندوبين المعتمدين بالوصول لمسارات التوصيل.
 *
 * المنطق (البند 11 من المتطلبات: المندوب يخضع للاعتماد قبل استخدام وظائف التوصيل):
 *  - يجب أن يمتلك المستخدم دور "driver".
 *  - يجب أن تكون حالة driver_status = "approved".
 *  - pending / suspended / rejected → 403 برسالة مناسبة.
 */
class EnsureIsDriver
{
    use ApiTrait;

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return $this->returnError(__('api.Api_Unauthenticated'), 401);
        }

        if (!$user->hasRole('driver', 'web')) {
            return $this->returnError(__('api.Api_Driver_Role_Required'), 403);
        }

        return match ($user->driver_status) {
            'approved'  => $next($request),
            'pending'   => $this->returnError(__('api.Api_Driver_Pending'), 403),
            'suspended' => $this->returnError(__('api.Api_Driver_Suspended'), 403),
            'rejected'  => $this->returnError(__('api.Api_Driver_Rejected'), 403),
            default     => $this->returnError(__('api.Api_Driver_Unknown_Status'), 403),
        };
    }
}
