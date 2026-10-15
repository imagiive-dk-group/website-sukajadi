@php
    $nav = [
        ['label' => __('site.nav.home'), 'route' => 'home'],
        ['label' => __('site.nav.about'), 'children' => [
            ['label' => __('site.nav.tourism_village'), 'route' => 'about', 'icon' => 'heroicon-o-sparkles'],
            ['label' => __('site.nav.profile'), 'route' => 'profile', 'icon' => 'heroicon-o-building-library'],
            ['label' => __('site.nav.team'), 'route' => 'team', 'icon' => 'heroicon-o-user-group'],
            ['label' => __('site.nav.news'), 'route' => 'news.index', 'icon' => 'heroicon-o-newspaper'],
        ]],
        ['label' => __('site.nav.explore'), 'children' => [
            ['label' => __('site.nav.attractions'), 'route' => 'attractions.index', 'icon' => 'heroicon-o-map'],
            ['label' => __('site.partner_categories.wisata'), 'route' => 'partners.index', 'params' => ['kategori' => 'wisata'], 'icon' => 'heroicon-o-globe-asia-australia'],
            ['label' => __('site.partner_categories.penginapan'), 'route' => 'partners.index', 'params' => ['kategori' => 'penginapan'], 'icon' => 'heroicon-o-home-modern'],
            ['label' => __('site.partner_categories.kafe'), 'route' => 'partners.index', 'params' => ['kategori' => 'kafe'], 'icon' => 'heroicon-o-cake'],
        ]],
        ['label' => __('site.nav.packages'), 'children' => [
            ['label' => __('site.nav.all_packages'), 'route' => 'packages.index', 'icon' => 'heroicon-o-ticket'],
            ['label' => __('site.nav.custom'), 'route' => 'custom', 'icon' => 'heroicon-o-adjustments-horizontal'],
            ['label' => __('site.nav.check_booking'), 'route' => 'booking.check', 'icon' => 'heroicon-o-magnifying-glass'],
        ]],
        ['label' => __('site.nav.umkm'), 'children' => [
            ['label' => __('site.nav.umkm_directory'), 'route' => 'umkm.index', 'icon' => 'heroicon-o-building-storefront'],
            ['label' => __('site.nav.products'), 'route' => 'products', 'icon' => 'heroicon-o-shopping-bag'],
        ]],
        ['label' => __('site.nav.gallery'), 'route' => 'gallery.index'],
        ['label' => __('site.nav.contact'), 'route' => 'contact'],
    ];
    $current = \Illuminate\Support\Str::after((string) request()->route()?->getName(), '.');
    $isActive = fn ($item) => isset($item['route'])
        ? \Illuminate\Support\Str::before($item['route'], '.') === \Illuminate\Support\Str::before($current, '.') && ($item['route'] !== 'home' || $current === 'home')
        : collect($item['children'])->contains(fn ($c) => \Illuminate\Support\Str::before($c['route'], '.') === \Illuminate\Support\Str::before($current, '.'));
    $overHero = $overHero ?? true;
@endphp
<header
    x-data="{ open: false, scrolled: false }"
    x-init="scrolled = window.scrollY > 20"
    @scroll.window="scrolled = window.scrollY > 20"
    @keydown.escape.window="open = false"
    :class="scrolled || open ? 'bg-white/95 shadow-sm backdrop-blur text-leaf-900' : 'bg-transparent text-white'"
    class="fixed inset-x-0 top-0 z-50 text-white transition-colors duration-300"
