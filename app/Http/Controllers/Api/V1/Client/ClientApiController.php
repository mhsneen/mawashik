<?php

namespace App\Http\Controllers\Api\V1\Client;

use App\Http\Controllers\Controller;
use App\Models\{User, OtpCode};
use App\Traits\ApiTrait;
use App\Http\Requests\Api\V1\{
    ClientRegisterRequest,
    ClientUpdateRequest,
    VerifyOtpRequest,
    ResendOtpRequest
};
use App\Services\{ImageUploadService, OtpService};
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Http\Resources\Api\V1\Client\UserResource;

class ClientApiController extends Controller
{
    use ApiTrait;

    protected $folder = "users";

    public function register(ClientRegisterRequest $request, ImageUploadService $imageService, OtpService $otpService)
    {
        try {
            DB::beginTransaction();

            $isDirect = $otpService->isDirectMode();

            $item_data = [
                'name'      => $request->name,
                'is_active' => $isDirect ? 1 : 0,
                'mobile'    => $request->mobile,
                'email'     => $request->email ?? null,
                'device_token' => $request->device_token,
            ];

            if ($request->hasFile('image')) {
                $item_data['image'] = $imageService->uploadAndProcess(
                    image: $request->file('image'),
                    folder: $this->folder,
                    thumbnailWidth: 128,
                    thumbnailHeight: 128
                );
            }

            $user = User::create($item_data);

            $clientRole = Role::where('name', 'client')->where('guard_name', 'web')->first();
            $user->assignRole($clientRole);

            DB::commit();

            if ($isDirect) {
                $token = $user->createToken($user->name . 'Auth-Token')->plainTextToken;
                $user->update(['device_token' => $request->device_token]);

                $user->access_token = $token;

                $data = [
                    'requires_verification' => false,
                    'user' => new UserResource($user),
                ];

                return $this->returnData('data', $data, __('api.You_have_been_successfully_registered'));
            }

            $otpService->generateAndSend($user, OtpCode::PURPOSE_REGISTRATION);

            $data = [
                'requires_verification' => true,
            ];
            return $this->returnData(
                'data', $data,  __('api.You have been successfully registeredplease enter the code sent to your mobile to activate the account')
            );
        } catch (Exception $ex) {
            DB::rollback();
            Log::error('ClientApi register: ' . $ex->getMessage(), [
                'trace' => $ex->getTraceAsString(),
                'input' => $request->except('image'),
            ]);
            return $this->returnError(__('api.Api_Error_Contact_Programmer'));
        }
    }

    public function check_active_code(VerifyOtpRequest $request, OtpService $otpService)
    {
        try {
            $user = User::when($request->filled('email'), function ($query) use ($request) {
                return $query->where('email', $request->email);
            }, function ($query) use ($request) {
                return $query->where('mobile', $request->mobile);
            })
            ->whereNull('deleted_at')
            ->first();

            if (!$user) {
                return $this->returnError(__('api.User_Not_Found'));
            }

            // ✅ منع المستخدم المحظور من تفعيل الحساب
            if ($user->is_blocked) {
                return $this->returnError(__('api.account_blocked'));
            }

            try {
                $isValid = $otpService->verify($user, $request->code, OtpCode::PURPOSE_REGISTRATION);
            } catch (Exception $e) {
                return $this->returnError(__('api.Too_many_attempts_request_new_code'));
            }

            if (!$isValid) {
                return $this->returnError(__('api.Invalid verification code'));
            }

            // التاجر/المندوب: بعد التحقق من الجوال لا يُفعَّل الحساب ولا يُصدَر توكن؛
            // يبقى بانتظار اعتماد الإدارة ولا يدخل إلا بعد الاعتماد.
            if ($user->approvalBlockKey() === 'api.Api_Trader_Pending'
                || $user->approvalBlockKey() === 'api.Api_Driver_Pending') {
                return $this->returnData('data', [
                    'user_id'       => $user->id,
                    'mobile'        => $user->mobile,
                    'status'        => 'pending',
                    'mobile_verified' => true,
                ], __('api.Registration_Verified_Pending_Review'));
            }

            if ($blockKey = $user->approvalBlockKey()) {
                return $this->returnError(__($blockKey));
            }

            DB::beginTransaction();

            $user->update([
                'is_active'    => 1,
                'device_token' => $request->device_token,
            ]);

            $token = $user->createToken($user->name . 'Auth-Token')->plainTextToken;
            DB::commit();

            $user->access_token = $token;

            $data = new UserResource($user);

            return $this->returnData('data', $data, __('api.Account activated successfully'));
        } catch (Exception $ex) {
            DB::rollback();
            Log::error('check_active_code: ' . $ex->getMessage(), [
                'trace' => $ex->getTraceAsString(),
                'input' => $request->all(),
            ]);
            return $this->returnError(__('api.Api_Error_Contact_Programmer'));
        }
    }

