<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\{Country, City, Area, Neighborhood};

class GeoAreasSeeder extends Seeder
{
    public function run(): void
    {
        // ====== السعودية ======
        $ksa = Country::create([
            'is_active' => true,
            'ar' => ['name' => 'المملكة العربية السعودية'],
            'en' => ['name' => 'Saudi Arabia'],
        ]);

        // ====== الرياض ======
        $ryd = City::create([
            'country_id' => $ksa->id,
            'is_active' => true,
            'ar' => ['name' => 'الرياض'],
            'en' => ['name' => 'Riyadh'],
        ]);

        // منطقة شمال الرياض
        $north = Area::create([
            'city_id' => $ryd->id,
            'is_active' => true,
            'ar' => ['name' => 'شمال الرياض'],
            'en' => ['name' => 'North Riyadh'],
        ]);

        // أحياء شمال الرياض
        $this->addNeighborhoods($north, [
            'العليا'    => 'Al Olaya',
            'الملك فهد' => 'King Fahd',
            'النخيل'    => 'Al Nakheel',
        ]);

        // منطقة جنوب الرياض
        $south = Area::create([
            'city_id' => $ryd->id,
            'is_active' => true,
            'ar' => ['name' => 'جنوب الرياض'],
            'en' => ['name' => 'South Riyadh'],
        ]);

        // أحياء جنوب الرياض
        $this->addNeighborhoods($south, [
            'السليمانية' => 'Al Sulaimaniyah',
            'الشهداء'    => 'Al Shuhada',
            'البطحاء'    => 'Al Batha',
        ]);

        // ====== جدة ======
        $jed = City::create([
            'country_id' => $ksa->id,
            'is_active' => true,
            'ar' => ['name' => 'جدة'],
            'en' => ['name' => 'Jeddah'],
        ]);

        // منطقة شمال جدة
        $jedNorth = Area::create([
            'city_id' => $jed->id,
            'is_active' => true,
            'ar' => ['name' => 'شمال جدة'],
            'en' => ['name' => 'North Jeddah'],
        ]);

        // أحياء شمال جدة
        $this->addNeighborhoods($jedNorth, [
            'الحمراء' => 'Al Hamra',
            'الروضة'  => 'Al Rawdah',
            'السلامة' => 'Al Salamah',
        ]);

        // ====== الدمام ======
        $dmm = City::create([
            'country_id' => $ksa->id,
            'is_active' => true,
            'ar' => ['name' => 'الدمام'],
            'en' => ['name' => 'Dammam'],
        ]);

        // منطقة وسط الدمام
        $dmmCenter = Area::create([
            'city_id' => $dmm->id,
            'is_active' => true,
            'ar' => ['name' => 'وسط الدمام'],
            'en' => ['name' => 'Central Dammam'],
        ]);

        // أحياء وسط الدمام
        $this->addNeighborhoods($dmmCenter, [
            'الفيصلية' => 'Al Faisaliah',
            'الشاطئ'   => 'Al Shati',
        ]);
    }

    /**
     * إضافة الأحياء باللغتين العربية والإنجليزية.
     */
    private function addNeighborhoods(Area $area, array $names): void
    {
        foreach ($names as $ar => $en) {
            Neighborhood::create([
                'area_id' => $area->id,
                'is_active' => true,
                'ar' => ['name' => $ar],
                'en' => ['name' => $en],
            ]);
        }
    }
}