>
    <div class="container-site flex h-16 items-center justify-between gap-4 sm:h-20">
        <a href="{{ lroute('home') }}" class="flex shrink-0 items-center gap-2.5">
            <img src="{{ asset('images/logo.webp') }}" alt="Logo Desa Wisata Sukajadi" class="size-11 drop-shadow sm:size-14" width="56" height="56">
            <span class="leading-tight">
                <span class="block font-display text-[11px] font-semibold tracking-[0.18em] uppercase opacity-80">Desa Wisata</span>
                <span class="block font-display text-lg font-extrabold sm:text-xl">Sukajadi</span>
            </span>
        </a>

        <nav class="hidden items-center gap-1 xl:flex" aria-label="Menu utama">
            @foreach($nav as $item)
                @if(isset($item['children']))
                    <div class="relative" x-data="{ sub: false }" @mouseenter="sub = true" @mouseleave="sub = false">
                        <button type="button" @click="sub = !sub" :aria-expanded="sub" @class(['flex items-center gap-1 rounded-full px-3 py-2 text-sm font-semibold transition hover:bg-black/5', 'text-sun-500' => $isActive($item)])>
                            {{ $item['label'] }} <x-heroicon-m-chevron-down class="size-4 opacity-70" />
                        </button>
                        <div x-show="sub" x-cloak x-transition.opacity.duration.150ms class="absolute top-full left-0 pt-2">
                            <div class="w-60 rounded-2xl bg-white p-2 text-leaf-900 shadow-xl ring-1 ring-leaf-900/10">
                                @foreach($item['children'] as $child)
                                    <a href="{{ lroute($child['route'], $child['params'] ?? []) }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium hover:bg-leaf-50">
                                        <x-dynamic-component :component="$child['icon']" class="size-5 text-leaf-600" /> {{ $child['label'] }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @else
                    <a href="{{ lroute($item['route']) }}" @class(['rounded-full px-3 py-2 text-sm font-semibold transition hover:bg-black/5', 'text-sun-500' => $isActive($item)])>{{ $item['label'] }}</a>
                @endif
            @endforeach
        </nav>

        <div class="flex items-center gap-2">
            <div class="flex items-center rounded-full bg-black/10 p-0.5 font-display text-xs font-bold" aria-label="{{ __('site.nav.language') }}">
                @foreach(['id' => 'ID', 'en' => 'EN'] as $code => $text)
                    <a href="{{ alternate_locale_url($code) }}" hreflang="{{ $code }}" lang="{{ $code }}" @class(['rounded-full px-2.5 py-1.5 transition', 'bg-white text-leaf-800 shadow' => app()->getLocale() === $code, 'opacity-75 hover:opacity-100' => app()->getLocale() !== $code])>{{ $text }}</a>
                @endforeach
            </div>
            <a href="{{ lroute('booking.create') }}" class="btn-primary hidden sm:inline-flex">{{ __('site.nav.book_now') }}</a>
            <button type="button" class="rounded-full p-2 hover:bg-black/5 xl:hidden" @click="open = !open" :aria-expanded="open" aria-controls="menu-mobile">
                <span class="sr-only">{{ __('site.nav.menu') }}</span>
                <x-heroicon-o-bars-3 class="size-7" x-show="!open" />
                <x-heroicon-o-x-mark class="size-7" x-show="open" x-cloak />
            </button>
        </div>
    </div>

    {{-- Menu HP --}}
    <div id="menu-mobile" x-show="open" x-cloak x-collapse class="border-t border-leaf-900/10 bg-white text-leaf-900 xl:hidden">
        <nav class="container-site max-h-[calc(100vh-4rem)] overflow-y-auto py-4" aria-label="Menu">
            @foreach($nav as $item)
                @if(isset($item['children']))
                    <p class="mt-3 px-3 pb-1 text-xs font-bold tracking-widest text-leaf-950/45 uppercase">{{ $item['label'] }}</p>
                    @foreach($item['children'] as $child)
                        <a href="{{ lroute($child['route'], $child['params'] ?? []) }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 font-medium hover:bg-leaf-50">
                            <x-dynamic-component :component="$child['icon']" class="size-5 text-leaf-600" /> {{ $child['label'] }}
                        </a>
                    @endforeach
                @else
                    <a href="{{ lroute($item['route']) }}" class="block rounded-xl px-3 py-2.5 font-semibold hover:bg-leaf-50">{{ $item['label'] }}</a>
                @endif
            @endforeach
            <a href="{{ lroute('booking.create') }}" class="btn-primary mt-4 w-full">{{ __('site.nav.book_now') }}</a>
        </nav>
    </div>
</header>
