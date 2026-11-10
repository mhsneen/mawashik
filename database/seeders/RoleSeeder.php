<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {



        // Admin / Employee → web
        Role::create(['name' => 'admin', 'guard_name' => 'admin']);
        Role::create(['name' => 'employee', 'guard_name' => 'admin']);

        // المستخدمون عبر تطبيقات الموبايل (Sanctum)
        Role::create(['name' => 'client', 'guard_name' => 'web']);
        Role::create(['name' => 'trader', 'guard_name' => 'web']);   // التاجر
        Role::create(['name' => 'driver', 'guard_name' => 'web']);  // المندوب
    }
}
