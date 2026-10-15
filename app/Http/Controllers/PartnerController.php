<?php

namespace App\Http\Controllers;

use App\Models\Partner;
use Illuminate\Http\Request;

class PartnerController extends Controller
{
    public function index(Request $request)
    {
        $category = $request->query('kategori');

        $partners = Partner::query()->active()
            ->with(['offers' => fn ($q) => $q->where('is_active', true)])
            ->when(in_array($category, config('sukajadi.partner_categories'), true), fn ($q) => $q->where('category', $category))
            ->get();

        return view('partners.index', compact('partners', 'category'));
    }

    public function show(string $slug)
    {
        $partner = Partner::query()->where('is_active', true)->whereSlug($slug)
            ->with(['photos', 'offers' => fn ($q) => $q->where('is_active', true)])
            ->firstOrFail();

        view()->share('alternateUrls', ['id' => $partner->url('id'), 'en' => $partner->url('en')]);

        return view('partners.show', [
            'partner' => $partner,
            'others' => Partner::query()->active()->whereKeyNot($partner->id)->with(['offers' => fn ($q) => $q->where('is_active', true)])->take(3)->get(),
        ]);
    }
}
