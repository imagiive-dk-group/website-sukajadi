<?php

namespace App\Http\Controllers;

use App\Models\Umkm;
use App\Models\UmkmProduct;
use Illuminate\Http\Request;

class UmkmController extends Controller
{
    public function index(Request $request)
    {
        $category = $request->query('kategori');

        $umkms = Umkm::query()->active()
            ->withCount(['products' => fn ($q) => $q->where('is_active', true)])
            ->when(in_array($category, config('sukajadi.umkm_categories'), true), fn ($q) => $q->where('category', $category))
            ->get();

        return view('umkm.index', compact('umkms', 'category'));
    }

    public function show(string $slug)
    {
        $umkm = Umkm::query()->where('is_active', true)->whereSlug($slug)
            ->with(['photos', 'products' => fn ($q) => $q->where('is_active', true), 'packages' => fn ($q) => $q->where('is_active', true)->with('tiers')])
            ->firstOrFail();

        view()->share('alternateUrls', ['id' => $umkm->url('id'), 'en' => $umkm->url('en')]);

        return view('umkm.show', [
            'umkm' => $umkm,
            'others' => Umkm::query()->active()->whereKeyNot($umkm->id)->take(3)->get(),
        ]);
    }

    public function products(Request $request)
    {
        $category = $request->query('kategori');

        $products = UmkmProduct::query()
            ->where('is_active', true)
            ->whereHas('umkm', fn ($q) => $q->where('is_active', true)
                ->when(in_array($category, config('sukajadi.umkm_categories'), true), fn ($q) => $q->where('category', $category)))
            ->with('umkm')
            ->orderBy('umkm_id')->orderBy('sort')
            ->get();

        return view('umkm.products', compact('products', 'category'));
    }
}
