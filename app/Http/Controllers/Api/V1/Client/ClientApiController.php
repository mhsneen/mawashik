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

            // بوضع "مباشر": الحساب انخلق مفعّل من أول استعلام -> نرجّع توكن مباشرة بدون أي كود
            if ($isDirect) {
                $token = $user->createToken($user->name . 'Auth-Token')->plainTextToken;
            $user->update(['device_token' => $request->device_token]);
            $data = [
            'requires_verification' => false,
            'user' => [
               'token' => $token,
                'id' => $user->id,
                'name' => $user->name ?? '',
                'email' => $user->email ?? '',
                'mobile' => $user->mobile ?? '',
                'image_url' => $user->image_url ?? '',
                'base_image_url' => $user->base_image_url ?? '',
                'role' => $user->getRoleNames()->first() ?? '',
            ],
        ];

                return $this->returnData('data', $data, __('api.You_have_been_successfully_registered'));
            }

            // بوضع "تحقق": نولّد ونرسل الكود بعد الـ commit وخارج الـ transaction
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

            try {
                $isValid = $otpService->verify($user, $request->code, OtpCode::PURPOSE_REGISTRATION);
            } catch (Exception $e) {
                return $this->returnError(__('api.Too_many_attempts_request_new_code'));
            }

            if (!$isValid) {
                return $this->returnError(__('api.Invalid verification code'));
            }

            DB::beginTransaction();

            $user->update([
                'is_active'    => 1,
                'device_token' => $request->device_token,
            ]);

            $token = $user->createToken($user->name . 'Auth-Token')->plainTextToken;

            DB::commit();



            $data = [
    'token'           => $token,
    'userId'          => $user->id,
    'name'            => $user->name ?? '',
    'email'           => $user->email ?? '',
    'mobile'          => $user->mobile ?? '',
    'image_url'       => $user->image_url ?? '',
    'base_image_url'  => $user->base_image_url ?? '',
    'role'            => $user->getRoleNames()->first() ?? '',
];

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

        $user->makeHidden([
            'email_verified_at',
            'password',
            'remember_token',
            'created_at',
            'updated_at',
            'is_active',
            'device_token',
            'desktop_token',
            'deleted_at',
            'image',

        ]);
        $user->branch_id = $user->branch_id ?? 0;

        return $this->returnData('data', $user, __('api.Api_Return_Data'));
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
}
