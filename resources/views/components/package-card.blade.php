@props(['package'])
@php
    $from = $package->startingPrice();
    $badge = $package->tiers->first(fn ($t) => filled($t->tr('badge')))?->tr('badge');
@endphp
<article class="card-hover group flex flex-col">
    <a href="{{ $package->url() }}" class="relative block overflow-hidden">
        <x-img :path="$package->cover_path" :label="$package->placeholder_label" :alt="$package->tr('name')" ratio="4/3" class="transition duration-500 group-hover:scale-105" />
        <span class="chip absolute top-3 left-3 bg-white/90 text-leaf-800 backdrop-blur">{{ __('site.categories.'.$package->category) }}</span>
        @if($badge)
            <span class="chip absolute top-3 right-3 bg-sun-500 text-white shadow">{{ $badge }}</span>
        @endif
    </a>
    <div class="flex flex-1 flex-col p-5 sm:p-6">
        <h3 class="text-lg leading-snug font-bold"><a href="{{ $package->url() }}" class="hover:text-leaf-600">{{ $package->tr('name') }}</a></h3>
        <p class="mt-2 line-clamp-3 text-sm text-leaf-950/65">{{ $package->tr('summary') }}</p>
        <div class="mt-4 flex flex-wrap gap-2 text-xs text-leaf-800">
            @if($package->tr('duration'))
                <span class="chip bg-leaf-50"><x-heroicon-o-clock class="size-3.5" />{{ $package->tr('duration') }}</span>
            @endif
            <span class="chip bg-leaf-50"><x-heroicon-o-user-group class="size-3.5" />{{ __('site.common.min_pax', ['count' => $package->minPax()]) }}</span>
        </div>
        <div class="mt-auto flex items-end justify-between gap-3 pt-5">
            @if($from)
                <div>
                    <p class="text-xs text-leaf-950/55">{{ __('site.common.starting_from') }}</p>
                    <p class="font-display text-xl font-bold text-leaf-700">{{ rupiah($from) }}<span class="text-sm font-medium text-leaf-950/50">{{ __('site.common.per_pax') }}</span></p>
                </div>
            @endif
            <a href="{{ $package->url() }}" class="btn-outline !px-4 !py-2" aria-label="{{ __('site.common.see_detail') }}: {{ $package->tr('name') }}">
                {{ __('site.common.see_detail') }} <x-heroicon-m-arrow-right class="size-4" />
            </a>
        </div>
    </div>
</article>