    public function rsend_active_code(ResendOtpRequest $request, OtpService $otpService)
    {
        try {
            $user = User::when($request->filled('email'), function ($query) use ($request) {
                return $query->where('email', $request->email);
            }, function ($query) use ($request) {
                return $query->where('mobile', $request->mobile);
            })
            ->whereNull('deleted_at')
            ->first();

            if (!$user) {
                return $this->returnError(__('api.User_Not_Found'));
            }

            // ✅ منع المستخدم المحظور من طلب كود تفعيل جديد
            if ($user->is_blocked) {
                return $this->returnError(__('api.account_blocked'));
            }

            $otpService->generateAndSend($user, OtpCode::PURPOSE_REGISTRATION);

            return $this->returnSuccessMessage(__('api.The_code_has_been_sent_successfully'));
        } catch (Exception $ex) {
            Log::error('rsend_active_code: ' . $ex->getMessage(), [
                'trace' => $ex->getTraceAsString(),
                'input' => $request->all(),
            ]);
            return $this->returnError(__('api.Api_Error_Contact_Programmer'));
        }
    }

    public function get_ClientInfo(Request $request)
    {
        $user = $request->user();

        return $this->returnData(
            'data',
            new UserResource($user),
            __('api.Api_Return_Data')
        );
    }

    public function client_Update(ClientUpdateRequest $request, ImageUploadService $imageService)
    {
        try {
            DB::beginTransaction();
            $user = $request->user();
            $item_data = [
                'name'   => $request->name,
                'mobile' => $request->mobile,
                'email'  => $request->email,
            ];

            if ($request->has('image')) {
                $item_data['image'] = $imageService->uploadAndProcess(
                    image: $request->file('image'),
                    folder: $this->folder,
                    thumbnailWidth: 128,
                    thumbnailHeight: 128,
                    oldFilename: $user->image
                );
            }

            $user->update($item_data);
            DB::commit();
            return $this->returnSuccessMessage(__('api.Api_Data_has_been_modified_successfully'));
        } catch (Exception $ex) {
            DB::rollback();
            Log::error('client_Update' . $ex->getMessage(), [
                'trace' => $ex->getTraceAsString(),
                'input' => $request->all(),
            ]);
            return $this->returnError(__('api.Api_Error_Contact_Programmer'));
        }
    }

    /**
     * عرض رصيد محفظة العميل وإحصائياتها
     */
    public function getWalletBalance(Request $request)
    {
        try {
            $client = $request->user();

            $wallet = Wallet::firstOrCreate(
                ['user_id' => $client->id],
                ['balance' => 0, 'rolename' => 'client']
            );

            $totalSpent = WalletTransaction::where('wallet_id', $wallet->id)
                ->where('type', 'debit')
                ->sum('amount');

            $totalRefunded = WalletTransaction::where('wallet_id', $wallet->id)
                ->where('type', 'credit')
                ->sum('amount');

            $todayOrders = $client->orders()
                ->whereDate('created_at', today())
                ->count();

            return $this->returnData('data', [
                'balance'        => (float) $wallet->balance,
                'total_spent'    => (float) $totalSpent,
                'total_refunded' => (float) $totalRefunded,
                'today_orders'   => $todayOrders,
            ], __('api.Api_Return_Data'));

        } catch (Exception $ex) {
            Log::error('getWalletBalance (Client): ' . $ex->getMessage(), ['trace' => $ex->getTraceAsString()]);
            return $this->returnError(__('api.Api_Error_Contact_Programmer'));
        }
    }

    /**
     * سجل معاملات محفظة العميل
     */
    public function getWalletTransactions(Request $request)
    {
        try {
            $client = $request->user();
            $wallet = Wallet::where('user_id', $client->id)->first();

            if (!$wallet) {
                return $this->returnData('data', [], __('api.Api_Return_Data'));
            }

            $transactions = WalletTransaction::where('wallet_id', $wallet->id)
                ->with('order:id,final_price,delivery_price,payment_method')
                ->orderBy('id', 'DESC')
                ->get();

            $transformedData = $transactions->map(function ($tx) {
                return [
                    'id'            => $tx->id,
                    'type'          => $tx->type,
                    'type_label'    => $tx->type_translated,
                    'amount'        => $tx->amount,
                    'amount_signed' => $tx->amount_signed,
                    'description'   => $tx->description,
                    'order_id'      => $tx->order_id,
                         'date'          => $tx->created_at?->diffForHumans(),

                ];
            });

            return $this->returnData('data', $transformedData, __('api.Api_Return_Data'));

        } catch (Exception $ex) {
            Log::error('getWalletTransactions (Client): ' . $ex->getMessage(), ['trace' => $ex->getTraceAsString()]);
            return $this->returnError(__('api.Api_Error_Contact_Programmer'));
        }
    }
}
