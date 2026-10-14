@props([
    'path' => null,
    'label' => null,
    'alt' => null,
    'ratio' => '3/2',
    'size' => 'md',
    'sizes' => '(min-width: 1024px) 33vw, (min-width: 640px) 50vw, 100vw',
    'fill' => false,
    'eager' => false,
    'dark' => false,
    'compact' => false,
])
@php($url = \App\Support\Images::url($path, $size))
@if($url)
    <img
        src="{{ $url }}"
        @if($srcset = \App\Support\Images::srcset($path)) srcset="{{ $srcset }}" sizes="{{ $sizes }}" @endif
        alt="{{ $alt ?? $label ?? '' }}"
        loading="{{ $eager ? 'eager' : 'lazy' }}"
        decoding="async"
        @if($eager) fetchpriority="high" @endif
        {{ $attributes->class([
            'object-cover',
            'absolute inset-0 h-full w-full' => $fill,
            'w-full' => ! $fill,
        ]) }}
        @unless($fill) style="aspect-ratio: {{ $ratio }}" @endunless
    >
@else
    <x-placeholder :label="$label" :ratio="$ratio" :fill="$fill" :dark="$dark" :compact="$compact" {{ $attributes }} />
@endif
