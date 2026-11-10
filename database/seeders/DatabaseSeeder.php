<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // ترتيب الـ seeders مهم:
        // 1) الأدوار والصلاحيات قبل أي مستخدم
        $this->call([
            RoleSeeder::class,
            PermissionSeeder::class,
        ]);

        // 2) الإعدادات الأساسية (الإعدادات الافتراضية + الدول)
        $this->call([
            SettingSeeder::class,
            PaymentMethodSeeder::class,
            GeoAreasSeeder::class,
        ]);

        // 3) المستخدمون (الأدمن الأول + فروع)
        $this->call([
            AdminSeeder::class,
            BranchSeeder::class,
            PermissionSeeder::class,
        ]);

        // 4) بيانات تجريبية (اختياري)
        // OrderSeeder::class, OrderItemSeeder::class

        // ملاحظة: User::factory تم تعطيله للحفاظ على بيانات نظيفة
    }
}
