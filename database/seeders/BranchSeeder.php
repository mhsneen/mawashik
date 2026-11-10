<?php

namespace Database\Seeders;

use App\Models\Branch;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class BranchSeeder extends Seeder
{
    protected array $staffTables = ['employees', 'admins', 'staff'];

    public function run(): void
    {
        $main = Branch::firstOrNew(['is_main' => true]);

        if (!$main->exists) {
            $main->is_active  = true;
            $main->sort_order = 0;
            $main->save();
        }

        // الترجمات تُحفظ عبر translateOrNew() -> fill() -> save() حسب اتفاقية الحزمة،
        // فلا داعي للتعامل مع جدول branch_translations مباشرة.
        $main->translateOrNew('ar')->fill(['name' => 'الفرع الرئيسي', 'address' => null]);
        $main->translateOrNew('en')->fill(['name' => 'Main Branch', 'address' => null]);
        $main->save();

        // تعبئة branch_id لكل الصفوف الموجودة مسبقاً حتى لا يبقى أي طلب/موظف بدون فرع.
        if (Schema::hasTable('orders') && Schema::hasColumn('orders', 'branch_id')) {
            DB::table('orders')->whereNull('branch_id')->update(['branch_id' => $main->id]);
        }

        foreach ($this->staffTables as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'branch_id')) {
                DB::table($table)->whereNull('branch_id')->update(['branch_id' => $main->id]);
            }
        }

        $this->command?->info("Branch seeding done. Main branch id: {$main->id}");
    }
}
