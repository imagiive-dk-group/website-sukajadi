@props(['label' => null, 'ratio' => '3/2', 'dark' => false, 'fill' => false, 'compact' => false, 'corner' => false])
{{-- Placeholder berlabel: menjelaskan foto apa yang perlu diunggah ke posisi ini. --}}
<div
    {{ $attributes->class([
        'relative isolate flex items-center justify-center overflow-hidden text-center',
        'ph-dark text-white/80' => $dark,
        'ph text-leaf-800' => ! $dark,
        'absolute inset-0 h-full w-full' => $fill,
        'w-full' => ! $fill,
    ]) }}
    @unless($fill) style="aspect-ratio: {{ $ratio }}" @endunless
    role="img"
    aria-label="{{ $label ?? __('site.common.photo') }}"
>
    @unless($dark && $fill)
        <div class="flex max-w-[85%] flex-col items-center gap-1.5 {{ $compact ? 'scale-90' : '' }}">
            <x-heroicon-o-camera @class(['opacity-60', 'size-6' => $compact, 'size-8 sm:size-10' => ! $compact]) />
            <span class="font-display text-[10px] font-bold tracking-[0.3em] opacity-70">{{ __('site.common.photo') }}</span>
            @if($label && ! $compact)
                <span class="line-clamp-2 text-xs leading-snug font-medium opacity-80 sm:text-sm">{{ $label }}</span>
            @endif
        </div>
    @else
        @if($label && ! $compact)
            <span @class(['absolute z-10 hidden max-w-xs items-center gap-2 rounded-full bg-black/25 px-3 py-1.5 text-left text-[11px] text-white/80 backdrop-blur sm:flex', 'top-3 right-3' => $corner, 'top-24 right-4 sm:top-28' => ! $corner])>
                <x-heroicon-o-camera class="size-4 shrink-0" />
                <span class="line-clamp-1">{{ __('site.common.photo') }}: {{ $label }}</span>
            </span>
        @endif
    @endunless
</div>
