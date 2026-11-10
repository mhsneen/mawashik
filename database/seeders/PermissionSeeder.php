<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            // === الإعدادات ===
            'General_Settings',
            'Store_Settings',
            'Delivery_Settings',
            'Geo_Areas',
            'Social_Media',
            'Cancel_Reason_Order',

            // === المستخدمين ===
            'Clients',
            'Drivers',
            'Traders',
            'Trader_Approvals',

            // === المالية والعمولات ===
            'Trader_Commissions',
            'Driver_Commissions',
            'Deposits_Settings',
            'Slaughter_Service',
            'Reports_Deposits',
            'Reports_Commissions',
            'Reports_Slaughter',
            'Reports_Traders',

            // === الطلبات والمتجر ===
            'Orders',
            'Products',                  // يشمل إدارة المنتجات واعتمادها
            'Categories',
            'Offers and discounts',

            // === المحافظ ===
            'Client_Wallets',
            'Driver_Wallets',
            'Trader_Wallets',

            // === التقارير ===
            'The_Sales',
            'Products_purchased',

            // === التنبيهات والأقسام الأخرى ===
            'Alerts',
            'TextPages',
            'Slideshow',
            'Contact_Us',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'admin',
            ]);
        }
    }
}
