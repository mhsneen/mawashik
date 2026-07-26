<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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

            'Home',
            'General_Settings',
            'Store_Settings',
            'Store_policy_settings',
            'Social_Media',
            'Cancel_Reason_Order',
            'Onboardings',
            'Clients',
            'Orders',
            'Products',
            'Categories',
            'Brands',
            'Product_Features',
            'Offers and discounts',
            'The_Sales',
            'Products_purchased',
            'Alerts',
            'TextPages',
            'Slideshow',
            'Contact_Us',
            'Working_days_and_times',
            'Delivery_Settings',
            'Payment_Methods',
            'Drivers',
            'Verification_method_settings',
            'Loyalty_Points_Settings',


        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'admin',
            ]);
        }
    }
}
