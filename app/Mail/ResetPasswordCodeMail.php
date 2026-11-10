<?php

namespace App\Mail;

use App\Models\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ResetPasswordCodeMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public $code;
    public $storeName;

    public function __construct($code)
    {
        $this->code = $code;
        $this->storeName = $this->resolveStoreName();
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('api.Password Reset') . ' - ' . $this->storeName
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.reset_password_code', // مسار الـ Blade
            with: [
                'code'      => $this->code,
                'storeName' => $this->storeName,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }

    /**
     * جلب اسم المتجر المترجم حسب اللغة الحالية من جدول settings
     * (fallback لاسم الشركة الثابت لو الإعداد مش موجود لأي سبب)
     */
    protected function resolveStoreName(): string
    {
        $setting = Setting::where('key', 'store_name')->first();

        if (!$setting) {
            return __('dashboard.company_name');
        }

        $locale = app()->getLocale();

        return $setting->translate($locale)->value
            ?? $setting->translate('en')->value
            ?? __('dashboard.company_name');
    }
}
