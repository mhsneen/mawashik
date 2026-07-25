<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\User;
use App\Models\Orderstatus;
use App\Models\DiscountCodeUsage;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $status = Orderstatus::first();



        Order::create([
            'user_id' => 12,
            'status_id' => 1,
            'total_items' => 2,
            'total_price' => 200,
            'discount_amount' => 0,
            'final_price' => 200,
            'currency' => 'SAR',
            'note' => 'طلب تجريبي رقم 1',
            'latitude' => '31.41251100',
            'longitude' => '34.35202420',
            'address' => 'العنوان',
            'added_tax' => 0,
            'transaction_id' => 356666333,


        ]);

        Order::create([
            'user_id' => 12,
            'status_id' => 1,
            'total_items' => 1,
            'total_price' => 120,
            'discount_amount' => 20,
            'final_price' => 100,
            'currency' => 'SAR',
            'note' => 'طلب تجريبي رقم 2',
            'latitude' => '31.41251100',
            'longitude' => '34.35202420',
            'address' => 'العممممممنوان',
            'added_tax' => 0,
            'transaction_id' => 356666333,
            'discount_amount' => 25,
            'coupon_id' => '1',
            'coupon' => 'YELRJ',
            'cancel_reason_id' => '1',
            'cancel_other_reason' => 'سبب الغاء الطلب'


        ]);

        DiscountCodeUsage::create([
            'discount_code_id'   => 1,
            'order_id'           => 2,
            'user_id'            => 12,
            'discount_percentage' => 20,
            'discount_amount'    => 25,
            'used_at'            => Carbon::now(),
        ]);
    }
}
