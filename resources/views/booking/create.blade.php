@extends('layouts.app')

@section('title', __('site.booking.title'))
@section('description', __('site.booking.subtitle'))

@php
    $old = session()->getOldInput();
    $preset = array_merge($preset, array_filter([
        'type' => $old['type'] ?? null,
        'package_tier_id' => $old['package_tier_id'] ?? null,
        'attraction_ids' => $old['attraction_ids'] ?? null,
        'visit_date' => $old['visit_date'] ?? null,
        'pax' => $old['pax'] ?? null,
        'addons' => $old['addons'] ?? null,
    ], fn ($v) => $v !== null));
    if (! empty($old['package_tier_id'])) {
        $preset['package_id'] = $packages->first(fn ($p) => $p->tiers->contains('id', (int) $old['package_tier_id']))?->id;
    }
    // Buka langkah pertama yang memiliki error dari server.
    $startStep = 1;
    if ($errors->any()) {
        $startStep = match (true) {
            $errors->hasAny(['type', 'package_tier_id', 'attraction_ids', 'attraction_ids.*']) => 1,
            $errors->hasAny(['visit_date', 'pax', 'addons', 'addons.*']) => 2,
            default => 3,
        };
    }
    $steps = [1 => __('site.booking.step_package'), 2 => __('site.booking.step_schedule'), 3 => __('site.booking.step_contact'), 4 => __('site.booking.step_review')];
@endphp

