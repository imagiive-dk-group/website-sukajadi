@extends('layouts.app')

@section('title', $package->tr('name'))
@section('description', $package->tr('summary'))
@if($package->cover_path)
    @section('og_image', \App\Support\Images::url($package->cover_path, 'lg'))
@endif

@push('head')
    <script type="application/ld+json">{!! json_encode([
        '@context' => 'https://schema.org',
        '@type' => 'TouristTrip',
        'name' => $package->tr('name'),
        'description' => strip_tags((string) $package->tr('summary')),
        'url' => $package->url(),
        'touristType' => collect($package->target_groups ?? [])->map(fn ($g) => __('site.group_types.'.$g))->values(),
        'provider' => ['@type' => 'Organization', 'name' => setting('site_name'), 'url' => lroute('home')],
        'offers' => $package->tiers->map(fn ($tier) => [
            '@type' => 'Offer',
            'name' => $tier->tr('name'),
            'price' => $tier->price,
            'priceCurrency' => 'IDR',
            'url' => lroute('booking.create', ['paket' => $package->slugFor(), 'tier' => $tier->id]),
        ])->values(),
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}</script>
@endpush

@section('content')
    <x-page-hero :title="$package->tr('name')" :subtitle="$package->tr('summary')" :image="$package->cover_path" :label="$package->placeholder_label"
        :breadcrumbs="[__('site.packages.title') => lroute('packages.index'), $package->tr('name') => null]">
        <div class="mt-6 flex flex-wrap gap-2 text-sm">
            <span class="chip bg-white/15 py-1.5 text-white backdrop-blur"><x-heroicon-o-tag class="size-4" />{{ __('site.categories.'.$package->category) }}</span>
            @if($package->tr('duration'))
                <span class="chip bg-white/15 py-1.5 text-white backdrop-blur"><x-heroicon-o-clock class="size-4" />{{ $package->tr('duration') }}</span>
            @endif
            <span class="chip bg-white/15 py-1.5 text-white backdrop-blur"><x-heroicon-o-user-group class="size-4" />{{ __('site.common.min_pax', ['count' => $package->minPax()]) }}</span>
            @if($from = $package->startingPrice())
                <span class="chip bg-sun-500 py-1.5 text-white">{{ __('site.common.starting_from') }} {{ rupiah($from) }}{{ __('site.common.per_pax') }}</span>
            @endif
        </div>
    </x-page-hero>

    <section class="py-12 sm:py-16">
        <div class="container-site grid gap-12 lg:grid-cols-3">
            <div class="space-y-12 lg:col-span-2">
                <div class="prose-site text-lg">{!! $package->tr('description') !!}</div>

                {{-- Pilihan tingkat paket --}}
                <div id="pilihan">
                    <h2 class="text-2xl font-bold">{{ __('site.packages.choose_tier') }}</h2>
                    <div @class(['mt-6 grid gap-5', 'md:grid-cols-3' => $package->tiers->count() >= 3, 'md:grid-cols-2' => $package->tiers->count() === 2])>
                        @foreach($package->tiers as $tier)
                            @php($featured = filled($tier->tr('badge')))
                            <div @class(['relative flex flex-col rounded-3xl p-6', 'bg-leaf-700 text-white shadow-xl shadow-leaf-900/20' => $featured, 'bg-white ring-1 ring-leaf-900/10' => ! $featured])>
                                @if($featured)
                                    <span class="chip absolute -top-3 right-5 bg-sun-500 text-white shadow">{{ $tier->tr('badge') }}</span>
                                @endif
                                <h3 @class(['text-xl font-extrabold', '!text-white' => $featured])>{{ $tier->tr('name') }}</h3>
                                @if($tier->tr('duration'))
                                    <p @class(['text-sm', 'text-white/75' => $featured, 'text-leaf-950/55' => ! $featured])>{{ __('site.common.duration') }}: {{ $tier->tr('duration') }}</p>
                                @endif
                                <ul class="mt-5 flex-1 space-y-2.5 text-sm">
                                    @foreach($tier->inclusionList() as $item)
                                        <li class="flex gap-2"><x-heroicon-s-check-circle @class(['size-5 shrink-0', 'text-sun-300' => $featured, 'text-leaf-500' => ! $featured]) /> {{ $item }}</li>
                                    @endforeach
                                </ul>
                                <div @class(['mt-6 border-t pt-5', 'border-white/15' => $featured, 'border-leaf-900/10' => ! $featured])>
                                    <p @class(['text-xs', 'text-white/70' => $featured, 'text-leaf-950/55' => ! $featured])>{{ __('site.common.starting_from') }}</p>
                                    <p class="font-display text-3xl font-extrabold">{{ rupiah($tier->price) }}<span class="text-sm font-medium opacity-60">{{ __('site.common.per_pax') }}</span></p>
                                    <p @class(['mt-1 text-xs', 'text-white/70' => $featured, 'text-leaf-950/55' => ! $featured])>{{ __('site.common.min_pax', ['count' => $tier->min_pax]) }}</p>
                                    <a href="{{ lroute('booking.create', ['paket' => $package->slugFor(), 'tier' => $tier->id]) }}" @class(['mt-4 w-full', 'btn-primary' => true])>{{ __('site.packages.choose_this') }}</a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Galeri paket --}}
                @if($package->photos->isNotEmpty())
                    <div>
                        <h2 class="text-2xl font-bold">{{ __('site.common.gallery') }}</h2>
                        <div class="mt-6"><x-photo-grid :photos="$package->photos" :gallery="'package-'.$package->id" /></div>
                    </div>
                @endif

                {{-- Rundown --}}
                @if($package->itinerary)
                    <div>
                        <h2 class="text-2xl font-bold">{{ __('site.packages.itinerary') }}</h2>
                        <p class="mt-1 text-sm text-leaf-950/55">{{ __('site.packages.itinerary_note') }}</p>
                        <ol class="relative mt-6 space-y-5 border-l-2 border-dashed border-leaf-300 pl-6">
                            @foreach($package->itinerary as $row)
                                <li class="relative">
                                    <span class="absolute top-1 -left-[33px] size-4 rounded-full border-4 border-cream bg-sun-500"></span>
                                    <p class="font-display text-sm font-bold text-sun-600">{{ $row['time'] ?? '' }}</p>
                                    <p class="font-medium">{{ localized($row['activity'] ?? null) }}</p>
                                </li>
                            @endforeach
                        </ol>
                    </div>
                @endif

                {{-- Aktivitas --}}
                @if($package->attractions->isNotEmpty())
                    <div>
                        <h2 class="text-2xl font-bold">{{ __('site.packages.activities') }}</h2>
                        <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                            @foreach($package->attractions as $attraction)
                                <x-attraction-card :attraction="$attraction" />
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            {{-- Sidebar --}}
            <aside class="space-y-6 lg:sticky lg:top-28 lg:self-start">
                <div class="card p-6">
                    <p class="text-sm text-leaf-950/55">{{ __('site.common.starting_from') }}</p>
                    <p class="font-display text-3xl font-extrabold text-leaf-700">{{ rupiah($package->startingPrice()) }}<span class="text-base font-medium text-leaf-950/50">{{ __('site.common.per_pax') }}</span></p>
                    <a href="{{ lroute('booking.create', ['paket' => $package->slugFor()]) }}" class="btn-primary mt-5 w-full !py-3">{{ __('site.nav.book_now') }}</a>
                    <a href="{{ wa_track('package', $package->id, __('site.packages.wa_ask', ['package' => $package->tr('name')])) }}" target="_blank" rel="noopener" class="btn-wa mt-3 w-full !py-3"><x-icons.whatsapp /> {{ __('site.packages.ask_whatsapp') }}</a>
                </div>

                @if($package->facilities)
                    <div class="card p-6">
                        <h2 class="text-lg font-bold">{{ __('site.packages.facilities') }}</h2>
                        <ul class="mt-4 space-y-2.5 text-sm">
                            @foreach($package->facilities as $facility)
                                <li class="flex gap-2"><x-heroicon-s-check-circle class="size-5 text-leaf-500" /> {{ __('site.facilities.'.$facility) }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if($package->target_groups)
                    <div class="card p-6">
                        <h2 class="text-lg font-bold">{{ __('site.packages.audience') }}</h2>
                        <div class="mt-4 flex flex-wrap gap-2">
                            @foreach($package->target_groups as $group)
                                <span class="chip bg-leaf-50 text-leaf-800">{{ __('site.group_types.'.$group) }}</span>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if($package->umkms->isNotEmpty())
                    <div class="card p-6">
                        <h2 class="text-lg font-bold">{{ __('site.packages.umkm') }}</h2>
                        <ul class="mt-4 space-y-3">
                            @foreach($package->umkms as $umkm)
                                <li>
                                    <a href="{{ $umkm->url() }}" class="flex items-center gap-3 rounded-xl p-1 hover:bg-leaf-50">
                                        <span class="size-12 shrink-0 overflow-hidden rounded-xl"><x-img :path="$umkm->cover_path" :label="$umkm->placeholder_label" ratio="1/1" size="sm" compact /></span>
                                        <span class="text-sm font-semibold">{{ $umkm->name }}</span>
                                    </a>
                                </li>
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
                <h2 class="text-2xl font-bold">{{ __('site.common.related_packages') }}</h2>
                <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach($others as $other)
                        <x-package-card :package="$other" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- Bar booking di HP --}}
    <div class="fixed inset-x-0 bottom-0 z-30 border-t border-leaf-900/10 bg-white/95 p-3 backdrop-blur lg:hidden">
        <div class="flex items-center justify-between gap-3 pr-16">
            <div>
                <p class="text-[11px] text-leaf-950/55">{{ __('site.common.starting_from') }}</p>
                <p class="font-display text-lg font-bold text-leaf-700">{{ rupiah($package->startingPrice()) }}<span class="text-xs text-leaf-950/50">{{ __('site.common.per_pax') }}</span></p>
            </div>
            <a href="{{ lroute('booking.create', ['paket' => $package->slugFor()]) }}" class="btn-primary">{{ __('site.nav.book_now') }}</a>
        </div>
    </div>
@endsection
