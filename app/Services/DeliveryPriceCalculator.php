<?php

namespace App\Services;

use App\Models\Setting;
use InvalidArgumentException;

class DeliveryPriceCalculator
{
    /**
     * @param float $traderLat خط عرض متجر التاجر
     * @param float $traderLng خط طول متجر التاجر
     * @param float $userLat خط عرض العميل
     * @param float $userLng خط طول العميل
     * @return array{distance: float, price: float}
     */
    public function calculate(float $traderLat, float $traderLng, float $userLat, float $userLng): array
    {
        $settings = Setting::whereIn('key', [
            'delivery_max_distance',
            'delivery_price_type',
            'fixed_delivery_price',
            'dynamic_delivery_pricing',
        ])->pluck('plain_value', 'key');

        $maxDistance = (float) ($settings['delivery_max_distance'] ?? 0);

        // ✅ حساب المسافة بين متجر التاجر والعميل
        $distance = $this->distanceInKm($traderLat, $traderLng, $userLat, $userLng);

        if ($distance > $maxDistance) {
            throw new InvalidArgumentException(__('api.location_outside_delivery_range'));
        }

        $priceType = $settings['delivery_price_type'] ?? 1; // 1 = ثابت، 2 = حسب المسافة
        $price = 0;

        if ($priceType == 1) {
            $price = (float) ($settings['fixed_delivery_price'] ?? 0);
        } elseif ($priceType == 2) {
            $dynamicPricing = json_decode($settings['dynamic_delivery_pricing'] ?? '[]', true);

            if (is_array($dynamicPricing)) {
                foreach ($dynamicPricing as $interval) {
                    $from = (float) ($interval['from'] ?? 0);
                    $to = ($interval['to'] ?? null) === 'unlimited' ? INF : (float) ($interval['to'] ?? 0);

                    if ($distance >= $from && $distance <= $to) {
                        $price = (float) ($interval['price'] ?? 0);
                        break;
                    }
                }
            }
        }

        return [
            'distance' => round($distance, 2),
            'price' => round($price, 2),
        ];
    }

    protected function distanceInKm($lat1, $lon1, $lat2, $lon2): float
    {
        $earthRadius = 6371;
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat / 2) * sin($dLat / 2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $earthRadius * $c;
    }
}
