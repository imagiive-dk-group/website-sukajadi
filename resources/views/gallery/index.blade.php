@extends('layouts.app')

@section('title', __('site.gallery.title'))
@section('description', __('site.gallery.subtitle'))

@section('content')
    <x-page-hero :title="__('site.gallery.title')" :subtitle="__('site.gallery.subtitle')" label="Kolase foto alam & kegiatan Desa Sukajadi" :breadcrumbs="[__('site.gallery.title') => null]" />

    <section class="py-12 sm:py-16">
        <div class="container-site">
            <x-category-filter :categories="config('sukajadi.album_categories')" :active="$category" route="gallery.index" />
            <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @forelse($albums as $album)
                    <a href="{{ $album->url() }}" class="card-hover group block">
                        <div class="relative overflow-hidden">
                            <x-img :path="$album->coverPhoto?->path" :label="$album->coverPhoto?->placeholder_label ?? $album->tr('title')" :alt="$album->tr('title')" ratio="4/3" class="transition duration-500 group-hover:scale-105" />
                            <span class="chip absolute right-3 bottom-3 bg-black/50 text-white backdrop-blur"><x-heroicon-o-photo class="size-4" /> {{ __('site.common.photos', ['count' => $album->photos_count]) }}</span>
                        </div>
                        <div class="p-5">
                            <span class="text-xs font-semibold text-sun-600">{{ __('site.categories.'.$album->category) }}</span>
                            <h2 class="mt-1 text-lg font-bold group-hover:text-leaf-600">{{ $album->tr('title') }}</h2>
                            @if($album->tr('description'))<p class="mt-1 line-clamp-2 text-sm text-leaf-950/60">{{ $album->tr('description') }}</p>@endif
                        </div>
                    </a>
                @empty
                    <p class="text-leaf-950/60">{{ __('site.common.empty') }}</p>
                @endforelse
            </div>
        </div>
    </section>
@endsection
