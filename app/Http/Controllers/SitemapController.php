<?php

namespace App\Http\Controllers;

use App\Models\Album;
use App\Models\Attraction;
use App\Models\Package;
use App\Models\Post;
use App\Models\Umkm;

class SitemapController extends Controller
{
    public function __invoke()
    {
        $static = ['home', 'profile', 'about', 'packages.index', 'custom', 'attractions.index', 'umkm.index', 'products', 'gallery.index', 'news.index', 'faq', 'contact', 'reviews', 'booking.create'];

        $entries = collect($static)->map(fn ($name) => [
            'id' => lroute($name, [], 'id'),
            'en' => lroute($name, [], 'en'),
            'updated' => null,
        ]);

        foreach ([Package::query()->active()->get(), Attraction::query()->active()->get(), Umkm::query()->active()->get(), Album::query()->active()->get(), Post::query()->published()->get()] as $models) {
            foreach ($models as $model) {
                $entries->push(['id' => $model->url('id'), 'en' => $model->url('en'), 'updated' => $model->updated_at]);
            }
        }

        return response()->view('sitemap', ['entries' => $entries], 200, ['Content-Type' => 'application/xml']);
    }
}
