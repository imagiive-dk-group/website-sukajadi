@props(['partner'])
@php($from = $partner->startingPrice())
<article class="card-hover group flex flex-col">
    <a href="{{ $partner->url() }}" class="relative block overflow-hidden">
        <x-img :path="$partner->cover_path" :label="$partner->placeholder_label" :alt="$partner->name" ratio="4/3" class="transition duration-500 group-hover:scale-105" />
        <span class="chip absolute top-3 left-3 bg-white/90 text-leaf-800 backdrop-blur">{{ __('site.partner_categories.'.$partner->category) }}</span>
    </a>
    <div class="flex flex-1 flex-col p-5">
        <h3 class="text-lg font-bold"><a href="{{ $partner->url() }}" class="hover:text-leaf-600">{{ $partner->name }}</a></h3>
        <p class="mt-1.5 line-clamp-2 text-sm text-leaf-950/65">{{ $partner->tr('summary') }}</p>
        <div class="mt-auto flex items-end justify-between gap-3 pt-4">
            @if($from)
                @php($cheapest = $partner->offers->where('is_active', true)->sortBy('price')->first())
                <p class="text-sm"><span class="block text-xs text-leaf-950/55">{{ __('site.partners.from') }}</span><span class="font-display text-lg font-bold text-leaf-700">{{ rupiah($from) }}</span><span class="text-xs text-leaf-950/50">{{ $cheapest?->unitLabel() }}</span></p>
            @endif
            <a href="{{ $partner->url() }}" class="flex size-10 shrink-0 items-center justify-center rounded-full border border-leaf-600/30 text-leaf-800 transition hover:border-leaf-600 hover:bg-leaf-50" aria-label="{{ __('site.common.see_detail') }}: {{ $partner->name }}"><x-heroicon-m-arrow-right class="size-5" /></a>
        </div>
    </div>
</article>
