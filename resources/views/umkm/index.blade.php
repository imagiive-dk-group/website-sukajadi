@extends('layouts.app')

@section('title', __('site.umkm.title'))
@section('description', __('site.umkm.subtitle'))

@section('content')
    <x-page-hero :title="__('site.umkm.title')" :subtitle="__('site.umkm.subtitle')" label="Pelaku UMKM Sukajadi di tempat usahanya" :breadcrumbs="[__('site.umkm.title') => null]">
        <a href="{{ lroute('products') }}" class="btn-primary mt-6"><x-heroicon-o-shopping-bag class="size-5" /> {{ __('site.nav.products') }}</a>
    </x-page-hero>

    <section class="py-12 sm:py-16">
        <div class="container-site">
            <x-category-filter :categories="config('sukajadi.umkm_categories')" :active="$category" route="umkm.index" labels="site.umkm_categories" />

            {{-- Overview UMKM satu per satu --}}
            <div class="mt-8 space-y-6">
                @forelse($umkms as $umkm)
                    <article class="card grid overflow-hidden md:grid-cols-5">
                        <a href="{{ $umkm->url() }}" class="relative block md:col-span-2">
                            <x-img :path="$umkm->cover_path" :label="$umkm->placeholder_label" :alt="$umkm->name" ratio="3/2" class="h-full md:absolute md:inset-0" />
                        </a>
                        <div class="flex flex-col p-6 sm:p-8 md:col-span-3">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="chip bg-leaf-50 text-leaf-800">{{ __('site.umkm_categories.'.$umkm->category) }}</span>
                                @if($umkm->accepts_visits)<span class="chip bg-sky-soft/30 text-leaf-900"><x-heroicon-o-user-group class="size-3.5" /> {{ __('site.umkm.accepts_visits') }}</span>@endif
                            </div>
                            <h2 class="mt-3 text-2xl font-bold"><a href="{{ $umkm->url() }}" class="hover:text-leaf-600">{{ $umkm->name }}</a></h2>
                            @if($umkm->owner_name)<p class="text-sm text-leaf-950/55">{{ __('site.umkm.owner') }}: {{ $umkm->owner_name }}</p>@endif
                            <p class="mt-3 text-leaf-950/70">{{ $umkm->tr('summary') }}</p>
                            <dl class="mt-4 grid gap-2 text-sm sm:grid-cols-2">
                                @if($umkm->tr('opening_hours'))
                                    <div class="flex gap-2"><x-heroicon-o-clock class="size-5 shrink-0 text-leaf-600" /><dd>{{ $umkm->tr('opening_hours') }}</dd></div>
                                @endif
                                @if($umkm->address)
                                    <div class="flex gap-2"><x-heroicon-o-map-pin class="size-5 shrink-0 text-leaf-600" /><dd>{{ $umkm->address }}</dd></div>
                                @endif
                                @if($umkm->hasPublicContact())
                                    <div class="flex gap-2"><x-icons.whatsapp class="size-5 shrink-0 text-[#25D366]" /><dd>{{ $umkm->whatsapp }}</dd></div>
                                @endif
                                @if($umkm->show_contact && $umkm->instagram)
                                    <div class="flex gap-2"><x-icons.instagram class="size-5 shrink-0 text-sun-600" /><dd><a href="{{ instagram_url($umkm->instagram) }}" target="_blank" rel="noopener" class="hover:underline">{{ '@'.ltrim($umkm->instagram, '@') }}</a></dd></div>
                                @endif
                            </dl>
                            <div class="mt-auto flex flex-wrap gap-2 pt-6">
                                <a href="{{ $umkm->whatsappLink() }}" target="_blank" rel="noopener" class="btn-wa"><x-icons.whatsapp class="size-4" /> {{ __('site.umkm.whatsapp') }}</a>
                                <a href="{{ $umkm->url() }}" class="btn-outline">{{ __('site.umkm.visit_umkm') }} <x-heroicon-m-arrow-right class="size-4" /></a>
                            </div>
                        </div>
                    </article>
                @empty
                    <p class="text-leaf-950/60">{{ __('site.common.empty') }}</p>
                @endforelse
            </div>
        </div>
    </section>
@endsection
