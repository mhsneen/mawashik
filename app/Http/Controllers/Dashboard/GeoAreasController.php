<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\{Country, City, Area, Neighborhood};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * GeoAreasController — إدارة المناطق الجغرافية.
 *
 * هرمية: Countries → Cities → Areas → Neighborhoods
 * تدعم الترجمة (عربي/إنجليزي).
 */
class GeoAreasController extends Controller
{
    // ====== Countries ======
    public function countriesIndex(Request $request)
    {
        $countries = Country::with('translations')
            ->when($request->filled('search_name'), fn($q) =>
                $q->whereHas('translations', fn($t) =>
                    $t->where('name', 'like', '%' . $request->search_name . '%')))
            ->orderBy('id', 'asc')
            ->paginate(15);

        return view('dashboard.geo.countries', compact('countries'));
    }

public function countriesStore(Request $request)
{
    $request->validate([
        'is_active' => 'required|boolean',
        'name_ar'   => 'required|string|max:100',
        'name_en'   => 'required|string|max:100',
    ]);

    DB::transaction(function () use ($request) {
        Country::create([
            'is_active' => $request->boolean('is_active'),
            'ar' => ['name' => $request->name_ar],
            'en' => ['name' => $request->name_en],
        ]);
    });

    return redirect()->back()->with(
        'success',
        __('dashboard.Country_Created')
    );
}

    public function countriesUpdate(Request $request, $id)
    {
        $request->validate([
             'is_active' => 'required|boolean',
        'name_ar'   => 'required|string|max:100',
        'name_en'   => 'required|string|max:100',
        ]);

        DB::transaction(function () use ($request, $id) {
            $country = Country::findOrFail($id);
            $country->update([
              'is_active' => $request->boolean('is_active'),
            'ar' => ['name' => $request->name_ar],
            'en' => ['name' => $request->name_en],
            ]);

        });

        return redirect()->back()->with('success', __('dashboard.Country_Updated'));
    }

    public function countriesDestroy($id)
    {
        $country = Country::findOrFail($id);
        $country->delete();
        return redirect()->back()->with('success', __('dashboard.Country_Deleted'));
    }

    // ====== Cities ======
    public function citiesIndex(Request $request)
    {
        $cities = City::with(['country.translations', 'translations'])
            ->when($request->filled('country_id'), fn($q) => $q->where('country_id', $request->country_id))
            ->when($request->filled('search_name'), fn($q) =>
                $q->whereHas('translations', fn($t) =>
                    $t->where('name', 'like', '%' . $request->search_name . '%')))
            ->orderBy('id', 'asc')
            ->paginate(15);

        $countries = Country::with('translations')->get();
        return view('dashboard.geo.cities', compact('cities', 'countries'));
    }

    public function citiesStore(Request $request)
    {
        $request->validate([
            'country_id' => 'required|exists:countries,id',
           'is_active' => 'required|boolean',
        'name_ar'   => 'required|string|max:100',
        'name_en'   => 'required|string|max:100',
        ]);

        DB::transaction(function () use ($request) {
            $city = City::create([
                'country_id' => $request->country_id,
              'is_active' => $request->boolean('is_active'),
            'ar' => ['name' => $request->name_ar],
            'en' => ['name' => $request->name_en],
            ]);
        });

        return redirect()->back()->with('success', __('dashboard.City_Created'));
    }

    public function citiesUpdate(Request $request, $id)
    {
        $request->validate([
            'country_id' => 'required|exists:countries,id',
              'is_active' => 'required|boolean',
        'name_ar'   => 'required|string|max:100',
        'name_en'   => 'required|string|max:100',
        ]);

        DB::transaction(function () use ($request, $id) {
            $city = City::findOrFail($id);
            $city->update([
                'country_id' => $request->country_id,
                'is_active' => $request->boolean('is_active'),
            'ar' => ['name' => $request->name_ar],
            'en' => ['name' => $request->name_en],
            ]);

        });

        return redirect()->back()->with('success', __('dashboard.City_Updated'));
    }

    public function citiesDestroy($id)
    {
        City::findOrFail($id)->delete();
        return redirect()->back()->with('success', __('dashboard.City_Deleted'));
    }

    // ====== Areas ======
    public function areasIndex(Request $request)
    {
        $areas = Area::with(['city.country.translations', 'city.translations', 'translations'])
            ->when($request->filled('city_id'), fn($q) => $q->where('city_id', $request->city_id))
            ->when($request->filled('search_name'), fn($q) =>
                $q->whereHas('translations', fn($t) =>
                    $t->where('name', 'like', '%' . $request->search_name . '%')))
            ->orderBy('id', 'asc')
            ->paginate(15);

        $cities = City::with('translations')->get();
        return view('dashboard.geo.areas', compact('areas', 'cities'));
    }

    public function areasStore(Request $request)
    {
        $request->validate([
            'city_id'   => 'required|exists:cities,id',
           'is_active' => 'required|boolean',
        'name_ar'   => 'required|string|max:100',
        'name_en'   => 'required|string|max:100',
        ]);

        DB::transaction(function () use ($request) {
            $area = Area::create([
                'city_id'   => $request->city_id,
           'is_active' => $request->boolean('is_active'),
            'ar' => ['name' => $request->name_ar],
            'en' => ['name' => $request->name_en],
            ]);

        });

        return redirect()->back()->with('success', __('dashboard.Area_Created'));
    }

    public function areasDestroy($id)
    {
        Area::findOrFail($id)->delete();
        return redirect()->back()->with('success', __('dashboard.Area_Deleted'));
    }

    // ====== Neighborhoods ======
    public function neighborhoodsIndex(Request $request)
    {
        $neighborhoods = Neighborhood::with(['area.city.translations', 'translations'])
            ->when($request->filled('area_id'), fn($q) => $q->where('area_id', $request->area_id))
            ->when($request->filled('search_name'), fn($q) =>
                $q->whereHas('translations', fn($t) =>
                    $t->where('name', 'like', '%' . $request->search_name . '%')))
            ->orderBy('id', 'asc')
            ->paginate(15);

        $areas = Area::with(['translations', 'city.translations'])->get();
        return view('dashboard.geo.neighborhoods', compact('neighborhoods', 'areas'));
    }

    public function neighborhoodsStore(Request $request)
    {
        $request->validate([
            'area_id'   => 'required|exists:areas,id',
             'is_active' => 'required|boolean',
        'name_ar'   => 'required|string|max:100',
        'name_en'   => 'required|string|max:100',
        ]);

        DB::transaction(function () use ($request) {
            $n = Neighborhood::create([
                'area_id'   => $request->area_id,
                'is_active' => $request->boolean('is_active'),
            'ar' => ['name' => $request->name_ar],
            'en' => ['name' => $request->name_en],
            ]);

        });

        return redirect()->back()->with('success', __('dashboard.Neighborhood_Created'));
    }

    public function neighborhoodsDestroy($id)
    {
        Neighborhood::findOrFail($id)->delete();
        return redirect()->back()->with('success', __('dashboard.Neighborhood_Deleted'));
    }
}
