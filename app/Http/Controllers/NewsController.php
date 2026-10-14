<?php

namespace App\Http\Controllers;

use App\Models\Post;

class NewsController extends Controller
{
    public function index()
    {
        return view('news.index', [
            'posts' => Post::query()->published()->paginate(9),
        ]);
    }

    public function show(string $slug)
    {
        $post = Post::query()->published()->whereSlug($slug)->firstOrFail();

        view()->share('alternateUrls', ['id' => $post->url('id'), 'en' => $post->url('en')]);

        return view('news.show', [
            'post' => $post,
            'others' => Post::query()->published()->whereKeyNot($post->id)->take(3)->get(),
        ]);
    }
}
