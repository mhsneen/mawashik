<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\Socialmedialink;

class WelcomeController extends Controller
{
    public function index()
    {
        $settings = Setting::whereIn('key', [
            'store_logo', 'store_name', 'store_email', 'store_mobile',
            'store_whatsapp', 'store_address',
            'free_shipping_sentence', 'free_shipping_details',
            'return_policy_sentence', 'return_policy_details',
            'secure_payment_sentence', 'secure_payment_details',
            'appStorePageLink','googlePlayPageLink',
        ])->get()->keyBy('key');

        $socialLinks = Socialmedialink::select('id', 'icon', 'link', 'name')->get();

        return view('site.welcome', compact('settings', 'socialLinks'));
    }
}
