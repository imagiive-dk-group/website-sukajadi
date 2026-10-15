{{-- Latar hero: video (bila diunggah di Pengaturan) atau slideshow foto bergerak. --}}
@php
    $video = setting('hero_video');
    $slides = array_values(array_filter((array) setting('hero_slides', [])));
    if (! $slides && setting('hero_image')) {
        $slides = [setting('hero_image')];
    }
    $placeholderSlides = [
        'Video/foto pembuka: panorama sawah & perbukitan Sukajadi saat pagi',
        'Foto: warga & pengunjung beraktivitas di kebun',
        'Foto: air terjun Curug Aman',
    ];
    $count = $slides ? count($slides) : count($placeholderSlides);
    $step = 6;
@endphp
@if($video)
    <video autoplay muted loop playsinline preload="metadata"
           @if($slides) poster="{{ \App\Support\Images::url($slides[0], 'lg') }}" @endif
           class="absolute inset-0 h-full w-full object-cover" aria-hidden="true">
        <source src="{{ \Illuminate\Support\Facades\Storage::disk('uploads')->url($video) }}" type="{{ str_ends_with(strtolower($video), '.webm') ? 'video/webm' : 'video/mp4' }}">
    </video>
@else
    @for($i = 0; $i < $count; $i++)
        <div class="{{ $count > 1 ? 'hero-slide' : '' }} absolute inset-0 overflow-hidden" style="z-index: {{ $count - $i }}; animation-duration: {{ $count * $step }}s; animation-delay: {{ $i === 0 ? 0 : -($count * $step - $i * $step) }}s">
            <div class="absolute inset-0">
                <x-img :path="$slides[$i] ?? null" :label="$placeholderSlides[$i] ?? null" fill dark size="lg" sizes="100vw" :eager="$i === 0" />
            </div>
        </div>
    @endfor
@endif
