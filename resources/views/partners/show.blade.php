@extends('layouts.app')

@section('title', $partner->name)
@section('description', $partner->tr('summary'))
@if($partner->cover_path)
    @section('og_image', \App\Support\Images::url($partner->cover_path, 'lg'))
@endif

@push('head')
    <script type="application/ld+json">{!! json_encode(array_filter([
        '@context' => 'https://schema.org',
        '@type' => match ($partner->category) { 'penginapan' => 'LodgingBusiness', 'kafe' => 'CafeOrCoffeeShop', default => 'TouristAttraction' },
        'name' => $partner->name,
        'description' => strip_tags((string) $partner->tr('summary')),
        'url' => $partner->url(),
        'address' => $partner->address,
    ]), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}</script>
@endpush

@section('content')
    <x-page-hero :title="$partner->name" :subtitle="$partner->tr('summary')" :image="$partner->cover_path" :label="$partner->placeholder_label"
        :breadcrumbs="[__('site.nav.explore') => lroute('partners.index'), $partner->name => null]">
        <div class="mt-6 flex flex-wrap gap-2">
            <span class="chip bg-white/15 py-1.5 text-white backdrop-blur">{{ __('site.partner_categories.'.$partner->category) }}</span>
            @if($from = $partner->startingPrice())
                <span class="chip bg-sun-500 py-1.5 text-white">{{ __('site.partners.from') }} {{ rupiah($from) }}</span>
            @endif
        </div>
    </x-page-hero>

    <section class="py-12 sm:py-16">
        <div class="container-site grid gap-12 lg:grid-cols-3">
            <div class="space-y-12 lg:col-span-2">
                <div class="prose-site text-lg">{!! $partner->tr('description') !!}</div>

                @if($partner->highlightList())
                    <div>
                        <h2 class="text-2xl font-bold">{{ __('site.partners.highlights') }}</h2>
                        <ul class="mt-4 flex flex-wrap gap-2">
                            @foreach($partner->highlightList() as $item)
                                <li class="chip bg-white px-4 py-2 text-sm text-leaf-800 ring-1 ring-leaf-900/10"><x-heroicon-s-check-circle class="size-4 text-leaf-500" /> {{ $item }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if($partner->photos->isNotEmpty())
                    <div>
                        <h2 class="text-2xl font-bold">{{ __('site.common.gallery') }}</h2>
                        <div class="mt-6"><x-photo-grid :photos="$partner->photos" :gallery="'partner-'.$partner->id" /></div>
                    </div>
                @endif

                @if($partner->offers->isNotEmpty())
                    <div id="pilihan">
                        <h2 class="text-2xl font-bold">{{ __('site.partners.offers') }}</h2>
                        <div class="mt-6 space-y-4">
                            @foreach($partner->offers as $offer)
                                <div class="card grid gap-4 p-4 sm:grid-cols-[10rem_1fr_auto] sm:items-center">
                                    <div class="overflow-hidden rounded-2xl">
                                        <x-img :path="$offer->image_path" :label="$offer->placeholder_label" ratio="4/3" size="sm" compact />
                                    </div>
                                    <div>
                                        <h3 class="font-display text-lg font-bold">{{ $offer->tr('name') }}</h3>
                                        @if($offer->tr('description'))<p class="mt-1 text-sm text-leaf-950/65">{{ $offer->tr('description') }}</p>@endif
                                        @if($offer->min_qty > 1)<p class="mt-1 text-xs text-leaf-950/55">{{ __('site.partners.min_qty', ['count' => $offer->min_qty]) }}</p>@endif
                                    </div>
                                    <div class="flex items-center justify-between gap-4 sm:flex-col sm:items-end">
                                        <p class="font-display text-xl font-extrabold text-leaf-700">{{ rupiah($offer->price) }}<span class="text-sm font-medium text-leaf-950/50">{{ $offer->unitLabel() }}</span></p>
                                        <a href="{{ lroute('booking.create', ['mitra' => $partner->slugFor(), 'offer' => $offer->id]) }}" class="btn-primary !px-5 !py-2">{{ __('site.partners.book') }}</a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <aside class="space-y-6 lg:sticky lg:top-28 lg:self-start">
                <div class="card p-6">
                    @if($from)
                        <p class="text-sm text-leaf-950/55">{{ __('site.partners.from') }}</p>
                        <p class="font-display text-3xl font-extrabold text-leaf-700">{{ rupiah($from) }}</p>
                    @endif
                    <a href="#pilihan" class="btn-primary mt-5 w-full !py-3">{{ __('site.partners.book') }}</a>
                    <a href="{{ wa_track('partner', $partner->id, __('site.partners.wa_ask', ['name' => $partner->name])) }}" target="_blank" rel="noopener" class="btn-wa mt-3 w-full !py-3"><x-icons.whatsapp /> {{ __('site.partners.ask') }}</a>
                    <p class="mt-4 text-xs text-leaf-950/55">{{ __('site.partners.via_us') }}</p>
                </div>
                <div class="card space-y-3 p-6 text-sm">
                    @if($partner->address)
                        <p class="flex gap-3"><x-heroicon-o-map-pin class="size-5 shrink-0 text-leaf-600" /> {{ $partner->address }}</p>
                    @endif
                    @if($partner->tr('opening_hours'))
                        <p class="flex gap-3"><x-heroicon-o-clock class="size-5 shrink-0 text-leaf-600" /> {{ $partner->tr('opening_hours') }}</p>
                    @endif
                    @if($partner->maps_url)
                        <a href="{{ $partner->maps_url }}" target="_blank" rel="noopener" class="btn-outline w-full"><x-heroicon-o-map class="size-5" /> {{ __('site.common.open_maps') }}</a>
                    @endif
                </div>
            </aside>
        </div>
    </section>

    @if($others->isNotEmpty())
        <section class="bg-white py-16">
            <div class="container-site">
                <h2 class="text-2xl font-bold">{{ __('site.partners.other') }}</h2>
                <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach($others as $other)
                        <x-partner-card :partner="$other" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif
@endsection
