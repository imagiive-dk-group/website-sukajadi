@extends('layouts.app')

@section('title', $post->tr('title'))
@section('description', $post->tr('excerpt'))
@section('og_type', 'article')
@if($post->cover_path)
    @section('og_image', \App\Support\Images::url($post->cover_path, 'lg'))
@endif

@push('head')
    <script type="application/ld+json">{!! json_encode([
        '@context' => 'https://schema.org',
        '@type' => 'NewsArticle',
        'headline' => $post->tr('title'),
        'description' => $post->tr('excerpt'),
        'datePublished' => $post->published_at?->toIso8601String(),
        'dateModified' => $post->updated_at?->toIso8601String(),
        'publisher' => ['@type' => 'Organization', 'name' => setting('site_name'), 'logo' => ['@type' => 'ImageObject', 'url' => asset('images/logo.png')]],
        'mainEntityOfPage' => $post->url(),
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}</script>
@endpush

@section('content')
    <x-page-hero :title="$post->tr('title')" :image="$post->cover_path" :label="$post->placeholder_label"
        :breadcrumbs="[__('site.news.title') => lroute('news.index'), \Illuminate\Support\Str::limit($post->tr('title'), 40) => null]">
        @if($post->published_at)
            <p class="mt-4 text-sm text-white/75">{{ __('site.common.published') }}: <time datetime="{{ $post->published_at->toDateString() }}">{{ $post->published_at->translatedFormat('l, j F Y') }}</time></p>
        @endif
    </x-page-hero>

    <article class="py-12 sm:py-16">
        <div class="container-site max-w-3xl">
            @if($post->tr('excerpt'))
                <p class="mb-8 text-xl font-medium text-leaf-900">{{ $post->tr('excerpt') }}</p>
            @endif
            <div class="prose-site text-lg">{!! $post->tr('body') !!}</div>
            <div class="mt-10 flex flex-wrap items-center gap-3 border-t border-leaf-900/10 pt-6">
                <span class="text-sm font-semibold">{{ __('site.common.share') }}:</span>
                <a href="{{ 'https://wa.me/?text='.rawurlencode($post->tr('title').' '.$post->url()) }}" target="_blank" rel="noopener" class="btn-wa !px-4 !py-2 text-xs"><x-icons.whatsapp class="size-4" /> WhatsApp</a>
                <a href="{{ 'https://www.facebook.com/sharer/sharer.php?u='.rawurlencode($post->url()) }}" target="_blank" rel="noopener" class="btn-outline !px-4 !py-2 text-xs">Facebook</a>
            </div>
        </div>
    </article>

    @if($others->isNotEmpty())
        <section class="bg-white py-16">
            <div class="container-site">
                <h2 class="text-2xl font-bold">{{ __('site.news.latest') }}</h2>
                <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach($others as $other)
                        <x-post-card :post="$other" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif
@endsection
