@extends('layouts.app')

@section('title', __('site.profile.title'))
@section('description', __('site.profile.subtitle'))

@section('content')
    <x-page-hero :title="__('site.profile.title')" :subtitle="__('site.profile.subtitle')" label="Kantor Desa Sukajadi / panorama desa" :breadcrumbs="[__('site.nav.about') => null, __('site.nav.profile') => null]" />

    <section class="py-12 sm:py-16">
        <div class="container-site">
            <div class="flex items-start gap-3 rounded-2xl bg-sky-soft/25 p-4 text-sm text-leaf-900 ring-1 ring-sky-soft/60">
                <x-heroicon-o-information-circle class="size-6 shrink-0 text-leaf-700" />
                <p>{{ __('site.profile.data_note') }}</p>
            </div>

            {{-- Data resmi per kelompok --}}
            <div class="mt-10 grid gap-6 lg:grid-cols-2">
                @foreach(config('sukajadi.profile_groups') as $group)
                    @continue(! isset($stats[$group]))
                    <div class="card p-6 sm:p-8">
                        <h2 class="flex items-center gap-2 text-xl font-bold">
                            <span class="h-6 w-1.5 rounded-full bg-sun-500"></span>{{ __('site.profile_groups.'.$group) }}
                        </h2>
                        <dl class="mt-5 divide-y divide-leaf-900/5">
                            @foreach($stats[$group] as $stat)
                                <div class="grid gap-1 py-3 sm:grid-cols-5 sm:gap-4">
                                    <dt class="text-sm text-leaf-950/60 sm:col-span-2">{{ $stat->tr('label') }}</dt>
                                    <dd class="sm:col-span-3">
                                        <span class="font-semibold text-leaf-900">{{ $stat->tr('value') }}</span>
                                        @if($stat->needs_verification)
                                            <span class="chip ml-1 bg-sun-50 px-2 py-0.5 text-[10px] text-sun-700">{{ __('site.common.needs_verification') }}</span>
                                        @endif
                                        @if($stat->source)
                                            <span class="mt-0.5 block text-xs text-leaf-950/50">{{ __('site.common.source') }}: {{ $stat->source }}@if($stat->source_year) ({{ $stat->source_year }})@endif</span>
                                        @endif
                                    </dd>
                                </div>
                            @endforeach
                        </dl>
                    </div>
                @endforeach
            </div>

            {{-- Bagian naratif --}}
            <div class="mt-12 grid gap-6 lg:grid-cols-2">
                @foreach($sections as $section)
                    <div class="card p-6 sm:p-8" id="{{ $section->key }}">
                        <h2 class="text-xl font-bold">{{ $section->tr('title') }}</h2>
                        <div class="prose-site mt-4">{!! $section->tr('body') !!}</div>
                    </div>
                @endforeach
            </div>

            {{-- Perangkat desa --}}
            @if($officials->isNotEmpty())
                <div class="mt-16">
                    <x-section-heading :title="__('site.profile.officials')" />
                    <div class="mt-8 grid grid-cols-2 gap-5 sm:grid-cols-3 lg:grid-cols-4">
                        @foreach($officials as $official)
                            <div class="card text-center">
                                <x-img :path="$official->photo_path" :label="__('site.common.photo').' '.$official->tr('position')" :alt="$official->name" ratio="1/1" compact />
                                <div class="p-4">
                                    <p class="font-display font-bold text-leaf-900">{{ $official->name }}</p>
                                    <p class="text-sm text-leaf-950/60">{{ $official->tr('position') }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </section>

    @include('partials.location')
@endsection
