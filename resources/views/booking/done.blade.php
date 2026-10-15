@extends('layouts.app')

@section('title', __('site.booking.done_title'))

@push('head')
    <meta name="robots" content="noindex">
@endpush

@section('content')
    <x-page-hero :title="__('site.booking.done_title')" :subtitle="__('site.booking.done_text')" label="Rombongan bahagia di Desa Wisata Sukajadi" />

    <section class="py-12 sm:py-16">
        <div class="container-site max-w-2xl">
            <div class="card overflow-hidden">
                <div class="bg-leaf-700 p-6 text-center text-white sm:p-8">
                    <x-heroicon-o-check-badge class="mx-auto size-14 text-sun-300" />
                    <p class="mt-3 text-sm tracking-widest uppercase opacity-80">{{ __('site.booking.your_code') }}</p>
                    <p class="mt-1 font-display text-3xl font-extrabold tracking-wider select-all sm:text-4xl">{{ $booking->code }}</p>
                </div>
                <div class="p-6 sm:p-8">
                    @include('booking.details', ['booking' => $booking])
                    <a href="{{ wa_track('booking', $booking->id, $booking->customerWhatsappMessage()) }}" target="_blank" rel="noopener" class="btn-wa mt-8 w-full !py-4 !text-base">
                        <x-icons.whatsapp class="size-6" /> {{ __('site.booking.continue_wa') }}
                    </a>
                    <p class="mt-4 text-center text-sm text-leaf-950/60">{{ __('site.booking.check_later') }}</p>
                    <div class="mt-4 text-center">
                        <a href="{{ lroute('booking.check', ['code' => $booking->code]) }}" class="text-sm font-semibold text-leaf-700 underline">{{ __('site.nav.check_booking') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
