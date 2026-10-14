@extends('layouts.app')

@section('title', __('site.packages.title'))
@section('description', __('site.packages.subtitle'))

@section('content')
    <x-page-hero :title="__('site.packages.title')" :subtitle="__('site.packages.subtitle')" label="Rombongan study tour di jalan desa Sukajadi" :breadcrumbs="[__('site.packages.title') => null]" />

    <section class="py-12 sm:py-16">
        <div class="container-site">
            <x-category-filter :categories="config('sukajadi.package_categories')" :active="$category" route="packages.index" />

            <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @forelse($packages as $package)
                    <x-package-card :package="$package" />
                @empty
                    <p class="text-leaf-950/60">{{ __('site.common.empty') }}</p>
                @endforelse
            </div>

            <div class="mt-14 grid items-center gap-6 rounded-[2rem] bg-white p-6 ring-1 ring-leaf-900/5 sm:p-10 md:grid-cols-[1fr_auto]">
                <div class="flex items-start gap-4">
                    <span class="flex size-14 shrink-0 items-center justify-center rounded-2xl bg-sun-50 text-sun-600"><x-heroicon-o-adjustments-horizontal class="size-7" /></span>
                    <div>
                        <h2 class="text-2xl font-bold">{{ __('site.packages.custom_cta_title') }}</h2>
                        <p class="mt-1 text-leaf-950/65">{{ __('site.packages.custom_cta_text') }}</p>
                    </div>
                </div>
                <a href="{{ lroute('custom') }}" class="btn-primary">{{ __('site.nav.custom') }} <x-heroicon-m-arrow-right class="size-4" /></a>
            </div>
        </div>
    </section>
@endsection
