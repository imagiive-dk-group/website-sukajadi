@php
    $locale = app()->getLocale();
    $siteName = setting('site_name', 'Desa Wisata Sukajadi');
    // @section('x', '...') sudah di-escape oleh Blade; decode agar tidak ter-escape dua kali.
    $pageTitle = html_entity_decode(trim($__env->yieldContent('title')), ENT_QUOTES);
    $fullTitle = $pageTitle ? $pageTitle.' | '.$siteName : $siteName.' — '.setting_tr('tagline');
    $description = html_entity_decode(trim($__env->yieldContent('description')), ENT_QUOTES) ?: __('site.meta.default_description');
    $ogImage = html_entity_decode(trim($__env->yieldContent('og_image')), ENT_QUOTES) ?: asset('images/og-default.jpg');
    $gaId = setting('ga_measurement_id');
@endphp
<!DOCTYPE html>
<html lang="{{ $locale }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $fullTitle }}</title>
    <meta name="description" content="{{ \Illuminate\Support\Str::limit(strip_tags($description), 160) }}">
    <link rel="canonical" href="{{ url()->current() }}">
    <link rel="alternate" hreflang="id" href="{{ alternate_locale_url('id') }}">
    <link rel="alternate" hreflang="en" href="{{ alternate_locale_url('en') }}">
    <link rel="alternate" hreflang="x-default" href="{{ alternate_locale_url('id') }}">

    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:site_name" content="{{ $siteName }}">
    <meta property="og:title" content="{{ $pageTitle ?: $siteName }}">
    <meta property="og:description" content="{{ \Illuminate\Support\Str::limit(strip_tags($description), 200) }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="{{ $ogImage }}">
    <meta property="og:locale" content="{{ $locale === 'en' ? 'en_US' : 'id_ID' }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="theme-color" content="#277721">

    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/apple-touch-icon.png') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script type="application/ld+json">{!! json_encode([
        '@context' => 'https://schema.org',
        '@type' => 'TouristInformationCenter',
        'name' => $siteName,
        'url' => lroute('home'),
        'logo' => asset('images/logo.png'),
        'image' => asset('images/og-default.jpg'),
        'telephone' => setting('whatsapp'),
        'email' => setting('email'),
        'address' => [
            '@type' => 'PostalAddress',
            'addressLocality' => 'Desa Sukajadi, Kecamatan Tamansari',
            'addressRegion' => 'Kabupaten Bogor, Jawa Barat',
            'addressCountry' => 'ID',
        ],
        'sameAs' => array_values(array_filter([instagram_url(setting('instagram'))])),
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}</script>
    @stack('head')

    @if($gaId)
        <script async src="https://www.googletagmanager.com/gtag/js?id={{ $gaId }}"></script>
        <script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag('js',new Date());gtag('config','{{ $gaId }}');</script>
    @endif
</head>
<body class="flex min-h-screen flex-col">
    <a href="#konten" class="sr-only focus:not-sr-only focus:fixed focus:top-3 focus:left-3 focus:z-[100] focus:rounded-full focus:bg-white focus:px-4 focus:py-2">Skip to content</a>

    @include('partials.header')

    <main id="konten" class="flex-1">
        @yield('content')
    </main>

    @include('partials.footer')

    {{-- Tombol WhatsApp melayang --}}
    <a href="{{ wa_link(null, __('site.contact.wa_default')) }}" target="_blank" rel="noopener"
       class="fixed right-4 bottom-4 z-40 flex items-center gap-2 rounded-full bg-[#25D366] p-3.5 text-white shadow-xl shadow-black/20 transition hover:scale-105 sm:right-6 sm:bottom-6 sm:px-5"
       aria-label="{{ __('site.common.chat_whatsapp') }}">
        <x-icons.whatsapp class="size-6" />
        <span class="hidden font-display text-sm font-semibold sm:inline">{{ __('site.common.chat_whatsapp') }}</span>
    </a>

    @stack('scripts')
</body>
</html>
