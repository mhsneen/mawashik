<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Models\{User, OtpCode};
use App\Traits\ApiTrait;
use App\Services\OtpService;
use App\Http\Requests\Api\V1\{
    LoginRequest,
    VerifyOtpRequest,
    ResendOtpRequest,
    ForgetPasswordRequest,
    ResetPasswordRequest,
    UpdatePasswordRequest
};
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Resources\Api\V1\Client\UserResource;

class AuthCommonApiController extends Controller
{
    use ApiTrait;

    // مدة صلاحية توكن إعادة تعيين كلمة المرور (بعد التحقق من الكود)
    protected const RESET_TOKEN_TTL_MINUTES = 10;

         public function login(LoginRequest $request, OtpService $otpService)
    {
        $user = User::when($request->filled('email'), function ($query) use ($request) {
            return $query->where('email', $request->email);
        }, function ($query) use ($request) {
            return $query->where('mobile', $request->mobile);
        })
        ->whereNull('deleted_at')
        ->first();

        if (!$user) {
            return $this->returnError(__('dashboard.error_login'));
        }

        // منع المستخدم المحظور من تسجيل الدخول
        if ($user->is_blocked) {
            return $this->returnError(__('api.account_blocked'));
        }

        // بوضع "مباشر": تسجيل دخول فوري بدون أي كود
        if ($otpService->isDirectMode()) {
            $user->update(['device_token' => $request->device_token]);
            $user->tokens()->delete();
            $token = $user->createToken($user->name . 'Auth-Token')->plainTextToken;

            // ✅ ضف التوكن كـ attribute
            $user->access_token = $token;

            // ✅ استخدام UserResource الموحد
            $data = new UserResource($user);

            return $this->returnData('data', $data, __('api.Api_You_have_successfully_logged_in'));
        }

        try {
            $otpService->generateAndSend($user, OtpCode::PURPOSE_LOGIN);
        } catch (Exception $ex) {
            Log::error('login: ' . $ex->getMessage());
            return $this->returnError(__('api.Api_Error_Contact_Programmer'));
        }

        $data = [
            'requires_verification' => true,
        ];
        return $this->returnData('data', $data, __('api.A verification code has been sent for login'));
    }

    public function check_login_code(VerifyOtpRequest $request, OtpService $otpService)
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

            // منع المستخدم المحظور من تفعيل الكود وتسجيل الدخول
            if ($user->is_blocked) {
                return $this->returnError(__('api.account_blocked'));
            }

            try {
                $isValid = $otpService->verify($user, $request->code, OtpCode::PURPOSE_LOGIN);
            } catch (Exception $e) {
                return $this->returnError(__('api.Too_many_attempts_request_new_code'));
            }

            if (!$isValid) {
                return $this->returnError(__('api.Invalid verification code'));
            }

            $user->update(['device_token' => $request->device_token, 'is_active' => 1]);
            $user->tokens()->delete();
            $token = $user->createToken($user->name . 'Auth-Token')->plainTextToken;

            // ✅ ضف التوكن كـ attribute
            $user->access_token = $token;

            // ✅ استخدام UserResource الموحد
            $data = new UserResource($user);

