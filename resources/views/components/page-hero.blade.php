@props(['title', 'subtitle' => null, 'image' => null, 'label' => null, 'breadcrumbs' => []])
{{-- Hero halaman dalam: foto latar + judul + breadcrumb. Tanpa foto khusus, dipakai foto desa yang berganti per halaman. --}}
@php
    if (blank($image)) {
        $pool = ['photos/sukajadi-26.jpg', 'photos/sukajadi-18.jpg', 'photos/sukajadi-46.jpg', 'photos/sukajadi-32.jpg', 'photos/sukajadi-42.jpg', 'photos/sukajadi-19.jpg', 'photos/sukajadi-28.jpg', 'photos/sukajadi-20.jpg'];
        $image = $pool[crc32((string) $title) % count($pool)];
    }
@endphp
<section class="relative isolate overflow-hidden bg-leaf-900 pt-28 pb-16 sm:pt-36 sm:pb-24">
    <div class="hero-drift absolute inset-0 -z-10">
        <div class="hero-media absolute inset-0">
            <x-img :path="$image" :label="$label" fill dark size="lg" sizes="100vw" eager />
        </div>
        <div class="absolute inset-0 bg-gradient-to-t from-leaf-950/90 via-leaf-950/55 to-leaf-950/30"></div>
    </div>
    <div class="hero-exit container-site">
        @if($breadcrumbs)
            <nav aria-label="Breadcrumb" class="hero-in mb-4 flex flex-wrap items-center gap-1.5 text-sm text-white/70">
                <a href="{{ lroute('home') }}" class="hover:text-white">{{ __('site.common.breadcrumb_home') }}</a>
                @foreach($breadcrumbs as $text => $url)
                    <x-heroicon-m-chevron-right class="size-4 opacity-60" />
                    @if($url)
                        <a href="{{ $url }}" class="hover:text-white">{{ $text }}</a>
                    @else
                        <span class="text-white" aria-current="page">{{ $text }}</span>
                    @endif
                @endforeach
            </nav>
        @endif
        <h1 class="hero-in max-w-3xl text-3xl leading-tight font-extrabold !text-white sm:text-5xl" style="--hd: 120ms">{{ $title }}</h1>
        @if($subtitle)
            <p class="hero-in mt-4 max-w-2xl text-base text-white/85 sm:text-lg" style="--hd: 260ms">{{ $subtitle }}</p>
        @endif
        {{ $slot }}
    </div>
</section>
