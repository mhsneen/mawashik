<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\Product;
use App\Models\Attribute;
use App\Models\AttributeValue;
use Illuminate\Database\Seeder;

class OrderItemSeeder extends Seeder
{
    public function run()
    {
        $orders   = Order::take(2)->get();
        $products = Product::take(3)->get();

        if ($orders->isEmpty() || $products->isEmpty()) {
            return;
        }

        // ========= الطلب الأول =========
        $item1 = $orders[0]->items()->create([
            'product_id'  => $products[0]->id,
            'quantity'    => 1,
            'unit_price'  => 100,
            'total_price' => 100,
        ]);

        // ========= خصائص المنتج =========
        $this->attachAttributesToItem($item1);

        // ========= منتج ثاني =========
        $item2 = $orders[0]->items()->create([
            'product_id'  => $products[1]->id,
            'quantity'    => 1,
            'unit_price'  => 100,
            'total_price' => 100,
        ]);

        $this->attachAttributesToItem($item2);

        // ========= الطلب الثاني =========
        $item3 = $orders[1]->items()->create([
            'product_id'  => $products[2]->id,
            'quantity'    => 1,
            'unit_price'  => 120,
            'total_price' => 120,
        ]);

        $this->attachAttributesToItem($item3);
    }

    private function attachAttributesToItem($item)
    {
        // ===== COLOR =====
        $colorAttr = Attribute::where('type', 'color')->first();
        if ($colorAttr) {
            $colorValue = AttributeValue::where('attribute_id', $colorAttr->id)->first();

            $item->attributes()->create([
                'attribute_id'       => $colorAttr->id,
                'attribute_value_id' => $colorValue->id,
                'attribute_name'     => $colorAttr->name,
                'value_name'         => $colorValue->translate('ar')->value,
                'color_code'         => $colorValue->color_code,
            ]);
        }


        // ===== SIZE =====
        $sizeAttr = Attribute::where('type', 'select')->first();
        if ($sizeAttr) {
            $sizeValue = AttributeValue::where('attribute_id', $sizeAttr->id)->first();
            if ($sizeValue) { // ✅ فقط إذا موجود
                $item->attributes()->create([
                    'attribute_id'        => $sizeAttr->id,
                    'attribute_value_id'  => $sizeValue->id,
                    'attribute_name'      => $sizeAttr->name,
                    'value_name'          => $sizeValue->translate('ar')->value,
                ]);
            }
        }
    }
}
