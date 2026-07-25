<?php

namespace App\Services;

use App\Models\{User, OtpCode, Setting};
use Illuminate\Support\Facades\Log;
use Exception;
use App\Mail\VerificationCodeMail;
use App\Mail\ResetPasswordCodeMail;

class OtpService
{
    // أقصى عدد محاولات تحقق فاشلة قبل ما نطلب كود جديد
    protected const MAX_ATTEMPTS = 5;

    protected array $messagesMap = [
        'registration'   => 'api.Api_Verification_code_signup',
        'login'          => 'api.Your_login_code_is',
        'password_reset' => 'api.Your_password_reset_code_is',
    ];

    protected array $mailablesMap = [
        'registration'   => VerificationCodeMail::class,
        'login'          => VerificationCodeMail::class,
        'password_reset' => ResetPasswordCodeMail::class,
    ];

    public function __construct(
        protected SmsService $smsService,
        protected WhatsappService $whatsappService,
        protected EmailService $emailService
    ) {}

    /**
     * هل النظام بوضع "تفعيل مباشر بدون تحقق"؟
     * يُستخدم من الكونترولر قبل استدعاء generateAndSend عشان يقرر شكل الرد
     * (يرجّع توكن مباشرة، أو يرجّع رسالة "تم إرسال الكود").
     */
    public function isDirectMode(): bool
    {
        $mode = Setting::where('key', 'registration_activation_mode')
            ->value('plain_value') ?? 'verification';

        return $mode === 'direct';
    }

    /**
     * توليد وإرسال رمز التحقق
     */
    public function generateAndSend(User $user, string $purpose): void
    {
        $generalSettings = Setting::whereIn('key', [
            'verification_method', 'registration_activation_mode', 'test_mode'
        ])->pluck('plain_value', 'key');

        $verificationMethod = $generalSettings['verification_method'] ?? 'sms';
        $testMode = filter_var($generalSettings['test_mode'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $directMode = ($generalSettings['registration_activation_mode'] ?? 'verification') === 'direct';

        // بوضع "مباشر": التسجيل يفعّل الحساب فورًا بدون كود.
        // تسجيل الدخول بهالوضع يُعالَج من الكونترولر نفسه عبر isDirectMode() قبل ما يوصل لهون أصلاً.
        if ($purpose === OtpCode::PURPOSE_REGISTRATION && $directMode) {
            $user->update(['is_active' => 1]);
            return;
        }

        $contact = $verificationMethod === 'email' ? $user->email : $user->mobile;
        if (!$contact) {
            throw new Exception("Contact info is missing for OTP.");
        }

        $code = $testMode ? 1111 : mt_rand(1000, 9999);

        try {
            OtpCode::where('verifiable_id', $user->id)
                ->where('verifiable_type', User::class)
                ->where('purpose', $purpose)
                ->where('is_used', false)
                ->delete();

            OtpCode::create([
                'verifiable_id'   => $user->id,
                'verifiable_type' => User::class,
                'purpose'         => $purpose,
                'method'          => $verificationMethod,
                'contact'         => $contact,
                'code'            => $code,
                'attempts'        => 0,
                'is_used'         => false,
                'expires_at'      => now()->addMinutes(10),
            ]);

            if (!$testMode) {
                $messageKey = $this->messagesMap[$purpose] ?? 'api.Your_code_is';
                $message = __($messageKey) . " $code";

                $mailableClass = $this->mailablesMap[$purpose] ?? VerificationCodeMail::class;

                $sent = match ($verificationMethod) {
                    'sms'      => $this->smsService->send($contact, $message),
                    'whatsapp' => $this->whatsappService->send($contact, $message),
                    'email'    => $this->emailService->send($contact, new $mailableClass($code)),
                    default    => throw new Exception("Invalid verification method"),
                };

                if (!$sent) {
                    throw new Exception("Provider failed to deliver the OTP.");
                }
            }
        } catch (Exception $e) {
            Log::error("OTP Generation Error: " . $e->getMessage());
            throw new Exception("Failed to send OTP code.");
        }
    }

    /**
     * التحقق من صحة الرمز
     *
     * @throws Exception إذا تجاوز المستخدم عدد المحاولات المسموح (يُنصح بمعاملتها كخطأ "اطلب كود جديد")
     */
    public function verify(User $user, string $code, string $purpose): bool
    {
        $otp = OtpCode::where('verifiable_id', $user->id)
            ->where('verifiable_type', User::class)
            ->where('purpose', $purpose)
            ->where('is_used', false)
            ->latest()
            ->first();

        if (!$otp) {
            return false;
        }

        if ($otp->isExpired()) {
            $otp->delete();
            return false;
        }

        if ($otp->attempts >= self::MAX_ATTEMPTS) {
            $otp->delete();
            throw new Exception('too_many_attempts');
        }

        if ((string) $otp->code !== (string) $code) {
            $otp->increment('attempts');
            return false;
        }

        $otp->update(['is_used' => true]);
        return true;
    }
}
