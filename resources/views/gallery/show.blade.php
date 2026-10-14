@extends('layouts.app')

@section('title', $album->tr('title').' — '.__('site.gallery.title'))
@section('description', $album->tr('description') ?? __('site.gallery.subtitle'))

@section('content')
    <x-page-hero :title="$album->tr('title')" :subtitle="$album->tr('description')" :image="$album->photos->first()?->path" :label="$album->photos->first()?->placeholder_label"
        :breadcrumbs="[__('site.gallery.title') => lroute('gallery.index'), $album->tr('title') => null]" />

    <section class="py-12 sm:py-16">
        <div class="container-site">
            <x-photo-grid :photos="$album->photos" :gallery="'album-'.$album->id" />
        </div>
    </section>

    @if($albums->isNotEmpty())
        <section class="bg-white py-14">
            <div class="container-site">
                <h2 class="text-2xl font-bold">{{ __('site.gallery.albums') }}</h2>
                <div class="-mx-4 mt-6 flex gap-4 overflow-x-auto px-4 pb-3">
                    @foreach($albums as $other)
                        <a href="{{ $other->url() }}" class="group w-60 shrink-0">
                            <div class="overflow-hidden rounded-2xl">
                                <x-img :path="$other->coverPhoto?->path" :label="$other->coverPhoto?->placeholder_label" ratio="4/3" size="sm" compact class="transition duration-500 group-hover:scale-105" />
                            </div>
                            <p class="mt-2 font-display font-semibold group-hover:text-leaf-600">{{ $other->tr('title') }}</p>
                            <p class="text-xs text-leaf-950/55">{{ __('site.common.photos', ['count' => $other->photos_count]) }}</p>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif
@endsection
