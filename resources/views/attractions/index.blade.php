@extends('layouts.app')

@section('title', __('site.attractions.title'))
@section('description', __('site.attractions.subtitle'))

@section('content')
    <x-page-hero :title="__('site.attractions.title')" :subtitle="__('site.attractions.subtitle')" label="Air Terjun Curug Aman di tengah hutan hijau" :breadcrumbs="[__('site.attractions.title') => null]" />

    <section class="py-12 sm:py-16">
        <div class="container-site">
            <x-category-filter :categories="config('sukajadi.attraction_categories')" :active="$category" route="attractions.index" />
            <div class="mt-8 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                @forelse($attractions as $attraction)
                    <x-attraction-card :attraction="$attraction" />
                @empty
                    <p class="text-leaf-950/60">{{ __('site.common.empty') }}</p>
                @endforelse
            </div>
        </div>
    </section>

    @include('partials.cta')
@endsection
