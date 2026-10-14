<?php

namespace App\Http\Controllers;

use App\Models\Package;
use App\Services\BookingService;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    public function index(Request $request)
    {
        $category = $request->query('kategori');

        $packages = Package::query()->active()->with('tiers')
            ->when(in_array($category, config('sukajadi.package_categories'), true), fn ($q) => $q->where('category', $category))
            ->get();

        return view('packages.index', compact('packages', 'category'));
    }

    public function show(string $slug)
    {
        $package = Package::query()->where('is_active', true)->whereSlug($slug)
            ->with(['tiers', 'photos', 'attractions' => fn ($q) => $q->where('is_active', true), 'umkms' => fn ($q) => $q->where('is_active', true)])
            ->firstOrFail();

        view()->share('alternateUrls', ['id' => $package->url('id'), 'en' => $package->url('en')]);

        return view('packages.show', [
            'package' => $package,
            'others' => Package::query()->active()->with('tiers')->whereKeyNot($package->id)->take(3)->get(),
        ]);
    }

    public function custom(BookingService $service)
    {
        return view('packages.custom', [
            'attractions' => $service->customAttractions(),
            'basePrice' => $service->customBasePrice(),
            'minPax' => $service->customMinPax(),
        ]);
    }
}
