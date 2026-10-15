@extends('layouts.app')

@section('title', $attraction->tr('name'))
@section('description', $attraction->tr('summary'))
@if($attraction->cover_path)
    @section('og_image', \App\Support\Images::url($attraction->cover_path, 'lg'))
@endif

@push('head')
    <script type="application/ld+json">{!! json_encode([
        '@context' => 'https://schema.org',
        '@type' => 'TouristAttraction',
        'name' => $attraction->tr('name'),
        'description' => strip_tags((string) $attraction->tr('summary')),
        'url' => $attraction->url(),
        'address' => ['@type' => 'PostalAddress', 'addressLocality' => 'Desa Sukajadi, Tamansari', 'addressRegion' => 'Kabupaten Bogor, Jawa Barat', 'addressCountry' => 'ID'],
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}</script>
@endpush

@section('content')
    <x-page-hero :title="$attraction->tr('name')" :subtitle="$attraction->tr('summary')" :image="$attraction->cover_path" :label="$attraction->placeholder_label"
        :breadcrumbs="[__('site.attractions.title') => lroute('attractions.index'), $attraction->tr('name') => null]" />

    <section class="py-12 sm:py-16">
        <div class="container-site grid gap-12 lg:grid-cols-3">
            <div class="space-y-10 lg:col-span-2">
                <div class="prose-site text-lg">{!! $attraction->tr('description') !!}</div>
                @if($attraction->photos->isNotEmpty())
                    <div>
                        <h2 class="text-2xl font-bold">{{ __('site.common.gallery') }}</h2>
                        <div class="mt-6"><x-photo-grid :photos="$attraction->photos" :gallery="'attraction-'.$attraction->id" /></div>
                    </div>
                @endif
            </div>
            <aside class="space-y-6 lg:sticky lg:top-28 lg:self-start">
                @if($attraction->packages->isNotEmpty())
                    <div class="card p-6">
                        <h2 class="text-lg font-bold">{{ __('site.attractions.in_packages') }}</h2>
                        <ul class="mt-4 space-y-3">
                            @foreach($attraction->packages as $package)
                                <li>
                                    <a href="{{ $package->url() }}" class="block rounded-2xl bg-leaf-50 p-4 hover:bg-leaf-100">
                                        <span class="block font-display font-bold text-leaf-900">{{ $package->tr('name') }}</span>
                                        <span class="text-sm text-leaf-700">{{ __('site.common.starting_from') }} {{ rupiah($package->startingPrice()) }}{{ __('site.common.per_pax') }}</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                @if($attraction->custom_price)
                    <div class="card p-6">
                        <p class="text-sm text-leaf-950/70">{{ __('site.attractions.custom_price', ['price' => rupiah($attraction->custom_price)]) }}</p>
                        <a href="{{ lroute('custom') }}" class="btn-outline mt-4 w-full">{{ __('site.nav.custom') }}</a>
                    </div>
                @endif
                <a href="{{ wa_track('attraction', $attraction->id, __('site.packages.wa_ask', ['package' => $attraction->tr('name')])) }}" target="_blank" rel="noopener" class="btn-wa w-full !py-3"><x-icons.whatsapp /> {{ __('site.packages.ask_whatsapp') }}</a>
            </aside>
        </div>
    </section>

    @if($others->isNotEmpty())
        <section class="bg-white py-16">
            <div class="container-site">
                <h2 class="text-2xl font-bold">{{ __('site.attractions.title') }}</h2>
                <div class="mt-8 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach($others as $other)
                        <x-attraction-card :attraction="$other" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif
@endsection