@section('content')
    <x-page-hero :title="__('site.booking.title')" :subtitle="__('site.booking.subtitle')" label="Rombongan tiba di Desa Wisata Sukajadi" :breadcrumbs="[__('site.nav.booking') => null]" />

    <section class="py-10 sm:py-14" x-data="bookingForm(@js([
        'locale' => app()->getLocale(),
        'availabilityUrl' => route('booking.availability'),
        'startStep' => $startStep,
        'preset' => $preset,
        'customBase' => $customBase,
        'customMinPax' => $customMinPax,
        'packages' => $packages->map(fn ($p) => [
            'id' => $p->id,
            'name' => $p->tr('name'),
            'tiers' => $p->tiers->map(fn ($t) => ['id' => $t->id, 'name' => $t->tr('name'), 'price' => $t->price, 'min_pax' => $t->min_pax, 'duration' => $t->tr('duration')])->values(),
        ])->values(),
        'attractions' => $attractions->map(fn ($a) => ['id' => $a->id, 'name' => $a->tr('name'), 'price' => $a->custom_price])->values(),
        'addons' => $addons->map(fn ($a) => ['id' => $a->id, 'name' => $a->tr('name'), 'price' => $a->price, 'unit' => $a->unit])->values(),
        'labels' => [
            'checking' => __('site.booking.checking'),
            'choosePackage' => __('validation.required', ['attribute' => __('site.booking.package')]),
            'chooseActivity' => __('site.custom.select_one'),
            'chooseDate' => __('validation.required', ['attribute' => __('site.booking.date')]),
            'minPax' => __('site.booking.errors.min_pax'),
        ],
    ]))">
        <div class="container-site" x-ref="top">
            {{-- Indikator langkah --}}
            <ol class="mx-auto mb-8 flex max-w-3xl items-center gap-2 scroll-mt-28" aria-label="Langkah">
                @foreach($steps as $i => $label)
                    <li class="flex flex-1 items-center gap-2">
                        <button type="button" @click="goTo({{ $i }})" class="flex items-center gap-2 text-left" :class="step >= {{ $i }} ? 'text-leaf-800' : 'text-leaf-950/35'">
                            <span class="flex size-8 shrink-0 items-center justify-center rounded-full font-display text-sm font-bold transition"
                                  :class="step > {{ $i }} ? 'bg-leaf-600 text-white' : (step === {{ $i }} ? 'bg-sun-500 text-white ring-4 ring-sun-200' : 'bg-white ring-1 ring-leaf-900/15')">{{ $i }}</span>
                            <span class="hidden text-sm font-semibold md:inline">{{ $label }}</span>
                        </button>
                        @if($i < 4)<span class="h-0.5 flex-1 rounded bg-leaf-900/10" :class="step > {{ $i }} && '!bg-leaf-500'"></span>@endif
                    </li>
                @endforeach
            </ol>

            <form method="POST" action="{{ lroute('booking.store') }}" class="grid gap-8 lg:grid-cols-3" novalidate>
                @csrf
                <input type="text" name="website" class="hidden" tabindex="-1" autocomplete="off" aria-hidden="true">
                <input type="hidden" name="type" :value="type">

                <div class="card p-5 sm:p-8 lg:col-span-2">
                    @if($errors->any())
                        <div class="mb-6 rounded-2xl bg-red-50 p-4 text-sm text-red-700 ring-1 ring-red-200" role="alert">
                            <ul class="list-disc space-y-1 pl-5">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                        </div>
                    @endif

                    {{-- LANGKAH 1: Paket --}}
                    <div x-show="step === 1">
                        <h2 class="text-2xl font-bold">1. {{ __('site.booking.step_package') }}</h2>
                        <div class="mt-5 grid grid-cols-2 gap-2 rounded-2xl bg-cream p-1.5">
                            <button type="button" @click="type = 'package'" class="rounded-xl px-4 py-2.5 text-sm font-semibold transition" :class="type === 'package' ? 'bg-white shadow text-leaf-800' : 'text-leaf-950/55'">{{ __('site.nav.packages') }}</button>
                            <button type="button" @click="type = 'custom'" class="rounded-xl px-4 py-2.5 text-sm font-semibold transition" :class="type === 'custom' ? 'bg-white shadow text-leaf-800' : 'text-leaf-950/55'">{{ __('site.nav.custom') }}</button>
                        </div>

                        <div x-show="type === 'package'" class="mt-6 space-y-5">
                            <div>
                                <label class="label" for="package">{{ __('site.booking.package') }}</label>
                                <select id="package" class="input" x-model="packageId">
                                    <option value="">{{ __('site.booking.choose_package') }}</option>
                                    @foreach($packages as $package)
                                        <option value="{{ $package->id }}">{{ $package->tr('name') }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div x-show="tiers.length" x-cloak>
                                <span class="label">{{ __('site.booking.tier') }}</span>
                                <div class="grid gap-3 sm:grid-cols-3">
                                    <template x-for="tier in tiers" :key="tier.id">
                                        <label class="cursor-pointer rounded-2xl border-2 p-4 transition" :class="tierId === String(tier.id) ? 'border-sun-500 bg-sun-50' : 'border-leaf-900/10 hover:border-leaf-300'">
                                            <input type="radio" name="package_tier_id" class="sr-only" :value="tier.id" x-model="tierId">
                                            <span class="block font-display font-bold text-leaf-900" x-text="tier.name"></span>
                                            <span class="block text-xs text-leaf-950/55" x-text="tier.duration"></span>
                                            <span class="mt-2 block font-display font-bold text-leaf-700"><span x-text="money(tier.price)"></span><span class="text-xs font-medium text-leaf-950/50">{{ __('site.common.per_pax') }}</span></span>
                                            <span class="block text-xs text-leaf-950/55" x-text="@js(__('site.common.min_pax', ['count' => '__N__'])).replace('__N__', tier.min_pax)"></span>
                                        </label>
                                    </template>
                                </div>
                            </div>
                        </div>

                        <div x-show="type === 'custom'" x-cloak class="mt-6">
                            <span class="label">{{ __('site.custom.step_activities') }}</span>
                            <div class="grid gap-3 sm:grid-cols-2">
                                @foreach($attractions as $attraction)
                                    <label class="flex cursor-pointer items-start gap-3 rounded-2xl border-2 p-3.5 transition" :class="attractionIds.includes('{{ $attraction->id }}') ? 'border-sun-500 bg-sun-50' : 'border-leaf-900/10 hover:border-leaf-300'">
                                        <input type="checkbox" name="attraction_ids[]" value="{{ $attraction->id }}" x-model="attractionIds" class="mt-1 size-4 accent-sun-500">
                                        <span>
                                            <span class="block text-sm font-semibold text-leaf-900">{{ $attraction->tr('name') }}</span>
                                            <span class="text-xs text-leaf-700">+{{ rupiah($attraction->custom_price) }}{{ __('site.common.per_pax') }}</span>
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                            <p class="mt-3 text-xs text-leaf-950/55">{{ __('site.custom.base_price') }}: {{ rupiah($customBase) }}{{ __('site.common.per_pax') }} · {{ __('site.custom.min_pax', ['count' => $customMinPax]) }}</p>
                        </div>
                    </div>

                    {{-- LANGKAH 2: Jadwal & peserta --}}
                    <div x-show="step === 2" x-cloak>
                        <h2 class="text-2xl font-bold">2. {{ __('site.booking.step_schedule') }}</h2>
                        <div class="mt-6 grid gap-5 sm:grid-cols-2">
                            <div>
                                <label class="label" for="visit_date">{{ __('site.booking.date') }}</label>
                                <input id="visit_date" type="date" name="visit_date" class="input" x-model="date" min="{{ $earliest->toDateString() }}" max="{{ $latest->toDateString() }}" required>
                                <p class="mt-1.5 text-xs text-leaf-950/55">{{ __('site.booking.lead_days', ['days' => $leadDays]) }}</p>
                            </div>
                            <div>
                                <label class="label" for="pax">{{ __('site.booking.pax') }}</label>
                                <input id="pax" type="number" name="pax" class="input" x-model="pax" :min="minPax" max="2000" inputmode="numeric" required>
                                <p class="mt-1.5 text-xs text-leaf-950/55" x-text="@js(__('site.booking.errors.min_pax', ['count' => '__N__'])).replace('__N__', minPax)"></p>
                            </div>
                        </div>
                        <div class="mt-3 flex items-center gap-2 text-sm" x-show="availability.state !== 'idle'" x-cloak
                             :class="{ 'text-leaf-700': availability.state === 'ok', 'text-red-600': availability.state === 'error', 'text-leaf-950/55': availability.state === 'checking' }">
                            <x-heroicon-s-check-circle class="size-5" x-show="availability.state === 'ok'" />
                            <x-heroicon-s-exclamation-circle class="size-5" x-show="availability.state === 'error'" />
                            <span x-text="availability.message"></span>
                        </div>

                        @if($addons->isNotEmpty())
                            <div class="mt-8">
                                <span class="label">{{ __('site.booking.addons') }}</span>
                                <div class="grid gap-3 sm:grid-cols-2">
                                    @foreach($addons as $addon)
                                        <label class="flex cursor-pointer items-start gap-3 rounded-2xl border-2 p-3.5 transition" :class="addons.includes('{{ $addon->id }}') ? 'border-sun-500 bg-sun-50' : 'border-leaf-900/10 hover:border-leaf-300'">
                                            <input type="checkbox" name="addons[]" value="{{ $addon->id }}" x-model="addons" class="mt-1 size-4 accent-sun-500">
                                            <span>
                                                <span class="block text-sm font-semibold text-leaf-900">{{ $addon->tr('name') }}</span>
                                                @if($addon->tr('description'))<span class="block text-xs text-leaf-950/55">{{ $addon->tr('description') }}</span>@endif
                                                <span class="text-xs font-semibold text-leaf-700">{{ rupiah($addon->price) }}{{ $addon->unit === 'per_group' ? __('site.booking.per_group') : __('site.common.per_pax') }}</span>
                                            </span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- LANGKAH 3: Data pemesan --}}
                    <div x-show="step === 3" x-cloak>
                        <h2 class="text-2xl font-bold">3. {{ __('site.booking.step_contact') }}</h2>
                        <div class="mt-6 grid gap-5 sm:grid-cols-2">
                            <div>
                                <label class="label" for="customer_name">{{ __('site.booking.name') }}</label>
                                <input id="customer_name" name="customer_name" class="input" value="{{ old('customer_name') }}" required maxlength="120" autocomplete="name">
                            </div>
                            <div>
                                <label class="label" for="phone">{{ __('site.booking.phone') }}</label>
                                <input id="phone" name="phone" type="tel" class="input" value="{{ old('phone') }}" required maxlength="30" pattern="[0-9 +().\-]{8,30}" placeholder="08xx xxxx xxxx" autocomplete="tel">
                            </div>
                            <div>
                                <label class="label" for="email">{{ __('site.booking.email') }} <span class="font-normal text-leaf-950/45">({{ __('site.common.optional') }})</span></label>
                                <input id="email" name="email" type="email" class="input" value="{{ old('email') }}" maxlength="150" autocomplete="email">
                            </div>
                            <div>
                                <label class="label" for="organization">{{ __('site.booking.organization') }} <span class="font-normal text-leaf-950/45">({{ __('site.common.optional') }})</span></label>
                                <input id="organization" name="organization" class="input" value="{{ old('organization') }}" maxlength="150" autocomplete="organization">
                            </div>
                            <div class="sm:col-span-2">
                                <label class="label" for="group_type">{{ __('site.booking.group_type') }}</label>
                                <select id="group_type" name="group_type" class="input" required>
                                    @foreach(config('sukajadi.group_types') as $group)
                                        <option value="{{ $group }}" @selected(old('group_type', 'school') === $group)>{{ __('site.group_types.'.$group) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="sm:col-span-2">
                                <label class="label" for="notes">{{ __('site.booking.notes') }} <span class="font-normal text-leaf-950/45">({{ __('site.common.optional') }})</span></label>
                                <textarea id="notes" name="notes" rows="3" class="input" maxlength="2000" placeholder="{{ __('site.booking.notes_placeholder') }}">{{ old('notes') }}</textarea>
                            </div>
                        </div>
                    </div>

                    {{-- LANGKAH 4: Ringkasan --}}
                    <div x-show="step === 4" x-cloak>
                        <h2 class="text-2xl font-bold">4. {{ __('site.booking.step_review') }}</h2>
                        <p class="mt-2 text-leaf-950/65">{{ __('site.booking.estimate_note') }}</p>
                        <label class="mt-6 flex cursor-pointer items-start gap-3 rounded-2xl bg-cream p-4 text-sm">
                            <input type="checkbox" name="agree" value="1" class="mt-0.5 size-5 accent-leaf-600" @checked(old('agree')) required>
                            <span>{{ __('site.booking.agree') }}</span>
                        </label>
                    </div>

                    <p class="mt-6 rounded-xl bg-red-50 p-3 text-sm text-red-700" x-show="clientError" x-text="clientError" x-cloak role="alert"></p>

                    <div class="mt-8 flex items-center justify-between gap-3 border-t border-leaf-900/10 pt-6">
                        <button type="button" class="btn-outline" @click="prev()" x-show="step > 1" x-cloak><x-heroicon-m-arrow-left class="size-4" /> {{ __('site.booking.prev') }}</button>
                        <span x-show="step === 1"></span>
                        <button type="button" class="btn-green" @click="next()" x-show="step < 4">{{ __('site.booking.next') }} <x-heroicon-m-arrow-right class="size-4" /></button>
                        <button type="submit" class="btn-primary !px-7 !py-3" x-show="step === 4" x-cloak><x-heroicon-m-paper-airplane class="size-4" /> {{ __('site.booking.submit') }}</button>
                    </div>
                </div>

                {{-- Ringkasan harga --}}
                <aside class="lg:sticky lg:top-28 lg:self-start">
                    <div class="card p-6">
                        <h2 class="text-lg font-bold">{{ __('site.booking.summary') }}</h2>
                        <dl class="mt-4 space-y-3 text-sm">
                            <div>
                                <dt class="text-xs text-leaf-950/50">{{ __('site.booking.package') }}</dt>
                                <dd class="font-semibold text-leaf-900" x-text="packageLabel || '—'"></dd>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div><dt class="text-xs text-leaf-950/50">{{ __('site.booking.date') }}</dt><dd class="font-semibold" x-text="date ? new Date(date + 'T00:00:00').toLocaleDateString('{{ app()->getLocale() === 'en' ? 'en-GB' : 'id-ID' }}', { day: 'numeric', month: 'short', year: 'numeric' }) : '—'"></dd></div>
                                <div><dt class="text-xs text-leaf-950/50">{{ __('site.booking.pax') }}</dt><dd class="font-semibold" x-text="paxNumber || '—'"></dd></div>
                            </div>
                            <div class="space-y-2 border-t border-leaf-900/10 pt-3">
                                <div class="flex justify-between gap-3"><dt class="text-leaf-950/65"><span>{{ __('site.booking.subtotal') }}</span> <span class="text-xs" x-show="pricePerPax" x-text="'(' + money(pricePerPax) + ' × ' + paxNumber + ')'"></span></dt><dd class="font-semibold" x-text="money(subtotal)"></dd></div>
                                <template x-for="line in addonLines" :key="line.id">
                                    <div class="flex justify-between gap-3"><dt class="text-leaf-950/65" x-text="'+ ' + line.name"></dt><dd class="font-semibold" x-text="money(line.subtotal)"></dd></div>
                                </template>
                            </div>
                        </dl>
                        <div class="mt-4 rounded-2xl bg-leaf-50 p-4">
                            <p class="text-sm text-leaf-950/60">{{ __('site.booking.total') }}</p>
                            <p class="font-display text-3xl font-extrabold text-leaf-700" x-text="money(total)"></p>
                        </div>
                        <p class="mt-3 text-xs text-leaf-950/55">{{ __('site.booking.estimate_note') }}</p>
                    </div>
                </aside>
            </form>
        </div>
    </section>
@endsection
