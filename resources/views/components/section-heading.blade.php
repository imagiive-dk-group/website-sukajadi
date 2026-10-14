@props(['eyebrow' => null, 'title', 'subtitle' => null, 'center' => false, 'light' => false])
<div {{ $attributes->class(['max-w-2xl', 'mx-auto text-center' => $center]) }}>
    @if($eyebrow)
        <p @class(['eyebrow', '!text-sun-300' => $light])>
            <x-heroicon-s-sparkles class="size-3.5" />{{ $eyebrow }}
        </p>
    @endif
    <h2 @class(['mt-3 text-3xl leading-tight font-bold sm:text-4xl', '!text-white' => $light])>{{ $title }}</h2>
    @if($subtitle)
        <p @class(['mt-3 text-base sm:text-lg', 'text-leaf-950/65' => ! $light, 'text-white/80' => $light])>{{ $subtitle }}</p>
    @endif
</div>