            return $this->returnData('data', $data, __('api.Api_You_have_successfully_logged_in'));
        } catch (Exception $ex) {
            Log::error('check_login_code: ' . $ex->getMessage(), [
                'trace' => $ex->getTraceAsString(),
                'input' => $request->all(),
            ]);
            return $this->returnError(__('api.Api_Error_Contact_Programmer'));
        }
    }

    public function rsend_login_code(ResendOtpRequest $request, OtpService $otpService)
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

            // ✅ منع المستخدم المحظور من طلب كود جديد
            if ($user->is_blocked) {
                return $this->returnError(__('api.account_blocked'));
            }

            $otpService->generateAndSend($user, OtpCode::PURPOSE_LOGIN);

            return $this->returnSuccessMessage(__('api.The_code_has_been_sent_successfully'));
        } catch (Exception $ex) {
            Log::error('rsend_login_code: ' . $ex->getMessage());
            return $this->returnError(__('api.Api_Error_Contact_Programmer'));
        }
    }

    public function foreget_Password(ForgetPasswordRequest $request, OtpService $otpService)
    {
        try {
         $user = User::where('mobile', $request->mobile)
 ->when($request->filled('email'), fn($q) => $q->orWhere('email', $request->email))
 ->whereNull('deleted_at')
 ->first();

            if (!$user) {
                return $this->returnError(__('api.User_Not_Found'));
            }

            if ($user->is_active == 0) {
                return $this->returnError(__('dashboard.The_account_is_not_activated'));
            }

            $otpService->generateAndSend($user, OtpCode::PURPOSE_PASSWORD_RESET);

            return $this->returnSuccessMessage(__('api.Api_recover_password'));
        } catch (Exception $ex) {
            Log::error('foreget_Password: ' . $ex->getMessage());
            return $this->returnError(__('api.Api_Error_Contact_Programmer'));
        }
    }

    public function foregetPassword_CheckCode(VerifyOtpRequest $request, OtpService $otpService)
    {
        try {
         $user = User::where('mobile', $request->mobile)
 ->when($request->filled('email'), fn($q) => $q->orWhere('email', $request->email))
 ->whereNull('deleted_at')
 ->first();

            if (!$user) {
                return $this->returnError(__('api.User_Not_Found'));
            }

            try {
                $isValid = $otpService->verify($user, $request->code, OtpCode::PURPOSE_PASSWORD_RESET);
            } catch (Exception $e) {
                return $this->returnError(__('api.Too_many_attempts_request_new_code'));
            }

            if (!$isValid) {
                return $this->returnError(__('api.Invalid verification code'));
            }

            // توكن مؤقت يسمح بتنفيذ خطوة إعادة التعيين فقط، بدل إعادة استخدام نفس الكود
            $resetToken = Str::random(60);
            Cache::put(
                "password_reset_token:{$user->id}",
                $resetToken,
                now()->addMinutes(self::RESET_TOKEN_TTL_MINUTES)
            );

            return $this->returnData('reset_token', $resetToken, __('api.Api_Verification_code_is_correct'));
        } catch (Exception $ex) {
            Log::error('foregetPassword_CheckCode: ' . $ex->getMessage(), [
                'trace' => $ex->getTraceAsString(),
                'input' => $request->all(),
            ]);
            return $this->returnError(__('api.Api_Error_Contact_Programmer'));
        }
    }

    public function foregetPassword_ResetPassword(ResetPasswordRequest $request)
    {
        try {
         $user = User::where('mobile', $request->mobile)
 ->when($request->filled('email'), fn($q) => $q->orWhere('email', $request->email))
 ->whereNull('deleted_at')
 ->first();

            if (!$user) {
                return $this->returnError(__('api.User_Not_Found'));
            }

            $cachedToken = Cache::get("password_reset_token:{$user->id}");

            if (!$cachedToken || !hash_equals($cachedToken, (string) $request->reset_token)) {
                return $this->returnError(__('api.Api_code_is_expire'));
            }

            $user->update(['password' => bcrypt($request->password)]);
            Cache::forget("password_reset_token:{$user->id}");

            return $this->returnSuccessMessage(__('api.Api_Password_updated_successfully'));
        } catch (Exception $ex) {
            Log::error('foregetPassword_ResetPassword: ' . $ex->getMessage(), [
                'trace' => $ex->getTraceAsString(),
                'input' => $request->all(),
            ]);
            return $this->returnError(__('api.Api_Error_Contact_Programmer'));
        }
    }

    public function logout(Request $request)
    {
        try {
           // $request->user()->currentAccessToken()->delete();
              // ← حذف جميع التوكنات بدلاً من التوكن الحالي فقط
        $request->user()->tokens()->delete();
            return $this->returnSuccessMessage(__('api.Api_You_have_successfully_logged_out'));
        } catch (Exception $ex) {
            Log::error('logout' . $ex->getMessage(), [
                'trace' => $ex->getTraceAsString(),
                'input' => $request->all(),
            ]);
            return $this->returnError(__('api.Api_Error_Contact_Programmer'));
        }
    }

    public function destroy(Request $request)
    {
        try {
            $user = $request->user();
           // $request->user()->currentAccessToken()->delete();
            // ← حذف جميع التوكنات
        $user->tokens()->delete();
            $user->update(['is_active' => 0]);

            return $this->returnSuccessMessage(__('api.The_account_has_been_successfully_deleted'));
        } catch (Exception $ex) {
            Log::error('destroy' . $ex->getMessage(), [
                'trace' => $ex->getTraceAsString(),
                'input' => $request->all(),
            ]);
            return $this->returnError(__('api.Api_Error_Contact_Programmer'));
        }
    }

    public function updatePassword(UpdatePasswordRequest $request)
    {
        try {
            $user = $request->user();
            $user->update(['password' => bcrypt($request->password)]);

            return $this->returnSuccessMessage(__('dashboard.Edit_success'));
        } catch (Exception $ex) {
            Log::error('updatePassword' . $ex->getMessage(), [
                'trace' => $ex->getTraceAsString(),
                'input' => $request->all(),
            ]);
            return $this->returnError(__('api.Api_Error_Contact_Programmer'));
        }
    }
}
