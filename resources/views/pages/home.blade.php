@extends('layouts.app')

@section('description', setting_tr('hero_subtitle'))

@section('content')
    {{-- HERO --}}
    <section class="relative isolate flex min-h-[92svh] items-end overflow-hidden bg-leaf-900 pt-28 pb-16 sm:items-center sm:pb-24">
        <div class="absolute inset-0 -z-10">
            <x-img :path="setting('hero_image')" label="Panorama Desa Sukajadi: sawah, perbukitan & rombongan berjalan di jalan desa" fill dark size="lg" sizes="100vw" eager />
            <div class="absolute inset-0 bg-gradient-to-t from-leaf-950/90 via-leaf-950/40 to-leaf-950/30"></div>
        </div>
        <div class="container-site">
            <div class="max-w-3xl">
                <p class="inline-flex items-center gap-2 rounded-full bg-white/15 px-4 py-1.5 font-display text-xs font-semibold tracking-[0.2em] text-white uppercase backdrop-blur">
                    <x-heroicon-s-map-pin class="size-4 text-sun-400" /> Tamansari · Bogor
                </p>
                <h1 class="mt-5 text-5xl leading-[1.05] font-extrabold !text-white sm:text-7xl">
                    {{ setting_tr('hero_title') }}
                </h1>
                <p class="mt-3 font-hand text-3xl text-sun-300 sm:text-4xl">{{ setting_tr('tagline') }}</p>
                <p class="mt-5 max-w-2xl text-lg text-white/85">{{ setting_tr('hero_subtitle') }}</p>
                <div class="mt-8 flex flex-wrap gap-3">
                    <a href="{{ lroute('packages.index') }}" class="btn-primary !px-7 !py-3.5 !text-base">{{ __('site.home.hero_cta_packages') }} <x-heroicon-m-arrow-right class="size-5" /></a>
                    <a href="{{ wa_link(null, __('site.contact.wa_default')) }}" target="_blank" rel="noopener" class="btn-ghost-light !px-7 !py-3.5 !text-base"><x-icons.whatsapp /> {{ __('site.common.chat_whatsapp') }}</a>
                </div>
            </div>
        </div>
    </section>

    {{-- 4 PILAR --}}
    <section class="relative -mt-10 pb-6">
        <div class="container-site">
            <div class="grid grid-cols-2 gap-3 sm:gap-4 lg:grid-cols-4">
                @foreach(['edukasi' => 'heroicon-o-academic-cap', 'alam' => 'heroicon-o-sun', 'budaya' => 'heroicon-o-musical-note', 'kuliner' => 'heroicon-o-cake'] as $key => $icon)
                    <a href="{{ lroute('attractions.index', ['kategori' => $key]) }}" class="card-hover group p-5 sm:p-6">
                        <span class="flex size-12 items-center justify-center rounded-2xl bg-leaf-50 text-leaf-600 transition group-hover:bg-sun-500 group-hover:text-white">
                            <x-dynamic-component :component="$icon" class="size-6" />
                        </span>
                        <h3 class="mt-4 text-lg font-bold">{{ __('site.categories.'.$key) }}</h3>
                        <p class="mt-1 text-sm text-leaf-950/60">{{ __('site.home.pillar_'.$key) }}</p>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    {{-- PAKET --}}
    <section class="py-16 sm:py-24">
        <div class="container-site">
            <div class="flex flex-wrap items-end justify-between gap-6">
                <x-section-heading :eyebrow="__('site.home.packages_eyebrow')" :title="__('site.home.packages_title')" :subtitle="__('site.home.packages_subtitle')" />
                <a href="{{ lroute('packages.index') }}" class="btn-outline">{{ __('site.common.see_all') }} <x-heroicon-m-arrow-right class="size-4" /></a>
            </div>
            <div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($packages as $package)
                    <x-package-card :package="$package" />
                @endforeach
                <div class="card flex flex-col justify-center bg-gradient-to-br from-leaf-600 to-leaf-800 p-7 text-white">
                    <x-heroicon-o-adjustments-horizontal class="size-10 text-sun-300" />
                    <h3 class="mt-4 text-2xl font-bold !text-white">{{ __('site.packages.custom_cta_title') }}</h3>
                    <p class="mt-2 text-white/80">{{ __('site.packages.custom_cta_text') }}</p>
                    <a href="{{ lroute('custom') }}" class="btn-primary mt-6 self-start">{{ __('site.nav.custom') }} <x-heroicon-m-arrow-right class="size-4" /></a>
                </div>
            </div>
        </div>
    </section>

    {{-- DESTINASI --}}
    <section class="bg-white py-16 sm:py-24">
        <div class="container-site">
            <div class="flex flex-wrap items-end justify-between gap-6">
                <x-section-heading :eyebrow="__('site.home.attractions_eyebrow')" :title="__('site.home.attractions_title')" />
                <a href="{{ lroute('attractions.index') }}" class="btn-outline">{{ __('site.common.see_all') }} <x-heroicon-m-arrow-right class="size-4" /></a>
            </div>
            <div class="-mx-4 mt-10 flex snap-x snap-mandatory gap-4 overflow-x-auto px-4 pb-4 sm:mx-0 sm:grid sm:grid-cols-2 sm:overflow-visible sm:px-0 lg:grid-cols-3">
                @foreach($attractions as $attraction)
                    <div class="w-[78%] shrink-0 snap-start sm:w-auto">
                        <x-attraction-card :attraction="$attraction" />
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- COCOK UNTUK + FASILITAS --}}
    <section class="py-16 sm:py-24">
        <div class="container-site grid items-center gap-12 lg:grid-cols-2">
            <div>
                <x-section-heading :eyebrow="__('site.home.audience_eyebrow')" :title="__('site.home.audience_title')" />
                <div class="mt-8 grid grid-cols-2 gap-3">
                    @foreach(['school' => 'heroicon-o-academic-cap', 'community' => 'heroicon-o-user-group', 'company' => 'heroicon-o-briefcase', 'family' => 'heroicon-o-home-modern'] as $key => $icon)
                        <div class="flex items-center gap-3 rounded-2xl bg-white p-4 ring-1 ring-leaf-900/5">
                            <span class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-sun-50 text-sun-600"><x-dynamic-component :component="$icon" class="size-6" /></span>
                            <span class="text-sm font-semibold">{{ __('site.group_types.'.$key) }}</span>
                        </div>
                    @endforeach
                </div>
                <h3 class="mt-10 text-xl font-bold">{{ __('site.home.facilities_title') }}</h3>
                <ul class="mt-4 grid gap-2 sm:grid-cols-2">
                    @foreach(['transport', 'meals', 'tickets', 'guide', 'insurance'] as $facility)
                        <li class="flex items-center gap-2 text-sm"><x-heroicon-s-check-circle class="size-5 text-leaf-500" /> {{ __('site.facilities.'.$facility) }}</li>
                    @endforeach
                </ul>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <x-placeholder label="Rombongan sekolah belajar di kebun" ratio="5/9" class="rounded-3xl" />
                <div class="space-y-4">
                    <x-placeholder label="Pemandu lokal menjelaskan kepada peserta" ratio="1/1" class="rounded-3xl" />
                    <x-placeholder label="Keluarga makan nasi liwet bersama" ratio="4/3" class="rounded-3xl" />
                </div>
            </div>
        </div>
    </section>

    {{-- UMKM --}}
    @if($umkms->isNotEmpty())
        <section class="bg-cream-dark/60 py-16 sm:py-24">
            <div class="container-site">
                <div class="flex flex-wrap items-end justify-between gap-6">
                    <x-section-heading :eyebrow="__('site.home.umkm_eyebrow')" :title="__('site.home.umkm_title')" :subtitle="__('site.home.umkm_subtitle')" />
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

    {{-- GALERI --}}
    <section class="py-16 sm:py-24">
        <div class="container-site">
            <div class="flex flex-wrap items-end justify-between gap-6">
                <x-section-heading :eyebrow="__('site.home.gallery_eyebrow')" :title="__('site.home.gallery_title')" />
                <a href="{{ lroute('gallery.index') }}" class="btn-outline">{{ __('site.common.see_all') }} <x-heroicon-m-arrow-right class="size-4" /></a>
            </div>
            {{-- Bento: 1 foto besar, 1 foto lebar, sisanya kecil --}}
            <div class="mt-10 grid auto-rows-[160px] grid-cols-2 gap-3 sm:auto-rows-[200px] sm:gap-4 md:grid-cols-4">
                @foreach($photos as $photo)
                    @php($full = \App\Support\Images::url($photo->path, 'lg') ?? route('placeholder', ['w' => 1600, 'h' => 1066, 'label' => $photo->placeholder_label]))
                    <a href="{{ $full }}" data-gallery="home" data-type="image" data-description="{{ $photo->tr('caption') ?? $photo->placeholder_label }}"
                       @class(['glightbox group relative overflow-hidden rounded-2xl', 'col-span-2 row-span-2' => $loop->index === 0, 'col-span-2' => $loop->index === 5])>
                        <x-img :path="$photo->path" :label="$photo->placeholder_label" fill :compact="! in_array($loop->index, [0, 5])" sizes="(min-width: 768px) 50vw, 100vw" class="transition duration-500 group-hover:scale-105" />
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    {{-- CARA BOOKING --}}
    <section class="relative overflow-hidden bg-leaf-800 py-16 text-white sm:py-24">
        <div class="pointer-events-none absolute -top-24 -right-24 size-80 rounded-full bg-leaf-600/40 blur-3xl"></div>
        <div class="container-site relative">
            <x-section-heading :eyebrow="__('site.home.steps_eyebrow')" :title="__('site.home.steps_title')" center light />
            <ol class="mt-12 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                @foreach([1 => 'heroicon-o-ticket', 2 => 'heroicon-o-pencil-square', 3 => 'heroicon-o-qr-code', 4 => 'heroicon-o-chat-bubble-left-right'] as $i => $icon)
                    <li class="relative rounded-3xl bg-white/5 p-6 ring-1 ring-white/10">
                        <span class="absolute top-5 right-5 font-display text-5xl font-extrabold text-white/10">0{{ $i }}</span>
                        <x-dynamic-component :component="$icon" class="size-9 text-sun-400" />
                        <h3 class="mt-4 text-lg font-bold !text-white">{{ __('site.home.step'.$i) }}</h3>
                        <p class="mt-1.5 text-sm text-white/70">{{ __('site.home.step'.$i.'_text') }}</p>
                    </li>
                @endforeach
            </ol>
            <div class="mt-10 text-center">
                <a href="{{ lroute('booking.create') }}" class="btn-primary !px-8 !py-3.5 !text-base">{{ __('site.nav.book_now') }} <x-heroicon-m-arrow-right class="size-5" /></a>
            </div>
        </div>
    </section>

    {{-- ULASAN --}}
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

    {{-- BERITA --}}
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

    {{-- LOKASI --}}
    @include('partials.location')

    {{-- CTA --}}
    @include('partials.cta')
@endsection
