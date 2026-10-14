@extends('layouts.app')

@section('title', __('site.about.title'))
@section('description', setting_tr('about_intro'))

@section('content')
    <x-page-hero :title="__('site.about.title')" :subtitle="__('site.about.subtitle')" label="Warga & Pokdarwis menyambut rombongan di gerbang desa" :breadcrumbs="[__('site.nav.about') => null, __('site.nav.tourism_village') => null]" />

    <section class="py-12 sm:py-20">
        <div class="container-site grid items-center gap-12 lg:grid-cols-2">
            <div>
                <p class="eyebrow"><x-heroicon-s-sparkles class="size-3.5" /> {{ setting_tr('tagline') }}</p>
                <p class="mt-4 font-display text-2xl leading-snug font-semibold text-leaf-900 sm:text-3xl">{{ setting_tr('about_intro') }}</p>
                <div class="prose-site mt-6">{!! setting_tr('about_body') !!}</div>
                <p class="mt-6 font-hand text-3xl text-sun-600">{{ setting_tr('motto') }}</p>
            </div>
            <div class="relative">
                <x-placeholder label="Pokdarwis & warga Desa Sukajadi berfoto bersama" ratio="4/5" class="rounded-[2rem]" />
                <div class="absolute -bottom-6 -left-4 hidden w-44 rotate-[-4deg] rounded-2xl bg-white p-2 shadow-xl sm:block">
                    <x-placeholder label="Anak-anak bermain egrang" ratio="1/1" class="rounded-xl" compact />
                </div>
            </div>
        </div>
    </section>

    <section class="bg-white py-16 sm:py-20">
        <div class="container-site">
            <x-section-heading :title="__('site.about.why_title')" center />
            <div class="mt-10 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                @foreach([1 => 'heroicon-o-hand-raised', 2 => 'heroicon-o-map', 3 => 'heroicon-o-heart', 4 => 'heroicon-o-user-group'] as $i => $icon)
                    <div class="rounded-3xl bg-cream p-6">
                        <x-dynamic-component :component="$icon" class="size-9 text-leaf-600" />
                        <h3 class="mt-4 text-lg font-bold">{{ __('site.about.why_'.$i) }}</h3>
                        <p class="mt-1 text-sm text-leaf-950/65">{{ __('site.about.why_'.$i.'_text') }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="py-16 sm:py-20">
        <div class="container-site">
            <div class="flex flex-wrap items-end justify-between gap-6">
                <x-section-heading :eyebrow="__('site.home.attractions_eyebrow')" :title="__('site.home.attractions_title')" />
                <a href="{{ lroute('attractions.index') }}" class="btn-outline">{{ __('site.common.see_all') }} <x-heroicon-m-arrow-right class="size-4" /></a>
            </div>
            <div class="mt-8 grid grid-cols-2 gap-4 lg:grid-cols-4">
                @foreach($attractions as $attraction)
                    <x-attraction-card :attraction="$attraction" />
                @endforeach
            </div>
        </div>
    </section>

    @include('partials.location')
    @include('partials.cta')
@endsection
