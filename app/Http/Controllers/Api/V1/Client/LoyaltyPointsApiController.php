<?php

namespace App\Http\Controllers\Api\V1\Client;

use App\Http\Controllers\Controller;
use App\Traits\ApiTrait;
use App\Services\LoyaltyPointsService;
use Illuminate\Http\Request;
use App\Http\Resources\Api\V1\LoyaltyPointTransactionResource;
class LoyaltyPointsApiController extends Controller
{
    use ApiTrait;

    public function getBalance(Request $request, LoyaltyPointsService $loyaltyService)
    {
        $user = $request->user();

        return $this->returnData('data', [
            'balance' => $user->loyalty_points_balance,//رصيد المستخدم الحالي من نقاط الولاء
            'balance_value' => $loyaltyService->pointsToCurrency($user->loyalty_points_balance),// القيمة المالية (النقدية) لرصيد النقاط الذي يملكه المستخدم
            'min_redeem_points' => $loyaltyService->minRedeemPoints(),//الحد الأدنى لعدد النقاط التي يجب أن يملكها المستخدم ليتمكن من استبدالها في طلب جديد
            'loyalty_enabled' => $loyaltyService->isEnabled(),// هل نظام نقاط الولاء مُفعّل في المتجر أم لا؟
        ], __('api.Api_Return_Data'));
    }

       public function getHistory(Request $request)
    {
        $transactions = $request->user()
            ->loyaltyPointTransactions()
            ->latest()
            ->paginate(API_PAGANATION_COUNT);

        $data = [
            'data' => LoyaltyPointTransactionResource::collection($transactions->getCollection()),
            'meta' => [
                'current_page' => $transactions->currentPage(),
                'total_pages'  => $transactions->lastPage(),
                'total_items'  => $transactions->total(),
                'per_page'     => $transactions->perPage(),
            ],
        ];

        return $this->returnData('data', $data, __('api.Api_Return_Data'));
    }
}
