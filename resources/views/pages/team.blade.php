@extends('layouts.app')

@section('title', __('site.team.title'))
@section('description', __('site.team.subtitle'))

@section('content')
    <x-page-hero :title="__('site.team.title')" :subtitle="__('site.team.subtitle')" label="Foto bersama tim penggiat desa wisata" :breadcrumbs="[__('site.nav.about') => null, __('site.nav.team') => null]" />

    <section class="py-12 sm:py-16">
        <div class="container-site">
            <div class="grid grid-cols-2 gap-5 sm:grid-cols-3 lg:grid-cols-4">
                @foreach($team as $member)
                    <figure class="card">
                        <x-img :path="$member->photo_path" :label="__('site.common.photo').': '.$member->tr('position')" :alt="$member->name" ratio="4/5" />
                        <figcaption class="p-4">
                            <span class="block font-display font-bold text-leaf-900">{{ $member->name }}</span>
                            <span class="text-sm text-leaf-950/60">{{ $member->tr('position') }}</span>
                        </figcaption>
                    </figure>
                @endforeach
            </div>
            <p class="mt-10 max-w-2xl text-leaf-950/70">{{ setting_tr('managed_by') }}.</p>
        </div>
    </section>

    @include('partials.cta')
@endsection
