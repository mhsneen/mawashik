<?php

namespace App\Http\Controllers\Api\V1\Driver;

use App\Http\Controllers\Controller;
use App\Models\{Wallet, WalletWithdrawalRequest};
use App\Traits\ApiTrait;
use App\Services\WalletSettlementService;
use Exception;
use InvalidArgumentException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class DriverWalletApiController extends Controller
{
    use ApiTrait;

    /**
     * السائق يطلب سحب مستحقاته (فقط إذا رصيده موجب = المتجر مديون له)
     */
    public function requestWithdrawal(Request $request, WalletSettlementService $service)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:0.01',
            'note'   => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return $this->returnError($validator->errors()->first());
        }

        try {
            $driver = $request->user();

            $wallet = Wallet::firstOrCreate(
                ['user_id' => $driver->id],
                ['balance' => 0, 'rolename' => 'driver']
            );

            $withdrawalRequest = $service->requestWithdrawal($wallet, (float) $request->amount, $request->note);

            return $this->returnData('data', [
                'id'         => $withdrawalRequest->id,
                'amount'     => (float) $withdrawalRequest->amount,
                'status'     => $withdrawalRequest->status,
                'created_at' => $withdrawalRequest->created_at?->diffForHumans(),
            ], __('api.Withdrawal_Request_Sent_Success'));
        } catch (InvalidArgumentException $ex) {
            return $this->returnError($ex->getMessage());
        } catch (Exception $ex) {
            Log::error('requestWithdrawal: ' . $ex->getMessage(), ['trace' => $ex->getTraceAsString()]);
            return $this->returnError(__('api.Api_Error_Contact_Programmer'));
        }
    }

    /**
     * سجل طلبات السحب الخاصة بالسائق الحالي
     */
    public function getWithdrawalRequests(Request $request)
    {
        try {
            $driver = $request->user();

            $requests = WalletWithdrawalRequest::whereHas('wallet', fn($q) => $q->where('user_id', $driver->id))
                ->orderByDesc('id')
                ->paginate(API_PAGANATION_COUNT);

            $data = $requests->getCollection()->map(function ($item) {
                return [
                    'id'           => $item->id,
                    'amount'       => (float) $item->amount,
                    'status'       => $item->status,
                    'driver_note'  => $item->driver_note ?? '',
                    'admin_note'   => $item->admin_note ?? '',
                    // ✅ إرجاع التاريخ بصيغة مقروءة (Y-m-d وتوقيت صباحاً/مساءً)
                    'created_at'   => $item->created_at ? \Carbon\Carbon::parse($item->created_at)->translatedFormat('Y-m-d h:i A') : null,
                    'processed_at' => $item->processed_at ? \Carbon\Carbon::parse($item->processed_at)->translatedFormat('Y-m-d h:i A') : null,
                ];
            });

            return $this->returnData('data', [
                'items' => $data,
                'meta'  => [
                    'current_page' => $requests->currentPage(),
                    'total_pages'  => $requests->lastPage(),
                    'total_items'  => $requests->total(),
                    'per_page'     => $requests->perPage(),
                ],
            ], __('api.Api_Return_Data'));
        } catch (Exception $ex) {
            Log::error('getWithdrawalRequests: ' . $ex->getMessage(), ['trace' => $ex->getTraceAsString()]);
            return $this->returnError(__('api.Api_Error_Contact_Programmer'));
        }
    }

    /**
     * السائق يلغي طلبه بنفسه (فقط طالما لسا Pending)
     */
    public function cancelWithdrawalRequest(Request $request, WalletSettlementService $service)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|exists:wallet_withdrawal_requests,id',
        ]);

        if ($validator->fails()) {
            return $this->returnError($validator->errors()->first());
        }

        try {
            $driver = $request->user();

            $withdrawalRequest = WalletWithdrawalRequest::whereHas('wallet', fn($q) => $q->where('user_id', $driver->id))
                ->find($request->id);

            if (!$withdrawalRequest) {
                return $this->returnError(__('api.ID_Not_Found'));
            }

            $service->cancel($withdrawalRequest);

            return $this->returnSuccessMessage(__('api.Withdrawal_Request_Cancelled_Success'));
        } catch (InvalidArgumentException $ex) {
            return $this->returnError($ex->getMessage());
        } catch (Exception $ex) {
            Log::error('cancelWithdrawalRequest: ' . $ex->getMessage(), ['trace' => $ex->getTraceAsString()]);
            return $this->returnError(__('api.Api_Error_Contact_Programmer'));
        }
    }
}
