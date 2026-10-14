@extends('layouts.app')

@section('title', '404')

@section('content')
    <section class="relative isolate flex min-h-[80vh] items-center overflow-hidden bg-leaf-900 pt-28 pb-16 text-white">
        <div class="absolute inset-0 -z-10"><x-placeholder fill dark /></div>
        <div class="container-site text-center">
            <p class="font-display text-8xl font-extrabold text-sun-400">404</p>
            <h1 class="mt-4 text-3xl font-bold !text-white">{{ app()->getLocale() === 'en' ? 'Oops, this page got lost in the rice fields.' : 'Ups, halaman ini tersesat di sawah.' }}</h1>
            <div class="mt-8 flex flex-wrap justify-center gap-3">
                <a href="{{ lroute('home') }}" class="btn-primary">{{ __('site.nav.home') }}</a>
                <a href="{{ lroute('packages.index') }}" class="btn-ghost-light">{{ __('site.nav.packages') }}</a>
            </div>
        </div>
    </section>
@endsection
