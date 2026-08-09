<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Orderstatus;
use App\Http\Resources\Api\V1\OrderStatusResource;
use App\Traits\ApiTrait; // إذا كنت تستخدم التريت الخاص بك في الردود

class OrderStatusApiController extends Controller
{
    use ApiTrait;

    public function index()
    {
        try {
            // جلب جميع الحالات (المكتبة ستقوم تلقائياً بجلب الترجمة حسب لغة الطلب)
            $statuses = Orderstatus::orderBy('id', 'ASC')->get();

            return $this->returnData(
                'data',
                OrderStatusResource::collection($statuses),
                __('api.Api_Return_Data')
            );
        } catch (\Exception $ex) {
            return $this->returnError(__('api.Api_Error_Contact_Programmer'));
        }
    }
}
