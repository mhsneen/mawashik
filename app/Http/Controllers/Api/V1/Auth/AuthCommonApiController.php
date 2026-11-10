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

    /**
     * تسجيل الدخول:
     * - لو نفس الجهاز (device_token) والحساب مفعّل → دخول مباشر.
     * - لو جهاز جديد أو وضع OTP مفعّل → يرسل كود تحقق.
     */
    public function login(LoginRequest $request, OtpService $otpService)
    {
        // 1. البحث عن المستخدم
        $user = $this->findUser($request);

        if (!$user) {
            return $this->returnError(__('dashboard.error_login'));
        }

        // 2. منع المستخدم المحظور
        if ($user->is_blocked) {
            return $this->returnError(__('api.account_blocked'));
        }

        // 3. ✅ لو الحساب مفعّل وبنفس جهاز الدخول السابق (نفس device_token) → دخول مباشر بدون OTP
        if (
            $user->is_active &&
            $request->filled('device_token') &&
            $user->device_token === $request->device_token
        ) {
            // تحديث التوكن وإصدار Token للمصادقة
            $user->update(['device_token' => $request->device_token]);
            $user->tokens()->delete();
            $token = $user->createToken($user->name . 'Auth-Token')->plainTextToken;
            $user->access_token = $token; // إضافة التوكن كـ attribute للـ Resource

            return $this->returnData('data', new UserResource($user), __('api.Api_You_have_successfully_logged_in'));
        }

        // 4. بوضع "مباشر" (لو الإدارة ألغت الـ OTP نهائياً من الإعدادات)
        if ($otpService->isDirectMode()) {
            $user->update(['device_token' => $request->device_token]);
            $user->tokens()->delete();
            $token = $user->createToken($user->name . 'Auth-Token')->plainTextToken;
            $user->access_token = $token;

            return $this->returnData('data', new UserResource($user), __('api.Api_You_have_successfully_logged_in'));
        }

        // 5. إرسال كود OTP (للأجهزة الجديدة أو لو الحساب غير مفعّل)
        try {
            // نقوم بتحديث الـ device_token هنا ليُحفظ عند نجاح التحقق لاحقاً
            // أو يُمكن تحديثه الآن، لكن الأفضل تحديثه عند تأكيد الكود لضمان أن المستخدم هو صاحب الجهاز
            $otpService->generateAndSend($user, OtpCode::PURPOSE_LOGIN);
        } catch (Exception $ex) {
            Log::error('login OTP generation: ' . $ex->getMessage());
            return $this->returnError(__('api.Api_Error_Contact_Programmer'));
        }

        $data = [
            'requires_verification' => true,
        ];

        return $this->returnData('data', $data, __('api.A verification code has been sent for login'));
    }

    /**
     * التحقق من كود تسجيل الدخول.
     */
    public function check_login_code(VerifyOtpRequest $request, OtpService $otpService)
    {
        try {
            $user = $this->findUser($request);

            if (!$user) {
                return $this->returnError(__('api.User_Not_Found'));
            }

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

            // ✅ تفعيل الحساب وتحديث الـ device_token للجهاز الجديد
            $user->update([
                'device_token' => $request->device_token,
                'is_active' => 1
            ]);

            $user->tokens()->delete();
            $token = $user->createToken($user->name . 'Auth-Token')->plainTextToken;
            $user->access_token = $token;

            return $this->returnData('data', new UserResource($user), __('api.Api_You_have_successfully_logged_in'));
        } catch (Exception $ex) {
            Log::error('check_login_code: ' . $ex->getMessage(), [
                'trace' => $ex->getTraceAsString(),
                'input' => $request->all(),
            ]);
            return $this->returnError(__('api.Api_Error_Contact_Programmer'));
        }
    }

    /**
     * إعادة إرسال كود تسجيل الدخول.
     */
    public function rsend_login_code(ResendOtpRequest $request, OtpService $otpService)
    {
        try {
            $user = $this->findUser($request);

            if (!$user) {
                return $this->returnError(__('api.User_Not_Found'));
            }

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

    /**
     * نسيت كلمة المرور - إرسال الكود.
     */
    public function foreget_Password(ForgetPasswordRequest $request, OtpService $otpService)
    {
        try {
            $user = $this->findUser($request);

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

    /**
     * نسيت كلمة المرور - التحقق من الكود وإصدار توكن إعادة التعيين.
     */
    public function foregetPassword_CheckCode(VerifyOtpRequest $request, OtpService $otpService)
    {
        try {
            $user = $this->findUser($request);

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

            // توكن مؤقت يسمح بتنفيذ خطوة إعادة التعيين فقط
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

    /**
     * نسيت كلمة المرور - إعادة تعيين كلمة المرور.
     */
    public function foregetPassword_ResetPassword(ResetPasswordRequest $request)
    {
        try {
            $user = $this->findUser($request);

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

    /**
     * تسجيل الخروج.
     */
    public function logout(Request $request)
    {
        try {
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

    /**
     * حذف الحساب (تعطيل).
     */
    public function destroy(Request $request)
    {
        try {
            $user = $request->user();
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

    /**
     * تغيير كلمة المرور من داخل التطبيق.
     */
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

    /**
     * ✅ دالة مساعدة موحدة للبحث عن المستخدم (عبر الجوال أو الإيميل).
     */
    protected function findUser($request)
    {
        return User::when($request->filled('email'), function ($query) use ($request) {
            return $query->where('email', $request->email);
        }, function ($query) use ($request) {
            return $query->where('mobile', $request->mobile);
        })
        ->whereNull('deleted_at')
        ->first();
    }
}
