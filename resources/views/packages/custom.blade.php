@extends('layouts.app')

@section('title', __('site.custom.title'))
@section('description', __('site.custom.subtitle'))

@section('content')
    <x-page-hero :title="__('site.custom.title')" :subtitle="__('site.custom.subtitle')" label="Peserta mencoba berbagai aktivitas desa" :breadcrumbs="[__('site.packages.title') => lroute('packages.index'), __('site.custom.title') => null]" />

    <section class="py-12 sm:py-16" x-data="customPackage(@js([
        'attractions' => $attractions->map(fn ($a) => ['id' => $a->id, 'name' => $a->tr('name'), 'price' => $a->custom_price])->values(),
        'basePrice' => $basePrice,
        'minPax' => $minPax,
        'bookingUrl' => lroute('booking.create'),
    ]))">
        <div class="container-site grid gap-10 lg:grid-cols-3">
            <div class="lg:col-span-2">
                <h2 class="text-2xl font-bold">1. {{ __('site.custom.step_activities') }}</h2>
                <div class="mt-6 grid gap-4 sm:grid-cols-2">
                    @foreach($attractions as $attraction)
                        <label class="card group relative flex cursor-pointer gap-4 p-3 has-[:checked]:ring-2 has-[:checked]:ring-sun-500">
                            <input type="checkbox" value="{{ $attraction->id }}" x-model="selected" class="peer sr-only">
                            <span class="w-28 shrink-0 overflow-hidden rounded-2xl">
                                <x-img :path="$attraction->cover_path" :label="$attraction->placeholder_label" ratio="1/1" size="sm" compact />
                            </span>
                            <span class="flex flex-1 flex-col py-1 pr-6">
                                <span class="text-xs font-semibold text-sun-600">{{ __('site.categories.'.$attraction->category) }}</span>
                                <span class="font-display font-bold leading-snug text-leaf-900">{{ $attraction->tr('name') }}</span>
                                <span class="mt-1 line-clamp-2 text-xs text-leaf-950/60">{{ $attraction->tr('summary') }}</span>
                                <span class="mt-auto pt-2 text-sm font-bold text-leaf-700">+{{ rupiah($attraction->custom_price) }}{{ __('site.common.per_pax') }}</span>
                            </span>
                            <span class="absolute top-3 right-3 flex size-6 items-center justify-center rounded-full border-2 border-leaf-900/20 text-white peer-checked:border-sun-500 peer-checked:bg-sun-500">
                                <x-heroicon-m-check class="size-4" />
                            </span>
                        </label>
                    @endforeach
                </div>
            </div>

            <aside class="lg:sticky lg:top-28 lg:self-start">
                <div class="card p-6">
                    <h2 class="text-xl font-bold">{{ __('site.common.price_estimate') }}</h2>
                    <dl class="mt-5 space-y-3 text-sm">
                        <div class="flex justify-between gap-4"><dt class="text-leaf-950/65">{{ __('site.custom.base_price') }}</dt><dd class="font-semibold" x-text="money({{ $basePrice }})"></dd></div>
                        <template x-for="item in items" :key="item.id">
                            <div class="flex justify-between gap-4"><dt class="text-leaf-950/65" x-text="item.name"></dt><dd class="font-semibold" x-text="'+' + money(item.price)"></dd></div>
                        </template>
                        <div class="flex justify-between gap-4 border-t border-leaf-900/10 pt-3"><dt class="font-semibold">{{ __('site.custom.per_pax_total') }}</dt><dd class="font-display text-lg font-bold text-leaf-700" x-text="money(perPax)"></dd></div>
                    </dl>
                    <label class="label mt-6" for="pax">{{ __('site.custom.pax') }}</label>
                    <input id="pax" type="number" min="{{ $minPax }}" class="input" x-model="pax">
                    <p class="mt-1.5 text-xs text-leaf-950/55">{{ __('site.custom.min_pax', ['count' => $minPax]) }}</p>
                    <div class="mt-5 rounded-2xl bg-leaf-50 p-4">
                        <p class="text-sm text-leaf-950/65">{{ __('site.custom.total') }}</p>
                        <p class="font-display text-3xl font-extrabold text-leaf-700" x-text="money(total)"></p>
                    </div>
                    <p class="mt-3 text-xs text-sun-700" x-show="items.length === 0">{{ __('site.custom.select_one') }}</p>
                    <a :href="canContinue ? bookingUrl : null" :class="!canContinue && 'pointer-events-none opacity-50'" class="btn-primary mt-4 w-full !py-3">{{ __('site.custom.continue') }} <x-heroicon-m-arrow-right class="size-4" /></a>
                    <p class="mt-4 text-xs text-leaf-950/55">{{ __('site.custom.note') }}</p>
                </div>
            </aside>
        </div>
    </section>
@endsection
