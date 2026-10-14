@extends('layouts.app')

@section('title', $umkm->name)
@section('description', $umkm->tr('summary'))
@if($umkm->cover_path)
    @section('og_image', \App\Support\Images::url($umkm->cover_path, 'lg'))
@endif

@push('head')
    <script type="application/ld+json">{!! json_encode(array_filter([
        '@context' => 'https://schema.org',
        '@type' => 'LocalBusiness',
        'name' => $umkm->name,
        'description' => strip_tags((string) $umkm->tr('summary')),
        'url' => $umkm->url(),
        'telephone' => $umkm->hasPublicContact() ? $umkm->whatsapp : null,
        'address' => ['@type' => 'PostalAddress', 'streetAddress' => $umkm->address, 'addressLocality' => 'Desa Sukajadi, Tamansari', 'addressRegion' => 'Kabupaten Bogor', 'addressCountry' => 'ID'],
    ]), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}</script>
@endpush

@section('content')
    <x-page-hero :title="$umkm->name" :subtitle="$umkm->tr('summary')" :image="$umkm->cover_path" :label="$umkm->placeholder_label"
        :breadcrumbs="[__('site.umkm.title') => lroute('umkm.index'), $umkm->name => null]">
        <div class="mt-6 flex flex-wrap gap-2">
            <span class="chip bg-white/15 py-1.5 text-white backdrop-blur">{{ __('site.umkm_categories.'.$umkm->category) }}</span>
        </div>
    </x-page-hero>

    <section class="py-12 sm:py-16">
        <div class="container-site grid gap-12 lg:grid-cols-3">
            <div class="space-y-12 lg:col-span-2">
                <div class="prose-site text-lg">{!! $umkm->tr('description') !!}</div>

                @if($umkm->products->isNotEmpty())
                    <div>
                        <h2 class="text-2xl font-bold">{{ __('site.umkm.products') }}</h2>
                        <div class="mt-6 grid gap-5 sm:grid-cols-2">
                            @foreach($umkm->products as $product)
                                @include('umkm.product-card', ['product' => $product, 'umkm' => $umkm, 'showUmkm' => false])
                            @endforeach
                        </div>
                    </div>
                @endif

                @if($umkm->photos->isNotEmpty())
                    <div>
                        <h2 class="text-2xl font-bold">{{ __('site.common.gallery') }}</h2>
                        <div class="mt-6"><x-photo-grid :photos="$umkm->photos" :gallery="'umkm-'.$umkm->id" /></div>
                    </div>
                @endif
            </div>

            <aside class="space-y-6 lg:sticky lg:top-28 lg:self-start">
                <div class="card p-6">
                    <h2 class="text-lg font-bold">{{ __('site.umkm.contact') }}</h2>
                    <dl class="mt-4 space-y-3 text-sm">
                        @if($umkm->owner_name)
                            <div class="flex gap-3"><x-heroicon-o-user class="size-5 shrink-0 text-leaf-600" /><div><dt class="text-xs text-leaf-950/50">{{ __('site.umkm.owner') }}</dt><dd class="font-medium">{{ $umkm->owner_name }}</dd></div></div>
                        @endif
                        @if($umkm->hasPublicContact())
                            <div class="flex gap-3"><x-icons.whatsapp class="size-5 shrink-0 text-[#25D366]" /><div><dt class="text-xs text-leaf-950/50">WhatsApp</dt><dd class="font-medium">{{ $umkm->whatsapp }}</dd></div></div>
                        @endif
                        @if($umkm->show_contact && $umkm->instagram)
                            <div class="flex gap-3"><x-icons.instagram class="size-5 shrink-0 text-sun-600" /><div><dt class="text-xs text-leaf-950/50">{{ __('site.umkm.instagram') }}</dt><dd><a href="{{ instagram_url($umkm->instagram) }}" target="_blank" rel="noopener" class="font-medium hover:underline">{{ '@'.ltrim($umkm->instagram, '@') }}</a></dd></div></div>
                        @endif
                        @if($umkm->address)
                            <div class="flex gap-3"><x-heroicon-o-map-pin class="size-5 shrink-0 text-leaf-600" /><div><dt class="text-xs text-leaf-950/50">{{ __('site.umkm.address') }}</dt><dd class="font-medium">{{ $umkm->address }}</dd></div></div>
                        @endif
                        @if($umkm->tr('opening_hours'))
                            <div class="flex gap-3"><x-heroicon-o-clock class="size-5 shrink-0 text-leaf-600" /><div><dt class="text-xs text-leaf-950/50">{{ __('site.umkm.hours') }}</dt><dd class="font-medium">{{ $umkm->tr('opening_hours') }}</dd></div></div>
                        @endif
                    </dl>
                    @unless($umkm->hasPublicContact())
                        <p class="mt-4 rounded-xl bg-leaf-50 p-3 text-xs text-leaf-800">{{ __('site.umkm.contact_via_admin') }}</p>
                    @endunless
                    <a href="{{ $umkm->whatsappLink() }}" target="_blank" rel="noopener" class="btn-wa mt-5 w-full !py-3"><x-icons.whatsapp /> {{ __('site.umkm.whatsapp') }}</a>
                    @if($umkm->maps_url)
                        <a href="{{ $umkm->maps_url }}" target="_blank" rel="noopener" class="btn-outline mt-3 w-full"><x-heroicon-o-map class="size-5" /> {{ __('site.common.open_maps') }}</a>
                    @endif
                </div>

                <div class="card p-6">
                    <h2 class="text-lg font-bold">{{ __('site.umkm.services') }}</h2>
                    <ul class="mt-4 space-y-2 text-sm">
                        <li class="flex gap-2">@if($umkm->accepts_orders)<x-heroicon-s-check-circle class="size-5 text-leaf-500" />@else<x-heroicon-s-x-circle class="size-5 text-leaf-950/25" />@endif {{ __('site.umkm.accepts_orders') }}</li>
                        <li class="flex gap-2">@if($umkm->accepts_visits)<x-heroicon-s-check-circle class="size-5 text-leaf-500" />@else<x-heroicon-s-x-circle class="size-5 text-leaf-950/25" />@endif {{ __('site.umkm.accepts_visits') }}</li>
                    </ul>
                </div>

                @if($umkm->packages->isNotEmpty())
                    <div class="card p-6">
                        <h2 class="text-lg font-bold">{{ __('site.attractions.in_packages') }}</h2>
                        <ul class="mt-4 space-y-3">
                            @foreach($umkm->packages as $package)
                                <li><a href="{{ $package->url() }}" class="block rounded-2xl bg-leaf-50 p-4 font-display font-bold text-leaf-900 hover:bg-leaf-100">{{ $package->tr('name') }}</a></li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </aside>
        </div>
    </section>

    @if($others->isNotEmpty())
        <section class="bg-white py-16">
            <div class="container-site">
                <h2 class="text-2xl font-bold">{{ __('site.umkm.title') }}</h2>
                <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach($others as $other)
                        <x-umkm-card :umkm="$other" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif
@endsection
