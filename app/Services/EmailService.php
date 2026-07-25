<?php

namespace App\Services;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Models\Setting;
use Illuminate\Mail\Mailable;

class EmailService
{
    /**
     * إرسال إيميل بناءً على Mailable Class
     */
    public function send(string $email, Mailable $mailableInstance): bool
    {
        $this->setMailConfig();

        try {
            Mail::to($email)->send($mailableInstance);
            return true;
        } catch (\Exception $e) {
            Log::error("Email Exception: " . $e->getMessage());
            return false;
        }
    }

    /**
     * إرسال إيميل نصي بسيط (بدون قالب معقد)
     */
    public function sendRaw(string $email, string $subject, string $body): bool
    {
        $this->setMailConfig();

        try {
            Mail::raw($body, function ($message) use ($email, $subject) {
                $message->to($email)->subject($subject);
            });
            return true;
        } catch (\Exception $e) {
            Log::error("Raw Email Exception: " . $e->getMessage());
            return false;
        }
    }

    /**
     * تهيئة إعدادات الإيميل من قاعدة البيانات
     */
    protected function setMailConfig(): void
    {
        $settings = Setting::whereIn('key', [
            'mail_mailer', 'mail_host', 'mail_port', 'mail_username',
            'mail_password', 'mail_encryption', 'mail_from_address', 'mail_from_name'
        ])->pluck('plain_value', 'key');

        config([
            'mail.default'                   => $settings['mail_mailer'] ?? 'smtp',
            'mail.mailers.smtp.host'         => $settings['mail_host'] ?? '',
            'mail.mailers.smtp.port'         => $settings['mail_port'] ?? 587,
            'mail.mailers.smtp.username'     => $settings['mail_username'] ?? '',
            'mail.mailers.smtp.password'     => $settings['mail_password'] ?? '',
            'mail.mailers.smtp.encryption'   => $settings['mail_encryption'] ?? 'tls',
            'mail.from.address'              => $settings['mail_from_address'] ?? 'no-reply@example.com',
            'mail.from.name'                 => $settings['mail_from_name'] ?? 'My App',
        ]);
    }
}
