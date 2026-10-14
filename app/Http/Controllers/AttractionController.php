<?php

namespace App\Http\Controllers;

use App\Models\Attraction;
use Illuminate\Http\Request;

class AttractionController extends Controller
{
    public function index(Request $request)
    {
        $category = $request->query('kategori');

        $attractions = Attraction::query()->active()
            ->when(in_array($category, config('sukajadi.attraction_categories'), true), fn ($q) => $q->where('category', $category))
            ->get();

        return view('attractions.index', compact('attractions', 'category'));
    }

    public function show(string $slug)
    {
        $attraction = Attraction::query()->where('is_active', true)->whereSlug($slug)
            ->with(['photos', 'packages' => fn ($q) => $q->where('is_active', true)->with('tiers')])
            ->firstOrFail();

        view()->share('alternateUrls', ['id' => $attraction->url('id'), 'en' => $attraction->url('en')]);

        return view('attractions.show', [
            'attraction' => $attraction,
            'others' => Attraction::query()->active()->whereKeyNot($attraction->id)->where('category', $attraction->category)->take(3)->get(),
        ]);
    }
}
