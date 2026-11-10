<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Traits\ApiTrait;
class ApiCheckUse
{
    use ApiTrait;
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */



        public function handle(Request $request, Closure $next): Response
    {
        // قراءة كلمة المرور من config وليس من env مباشرة
        $apiPassword = config('app.api_password');

        // إذا كان الطلب لا يحتوي على api_password أو كانت غير مطابقة
        if (!$request->has('api_password') || $request->input('api_password') !== $apiPassword) {
            return $this->returnError(__('api.Api_Unauthenticated'));
        }

        return $next($request);
    }
}
