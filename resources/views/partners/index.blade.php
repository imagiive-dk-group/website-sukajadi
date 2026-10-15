@extends('layouts.app')

@section('title', __('site.partners.title'))
@section('description', __('site.partners.subtitle'))

@section('content')
    <x-page-hero :title="__('site.partners.title')" :subtitle="__('site.partners.subtitle')" label="Panorama kawasan Tamansari dari ketinggian" :breadcrumbs="[__('site.nav.explore') => null]" />

    <section class="py-12 sm:py-16">
        <div class="container-site">
            <x-category-filter :categories="config('sukajadi.partner_categories')" :active="$category" route="partners.index" labels="site.partner_categories" />
            <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @forelse($partners as $partner)
                    <x-partner-card :partner="$partner" />
                @empty
                    <p class="text-leaf-950/60">{{ __('site.common.empty') }}</p>
                @endforelse
            </div>
            <p class="mt-10 flex items-start gap-3 rounded-2xl bg-white p-4 text-sm text-leaf-950/70 ring-1 ring-leaf-900/5">
                <x-heroicon-o-information-circle class="size-5 shrink-0 text-leaf-600" /> {{ __('site.partners.via_us') }}
            </p>
        </div>
    </section>
@endsection
