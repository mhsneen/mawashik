<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Services\StoreStatusService;
use App\Traits\ApiTrait;

/**
 * يُطبّق على كل مجموعات الـ API (guest و auth) — لو appClosed=1 بالإعدادات،
 * يرجع رسالة الصيانة فوراً قبل ما يوصل لأي كنترولر.
 */
class CheckMaintenanceMode
{
    use ApiTrait;

    public function handle(Request $request, Closure $next): Response
    {
        $storeStatus = app(StoreStatusService::class);

        if ($storeStatus->isUnderMaintenance()) {
            return $this->returnError($storeStatus->maintenanceMessage(), '503');
        }

        return $next($request);
    }
}
