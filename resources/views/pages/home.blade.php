@extends('layouts.app')

@section('description', setting_tr('hero_subtitle'))

@section('content')
    {{-- 1. HERO BERGERAK: video atau slideshow foto, tanpa jualan --}}
    <section class="relative isolate flex min-h-[100svh] items-end overflow-hidden bg-leaf-950 pt-28 pb-14 sm:pb-20">
        <div class="absolute inset-0 -z-10">
            @include('partials.hero-media')
            <div class="absolute inset-0 z-20 bg-gradient-to-t from-leaf-950/90 via-leaf-950/35 to-leaf-950/30"></div>
        </div>
        <div class="container-site">
            <p class="inline-flex items-center gap-2 rounded-full bg-white/15 px-4 py-1.5 font-display text-xs font-semibold tracking-[0.2em] text-white uppercase backdrop-blur">
                <x-heroicon-s-map-pin class="size-4 text-sun-400" /> Desa Wisata · Tamansari, Bogor
            </p>
            <h1 class="mt-5 text-6xl leading-[0.95] font-extrabold !text-white sm:text-8xl lg:text-9xl">Sukajadi</h1>
            <p class="mt-3 font-hand text-3xl text-sun-300 sm:text-4xl">{{ setting_tr('tagline') }}</p>
            <p class="mt-4 max-w-xl text-lg text-white/85">{{ setting_tr('hero_subtitle') }}</p>
            <div class="mt-8 flex flex-wrap gap-3">
                <a href="#cerita" class="btn-primary !px-7 !py-3.5 !text-base">{{ __('site.home.explore_cta') }} <x-heroicon-m-arrow-down class="size-5" /></a>
                <a href="{{ lroute('gallery.index') }}" class="btn-ghost-light !px-7 !py-3.5 !text-base"><x-heroicon-o-photo class="size-5" /> {{ __('site.home.gallery_cta') }}</a>
            </div>
        </div>
        <a href="#cerita" class="scroll-cue absolute bottom-5 left-1/2 hidden -translate-x-1/2 text-white/70 sm:block" aria-label="{{ __('site.home.explore_cta') }}">
            <x-heroicon-o-chevron-double-down class="size-7" />
        </a>
    </section>

    {{-- 2. CERITA DESA --}}
    <section id="cerita" class="scroll-mt-16 py-16 sm:py-24">
        <div class="container-site grid items-center gap-12 lg:grid-cols-2">
            <div>
                <p class="eyebrow"><x-heroicon-s-sparkles class="size-3.5" /> {{ __('site.home.story_eyebrow') }}</p>
                <h2 class="mt-3 text-4xl leading-tight font-extrabold sm:text-5xl">{{ setting_tr('story_title') }}</h2>
                <div class="prose-site mt-6 text-lg">{!! setting_tr('story_body') !!}</div>
                <div class="mt-6 flex flex-wrap gap-3">
                    <a href="{{ lroute('profile') }}" class="btn-outline">{{ __('site.home.story_cta') }} <x-heroicon-m-arrow-right class="size-4" /></a>
                    <a href="{{ lroute('about') }}" class="btn-outline">{{ __('site.nav.tourism_village') }}</a>
                </div>
            </div>
            <div class="grid grid-cols-6 grid-rows-6 gap-3 sm:gap-4" style="aspect-ratio: 1/1">
                @foreach($storyPhotos as $i => $photo)
                    <div @class([
                        'relative overflow-hidden rounded-3xl',
                        'col-span-4 row-span-4' => $i === 0,
                        'col-span-2 row-span-3' => in_array($i, [1, 2]),
                        'col-span-2 row-span-2' => $i >= 3,
                    ])>
                        <x-img :path="$photo->path" :label="$photo->placeholder_label" :alt="$photo->tr('caption') ?? $photo->placeholder_label" fill :compact="$i > 0" size="md" sizes="(min-width: 1024px) 40vw, 90vw" />
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Fakta singkat --}}
        <div class="container-site mt-14">
            <dl class="grid grid-cols-2 gap-px overflow-hidden rounded-3xl bg-leaf-900/10 ring-1 ring-leaf-900/10 lg:grid-cols-4">
                @foreach([
                    [$counts['attractions'], __('site.home.facts_attractions')],
                    [$counts['umkm'], __('site.home.facts_umkm')],
                    [$counts['partners'], __('site.home.facts_partners')],
                    [$counts['packages'], __('site.home.facts_packages')],
                ] as [$value, $label])
                    <div class="flex flex-col-reverse justify-end bg-cream p-5 sm:p-7">
                        <dt class="mt-1 text-sm text-leaf-950/65">{{ $label }}</dt>
                        <dd class="font-display text-4xl font-extrabold text-leaf-700 sm:text-5xl">{{ $value }}</dd>
                    </div>
                @endforeach
            </dl>
        </div>
    </section>

    {{-- 3. PITA FOTO --}}
    <section class="pb-16 sm:pb-24">
        <x-photo-marquee :photos="$marquee" />
    </section>

    {{-- 4. ADA APA DI SUKAJADI --}}
    <section class="bg-leaf-900 py-16 text-white sm:py-24">
        <div class="container-site">
            <x-section-heading :eyebrow="__('site.home.whats_eyebrow')" :title="__('site.home.whats_title')" :subtitle="__('site.home.whats_subtitle')" light />
            <div class="mt-10 grid auto-rows-[220px] grid-cols-1 gap-4 sm:grid-cols-2 sm:auto-rows-[240px] lg:grid-cols-4">
                @foreach([
                    ['alam', __('site.categories.alam'), __('site.home.whats_alam'), lroute('attractions.index', ['kategori' => 'alam']), 'Air terjun & perbukitan Sukajadi', 'lg:col-span-2 lg:row-span-2'],
                    ['tani', __('site.home.tani'), __('site.home.whats_tani'), lroute('attractions.index', ['kategori' => 'edukasi']), 'Petani & pengunjung di sawah', ''],
                    ['budaya', __('site.categories.budaya'), __('site.home.whats_budaya'), lroute('attractions.index', ['kategori' => 'budaya']), 'Permainan tradisional Sunda', ''],
                    ['kuliner', __('site.categories.kuliner'), __('site.home.whats_kuliner'), lroute('umkm.index'), 'Nasi liwet & produk UMKM', 'lg:col-span-2'],
                    ['inap', __('site.partner_categories.penginapan'), __('site.home.whats_inap'), lroute('partners.index', ['kategori' => 'penginapan']), 'Vila & homestay di Sukajadi', 'lg:col-span-2'],
                    ['kafe', __('site.partner_categories.kafe'), __('site.home.whats_kafe'), lroute('partners.index', ['kategori' => 'kafe']), 'Kafe dengan pemandangan sawah', 'lg:col-span-2'],
                ] as [$key, $title, $text, $url, $label, $span])
                    <a href="{{ $url }}" class="group relative block overflow-hidden rounded-3xl {{ $span }}">
                        <x-placeholder :label="$label" fill dark corner class="transition duration-700 group-hover:scale-105" />
                        <div class="absolute inset-0 bg-gradient-to-t from-leaf-950/90 via-leaf-950/30 to-transparent"></div>
                        <div class="absolute inset-x-0 bottom-0 p-5 sm:p-6">
                            <h3 class="text-xl font-bold !text-white sm:text-2xl">{{ $title }}</h3>
                            <p class="mt-1 max-w-sm text-sm text-white/80">{{ $text }}</p>
                            <span class="mt-3 inline-flex items-center gap-1 text-sm font-semibold text-sun-300">{{ __('site.common.see_all') }} <x-heroicon-m-arrow-right class="size-4 transition group-hover:translate-x-1" /></span>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    {{-- 5. DESTINASI DI DALAM DESA --}}
    <section class="py-16 sm:py-24">
        <div class="container-site">
            <div class="flex flex-wrap items-end justify-between gap-6">
                <x-section-heading :eyebrow="__('site.home.village_eyebrow')" :title="__('site.home.village_title')" />
                <a href="{{ lroute('attractions.index') }}" class="btn-outline">{{ __('site.common.see_all') }} <x-heroicon-m-arrow-right class="size-4" /></a>
            </div>
        </div>
        <div class="container-site">
            <div class="-mx-4 mt-10 flex snap-x snap-mandatory scroll-px-4 gap-4 overflow-x-auto px-4 pb-4 sm:-mx-6 sm:scroll-px-6 sm:px-6 lg:-mx-8 lg:scroll-px-8 lg:px-8">
                @foreach($attractions as $attraction)
                    <div class="w-[72%] shrink-0 snap-start sm:w-[40%] lg:w-[23%]">
                        <x-attraction-card :attraction="$attraction" />
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- 6. GALERI --}}
    <section class="bg-white py-16 sm:py-24">
        <div class="container-site">
            <div class="flex flex-wrap items-end justify-between gap-6">
                <x-section-heading :eyebrow="__('site.home.gallery_eyebrow')" :title="__('site.home.gallery_title')" />
                <a href="{{ lroute('gallery.index') }}" class="btn-outline">{{ __('site.common.see_all') }} <x-heroicon-m-arrow-right class="size-4" /></a>
            </div>
            <div class="mt-10 grid grid-flow-dense auto-rows-[150px] grid-cols-2 gap-3 sm:auto-rows-[190px] sm:gap-4 md:grid-cols-4">
                @foreach($photos as $photo)
                    @php($full = \App\Support\Images::url($photo->path, 'lg') ?? route('placeholder', ['w' => 1600, 'h' => 1066, 'label' => $photo->placeholder_label]))
                    <a href="{{ $full }}" data-gallery="home" data-type="image" data-description="{{ $photo->tr('caption') ?? $photo->placeholder_label }}"
                       @class(['glightbox group relative overflow-hidden rounded-2xl', 'col-span-2 row-span-2' => $loop->index === 0, 'col-span-2' => in_array($loop->index, [5, 7, 9])])>
                        <x-img :path="$photo->path" :label="$photo->placeholder_label" fill :compact="! in_array($loop->index, [0, 5, 7, 9])" sizes="(min-width: 768px) 50vw, 100vw" class="transition duration-500 group-hover:scale-105" />
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    {{-- 7. JELAJAH TAMANSARI (mitra) --}}
    @if($partners->isNotEmpty())
        <section class="py-16 sm:py-24">
            <div class="container-site">
                <div class="flex flex-wrap items-end justify-between gap-6">
                    <x-section-heading :eyebrow="__('site.home.explore_eyebrow')" :title="__('site.home.explore_title')" :subtitle="__('site.home.explore_subtitle')" />
                    <a href="{{ lroute('partners.index') }}" class="btn-outline">{{ __('site.common.see_all') }} <x-heroicon-m-arrow-right class="size-4" /></a>
                </div>
                <div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach($partners->take(4) as $partner)
                        <x-partner-card :partner="$partner" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- 8. UMKM --}}
    @if($umkms->isNotEmpty())
        <section class="bg-cream-dark/60 py-16 sm:py-24">
            <div class="container-site">
                <div class="flex flex-wrap items-end justify-between gap-6">
                    <x-section-heading :eyebrow="__('site.home.umkm_eyebrow')" :title="__('site.home.umkm_title2')" :subtitle="__('site.home.umkm_subtitle')" />
                    <a href="{{ lroute('umkm.index') }}" class="btn-outline">{{ __('site.common.see_all') }} <x-heroicon-m-arrow-right class="size-4" /></a>
                </div>
                <div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach($umkms as $umkm)
                        <x-umkm-card :umkm="$umkm" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- 9. PENGGIAT DESA WISATA --}}
    <section class="py-16 sm:py-24">
        <div class="container-site grid items-center gap-10 lg:grid-cols-2">
            <div>
                <x-section-heading :eyebrow="__('site.home.team_eyebrow')" :title="__('site.home.team_title')" />
                <p class="mt-4 max-w-xl text-lg text-leaf-950/70">{{ __('site.home.team_text') }}</p>
                <a href="{{ lroute('team') }}" class="btn-outline mt-6">{{ __('site.home.team_cta') }} <x-heroicon-m-arrow-right class="size-4" /></a>
            </div>
            <div class="grid grid-cols-3 gap-3 sm:gap-4">
                @foreach($team as $member)
                    <figure class="overflow-hidden rounded-2xl bg-white ring-1 ring-leaf-900/5">
                        <x-img :path="$member->photo_path" :label="$member->tr('position')" :alt="$member->name" ratio="1/1" size="sm" compact />
                        <figcaption class="p-3 text-xs"><span class="block font-semibold text-leaf-900">{{ $member->name }}</span><span class="text-leaf-950/55">{{ $member->tr('position') }}</span></figcaption>
                    </figure>
                @endforeach
            </div>
        </div>
    </section>

    {{-- 10. BERKUNJUNG BERSAMA ROMBONGAN (paket, ditaruh belakangan) --}}
    <section class="bg-leaf-800 py-16 text-white sm:py-24">
        <div class="container-site">
            <div class="flex flex-wrap items-end justify-between gap-6">
                <x-section-heading :eyebrow="__('site.home.visit_eyebrow')" :title="__('site.home.visit_title')" :subtitle="__('site.home.visit_subtitle')" light />
                <a href="{{ lroute('packages.index') }}" class="btn-ghost-light">{{ __('site.common.see_all') }} <x-heroicon-m-arrow-right class="size-4" /></a>
            </div>
            <div class="mt-10 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($packages as $package)
                    <a href="{{ $package->url() }}" class="group flex gap-4 rounded-3xl bg-white/5 p-3 ring-1 ring-white/10 transition hover:bg-white/10">
                        <span class="relative w-28 shrink-0 overflow-hidden rounded-2xl sm:w-32">
                            <x-img :path="$package->cover_path" :label="$package->placeholder_label" fill size="sm" compact dark />
                        </span>
                        <span class="flex flex-col py-1">
                            <span class="text-xs font-semibold text-sun-300">{{ __('site.categories.'.$package->category) }} · {{ $package->tr('duration') }}</span>
                            <span class="mt-1 font-display font-bold leading-snug text-white">{{ $package->tr('name') }}</span>
                            <span class="mt-auto pt-2 text-sm text-white/70">{{ __('site.common.starting_from') }} <strong class="text-white">{{ rupiah($package->startingPrice()) }}</strong>{{ __('site.common.per_pax') }}</span>
                        </span>
                    </a>
                @endforeach
                <a href="{{ lroute('custom') }}" class="flex flex-col justify-center rounded-3xl bg-sun-500 p-6 text-white transition hover:bg-sun-600">
                    <x-heroicon-o-adjustments-horizontal class="size-8" />
                    <span class="mt-3 font-display text-lg font-bold">{{ __('site.packages.custom_cta_title') }}</span>
                    <span class="mt-1 text-sm text-white/85">{{ __('site.packages.custom_cta_text') }}</span>
                </a>
            </div>
        </div>
    </section>

    {{-- 11. ULASAN --}}
    @if($testimonials->isNotEmpty())
        <section class="py-16 sm:py-24">
            <div class="container-site">
                <div class="flex flex-wrap items-end justify-between gap-6">
                    <x-section-heading :eyebrow="__('site.home.testimonials_eyebrow')" :title="__('site.home.testimonials_title')" />
                    <a href="{{ lroute('reviews') }}" class="btn-outline">{{ __('site.common.see_all') }} <x-heroicon-m-arrow-right class="size-4" /></a>
                </div>
                <div class="mt-10 grid gap-6 md:grid-cols-3">
                    @foreach($testimonials as $testimonial)
                        @include('partials.testimonial', ['testimonial' => $testimonial])
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- 12. BERITA --}}
    @if($posts->isNotEmpty())
        <section class="bg-white py-16 sm:py-24">
            <div class="container-site">
                <div class="flex flex-wrap items-end justify-between gap-6">
                    <x-section-heading :eyebrow="__('site.home.news_eyebrow')" :title="__('site.home.news_title')" />
                    <a href="{{ lroute('news.index') }}" class="btn-outline">{{ __('site.common.see_all') }} <x-heroicon-m-arrow-right class="size-4" /></a>
                </div>
                <div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach($posts as $post)
                        <x-post-card :post="$post" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @include('partials.location')
    @include('partials.cta')
@endsection
