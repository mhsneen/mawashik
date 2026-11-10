<?php

namespace Database\Seeders;

use App\Models\PaymentMethod;
use Illuminate\Database\Seeder;

class PaymentMethodSeeder extends Seeder
{
    public function run(): void
    {
        $sort = 0;
        foreach (array_keys(config('payment_gateways', [])) as $code) {
            PaymentMethod::firstOrCreate(
                ['code' => $code],
                ['is_enabled' => false, 'mode' => 'test', 'sort_order' => $sort]
            );
            $sort++;
        }
    }
}
