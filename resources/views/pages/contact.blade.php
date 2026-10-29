@extends('layouts.app')

@section('title', __('site.contact.title'))
@section('description', __('site.contact.subtitle'))

@section('content')
    <x-page-hero :title="__('site.contact.title')" :subtitle="__('site.contact.subtitle')" label="Jalan masuk Desa Sukajadi" :breadcrumbs="[__('site.nav.contact') => null]" />

    <section class="py-12 sm:py-16">
        <div class="container-site grid gap-8 lg:grid-cols-5">
            <div class="space-y-4 lg:col-span-2">
                <a href="{{ wa_track('general', null, __('site.contact.wa_default')) }}" target="_blank" rel="noopener" class="card-hover flex items-center gap-4 p-5">
                    <span class="flex size-12 items-center justify-center rounded-2xl bg-[#25D366] text-white"><x-icons.whatsapp class="size-6" /></span>
                    <span><span class="block text-xs text-leaf-950/55">{{ __('site.contact.whatsapp') }}</span><span class="font-display text-lg font-bold text-leaf-900">{{ setting('whatsapp') }}</span></span>
                </a>
                @if(setting('instagram'))
                    <a href="{{ instagram_url(setting('instagram')) }}" target="_blank" rel="noopener" class="card-hover flex items-center gap-4 p-5">
                        <span class="flex size-12 items-center justify-center rounded-2xl bg-gradient-to-br from-sun-400 to-pink-600 text-white"><x-icons.instagram class="size-6" /></span>
                        <span><span class="block text-xs text-leaf-950/55">{{ __('site.contact.instagram') }}</span><span class="font-display text-lg font-bold text-leaf-900">{{ '@'.ltrim(setting('instagram'), '@') }}</span></span>
                    </a>
                @endif
                @if(setting('tiktok'))
                    <a href="{{ tiktok_url(setting('tiktok')) }}" target="_blank" rel="noopener" class="card-hover flex items-center gap-4 p-5">
                        <span class="flex size-12 items-center justify-center rounded-2xl bg-leaf-950 text-white"><x-icons.tiktok class="size-6" /></span>
                        <span><span class="block text-xs text-leaf-950/55">{{ __('site.contact.tiktok') }}</span><span class="font-display text-lg font-bold text-leaf-900">{{ '@'.ltrim(setting('tiktok'), '@') }}</span></span>
                    </a>
                @endif
                @if(setting('email'))
                    <a href="mailto:{{ setting('email') }}" class="card-hover flex items-center gap-4 p-5">
                        <span class="flex size-12 shrink-0 items-center justify-center rounded-2xl bg-sky-soft text-leaf-900"><x-heroicon-o-envelope class="size-6" /></span>
                        <span class="min-w-0"><span class="block text-xs text-leaf-950/55">{{ __('site.contact.email') }}</span><span class="block truncate font-display text-lg font-bold text-leaf-900">{{ setting('email') }}</span></span>
                    </a>
                @endif
                <div class="card flex items-start gap-4 p-5">
                    <span class="flex size-12 shrink-0 items-center justify-center rounded-2xl bg-leaf-50 text-leaf-700"><x-heroicon-o-map-pin class="size-6" /></span>
                    <span><span class="block text-xs text-leaf-950/55">{{ __('site.contact.address') }}</span><span class="font-medium">{{ setting_tr('address') }}</span>
                        <span class="mt-2 block text-xs text-leaf-950/55">{{ __('site.contact.hours') }}</span><span class="font-medium">{{ setting_tr('opening_hours') }}</span></span>
                </div>
            </div>

            <div class="card p-6 sm:p-8 lg:col-span-3">
                <h2 class="text-2xl font-bold">{{ __('site.contact.form_title') }}</h2>
                @if(session('status'))
                    <div class="mt-4 flex gap-3 rounded-2xl bg-leaf-50 p-4 text-leaf-800" role="status"><x-heroicon-s-check-circle class="size-6 shrink-0 text-leaf-600" /> {{ session('status') }}</div>
                @endif
                <form method="POST" action="{{ lroute('contact.send') }}" class="mt-6 space-y-5">
                    @csrf
                    <input type="text" name="website" class="hidden" tabindex="-1" autocomplete="off" aria-hidden="true">
                    <div class="grid gap-5 sm:grid-cols-2">
                        <div>
                            <label class="label" for="name">{{ __('site.contact.name') }}</label>
                            <input id="name" name="name" class="input" value="{{ old('name') }}" required maxlength="120">
                            @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="label" for="contact">{{ __('site.contact.contact') }}</label>
                            <input id="contact" name="contact" class="input" value="{{ old('contact') }}" required maxlength="150">
                            @error('contact')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                    </div>
                    <div>
                        <label class="label" for="message">{{ __('site.contact.message') }}</label>
                        <textarea id="message" name="message" rows="5" class="input" required maxlength="3000">{{ old('message') }}</textarea>
                        @error('message')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <button class="btn-green !py-3">{{ __('site.common.send') }} <x-heroicon-m-paper-airplane class="size-4" /></button>
                </form>
            </div>
        </div>
    </section>

    <section class="pb-16">
        <div class="container-site">
            <div class="overflow-hidden rounded-3xl bg-leaf-100 shadow-sm ring-1 ring-leaf-900/5">
                <iframe src="{{ setting('maps_embed_url') }}" class="aspect-[4/3] w-full sm:aspect-[21/9]" loading="lazy" referrerpolicy="no-referrer-when-downgrade" title="Google Maps — {{ setting('site_name') }}"></iframe>
            </div>
        </div>
    </section>
@endsection
