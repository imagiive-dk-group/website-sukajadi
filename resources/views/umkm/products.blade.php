@extends('layouts.app')

@section('title', __('site.umkm.products_title'))
@section('description', __('site.umkm.products_subtitle'))

@section('content')
    <x-page-hero :title="__('site.umkm.products_title')" :subtitle="__('site.umkm.products_subtitle')" label="Aneka oleh-oleh khas Sukajadi tertata di meja" :breadcrumbs="[__('site.umkm.title') => lroute('umkm.index'), __('site.nav.products') => null]" />

    <section class="py-12 sm:py-16">
        <div class="container-site">
            <x-category-filter :categories="config('sukajadi.umkm_categories')" :active="$category" route="products" labels="site.umkm_categories" />
            <div class="mt-8 grid grid-cols-2 gap-4 sm:gap-6 lg:grid-cols-4">
                @forelse($products as $product)
                    @include('umkm.product-card', ['product' => $product, 'umkm' => $product->umkm])
                @empty
                    <p class="col-span-full text-leaf-950/60">{{ __('site.common.empty') }}</p>
                @endforelse
            </div>
        </div>
    </section>
@endsection
