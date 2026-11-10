<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\{Country, City, Area, Neighborhood, Product};
use App\Traits\ApiTrait;
use Illuminate\Http\Request;

/**
 * GeoApiController — المناطق الجغرافية + المنتجات على الخريطة.
 *
 * Endpoints:
 *  - getCountries: قائمة الدول النشطة.
 *  - getCities: قائمة المدن النشطة (اختياري country_id).
 *  - getAreas: المناطق (city_id اختياري).
 *  - getNeighborhoods: الأحياء (area_id اختياري).
 *  - getProductsByMap: المنتجات داخل حدود جغرافية معينة.
 *
 * جميع المسارات GET لدعم caching من المتصفح.
 */
class GeoApiController extends Controller
{
    use ApiTrait;

    public function getCountries(Request $request)
    {
        $countries = Country::active()->with('translations')->get();
        return $this->returnData('data', $countries->map(fn($c) => [
            'id'         => $c->id,
            'name'       => $c->translate(app()->getLocale())?->name,
        ]), __('api.Api_Return_Data'));
    }

    public function getCities(Request $request)
    {
        $request->validate(['country_id' => 'nullable|exists:countries,id']);

        $cities = City::active()
            ->when($request->filled('country_id'), fn($q) => $q->where('country_id', $request->country_id))
            ->with('translations')
            ->get();

        return $this->returnData('data', $cities->map(fn($c) => [
            'id'   => $c->id,
            'name' => $c->translate(app()->getLocale())?->name,
        ]), __('api.Api_Return_Data'));
    }

    public function getAreas(Request $request)
    {
        $request->validate(['city_id' => 'nullable|exists:cities,id']);

        $areas = Area::active()
            ->when($request->filled('city_id'), fn($q) => $q->where('city_id', $request->city_id))
            ->with('translations')
            ->get();

        return $this->returnData('data', $areas->map(fn($a) => [
            'id'      => $a->id,
            'city_id' => $a->city_id,
            'name'    => $a->translate(app()->getLocale())?->name,
        ]), __('api.Api_Return_Data'));
    }

    public function getNeighborhoods(Request $request)
    {
        $request->validate(['area_id' => 'nullable|exists:areas,id']);

        $neighborhoods = Neighborhood::active()
            ->when($request->filled('area_id'), fn($q) => $q->where('area_id', $request->area_id))
            ->with('translations')
            ->get();

        return $this->returnData('data', $neighborhoods->map(fn($n) => [
            'id'      => $n->id,
            'area_id' => $n->area_id,
            'name'    => $n->translate(app()->getLocale())?->name,
        ]), __('api.Api_Return_Data'));
    }

    /**
     * المنتجات داخل حدود خريطة (LatLng bounds).
     * متغيرات POST:
     *  - min_lat, max_lat, min_lng, max_lng: حدود الخريطة الظاهرة للمستخدم.
     *  - city_id, area_id: (اختياري) لتضييق البحث.
     *  - category_id: (اختياري).
     */
    public function getProductsByMap(Request $request)
    {
        $request->validate([
            'min_lat'      => 'required|numeric|between:-90,90',
            'max_lat'      => 'required|numeric|between:-90,90',
            'min_lng'      => 'required|numeric|between:-180,180',
            'max_lng'      => 'required|numeric|between:-180,180',
            'city_id'      => 'nullable|exists:cities,id',
            'area_id'      => 'nullable|exists:areas,id',
            'category_id'  => 'nullable|exists:categories,id',
        ]);

        $products = Product::availableForClients()
            ->whereHas('trader', fn($q) =>
                $q->whereNotNull('store_lat')
                  ->whereNotNull('store_lng')
                  ->whereBetween('store_lat', [$request->min_lat, $request->max_lat])
                  ->whereBetween('store_lng', [$request->min_lng, $request->max_lng])
                  ->when($request->filled('city_id'),     fn($q2) => $q2->where('city_id', $request->city_id))
                  ->when($request->filled('area_id'),     fn($q2) => $q2->where('area_id', $request->area_id))
            )
            ->when($request->filled('category_id'), fn($q) => $q->where('category_id', $request->category_id))
            ->with(['translations', 'trader:id,name,store_lat,store_lng', 'category.translations'])
            ->limit(100)
            ->get();

        return $this->returnData('data', $products->map(fn($p) => [
            'id'              => $p->id,
            'name'            => $p->translate(app()->getLocale())?->name,
            'image'           => $p->image_url,
            'price'           => $p->final_price ?? $p->netPrice,
            // ✅ الإحداثيات تؤخذ من التاجر (store_lat / store_lng)، مش من المنتج.
            'lat'             => $p->trader?->store_lat,
            'lng'             => $p->trader?->store_lng,
            'trader_name'     => $p->trader?->name,
            'category_name'   => $p->category?->translate(app()->getLocale())?->name,
            'slaughter_available' => $p->slaughter_available,
        ]), __('api.Api_Return_Data'));
    }
}
