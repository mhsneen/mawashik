<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Setting::setMany([
            'store_logo' => '',
            'store_email' => '',
            'store_mobile' => '',
            'store_whatsapp' => '',
            'added_tax' => 10,
            'appClosed' => 0,
            'showFullScreenAdvert' => 0,
            'currentVersionNumber' => '1.0.0',
            'appStorePageLink' => 'htt',
            'googlePlayPageLink' => 'http',
            'delivery_price' => '',
            'bannerLink' => '',
            'minimum_price_order' => 10,
            'start_work' => '08:00',
            'end_work' => '17:00',
            'days_work' => 'Monday,Tuesday,Wednesday,Thursday,Friday',
            'longitude' => '30.000000',
            'Latitude' => '30.000000',
            'delivery_max_distance' => '10',
'open_pickup'=>'0',
            'open_work' => '1',
            'expired_quantities' => 5,
            'translatable' => [
                'store_name' => 'الجمعية العامة للذوق ',
                'store_address' => '',
                'free_shipping_sentence' => '',
                'free_shipping_details' => '',
                'return_policy_sentence' => '',
                'return_policy_details' => '',
                'secure_payment_sentence' => '',
                'secure_payment_details' => '',
                'appClosedText' => '',
            ]

        ]);
    }
}
