<?php

namespace App\Http\Controllers;

use App\Models\Album;
use Illuminate\Http\Request;

class GalleryController extends Controller
{
    public function index(Request $request)
    {
        $category = $request->query('kategori');

        $albums = Album::query()->active()
            ->withCount('photos')
            ->with('coverPhoto')
            ->when(in_array($category, config('sukajadi.album_categories'), true), fn ($q) => $q->where('category', $category))
            ->get();

        return view('gallery.index', compact('albums', 'category'));
    }

    public function show(string $slug)
    {
        $album = Album::query()->where('is_active', true)->whereSlug($slug)->with('photos')->firstOrFail();

        view()->share('alternateUrls', ['id' => $album->url('id'), 'en' => $album->url('en')]);

        return view('gallery.show', [
            'album' => $album,
            'albums' => Album::query()->active()->whereKeyNot($album->id)->with('coverPhoto')->withCount('photos')->get(),
        ]);
    }
}
