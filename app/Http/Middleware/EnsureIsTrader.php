<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Traits\ApiTrait;

/**
 * EnsureIsTrader — يسمح فقط للتجار المعتمدين بالوصول لمسارات التاجر.
 *
 * المنطق:
 *  - يجب أن يمتلك المستخدم دور "trader".
 *  - يجب أن تكون حالة trader_status = "approved".
 *  - لو "pending" → إرجاع 403 برسالة "حسابك قيد المراجعة".
 *  - لو "suspended" → إرجاع 403 برسالة "حسابك موقوف".
 *  - لو "rejected" → إرجاع 403 برسالة "تم رفض حسابك".
 */
class EnsureIsTrader
{
    use ApiTrait;

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return $this->returnError(__('api.Api_Unauthenticated'), 401);
        }

        if (!$user->hasRole('trader', 'web')) {
            return $this->returnError(__('api.Api_Trader_Role_Required'), 403);
        }

        return match ($user->trader_status) {
            'approved'  => $next($request),
            'pending'   => $this->returnError(__('api.Api_Trader_Pending'), 403),
            'suspended' => $this->returnError(__('api.Api_Trader_Suspended'), 403),
            'rejected'  => $this->returnError(__('api.Api_Trader_Rejected'), 403),
            default     => $this->returnError(__('api.Api_Trader_Unknown_Status'), 403),
        };
    }
}
