@extends('layouts.app')

@section('title', __('site.booking.check_title'))

@push('head')
    <meta name="robots" content="noindex">
@endpush

@section('content')
    <x-page-hero :title="__('site.booking.check_title')" :subtitle="__('site.booking.check_subtitle')" label="Pemandu desa menyambut pengunjung" :breadcrumbs="[__('site.nav.check_booking') => null]" />

    <section class="py-12 sm:py-16">
        <div class="container-site max-w-2xl space-y-8">
            <form method="GET" action="{{ lroute('booking.check') }}" class="card grid gap-4 p-6 sm:grid-cols-[1fr_1fr_auto] sm:items-end sm:p-8">
                <div>
                    <label class="label" for="code">{{ __('site.booking.code') }}</label>
                    <input id="code" name="code" class="input uppercase" value="{{ request('code') }}" placeholder="SKJ-XXXXXX-XXXX" required>
                </div>
                <div>
                    <label class="label" for="phone">{{ __('site.booking.phone') }}</label>
                    <input id="phone" name="phone" type="tel" class="input" value="{{ request('phone') }}" placeholder="08xx xxxx xxxx" required>
                </div>
                <button class="btn-green !py-3"><x-heroicon-m-magnifying-glass class="size-4" /> {{ __('site.booking.check') }}</button>
            </form>

            @if($searched && ! $booking)
                <div class="flex gap-3 rounded-2xl bg-red-50 p-4 text-red-700 ring-1 ring-red-200" role="alert"><x-heroicon-s-exclamation-circle class="size-6 shrink-0" /> {{ __('site.booking.not_found') }}</div>
            @endif

            @if($booking)
                @php
                    $colors = ['new' => 'bg-sun-100 text-sun-700', 'confirmed' => 'bg-sky-soft/40 text-leaf-900', 'completed' => 'bg-leaf-100 text-leaf-800', 'cancelled' => 'bg-red-100 text-red-700'];
                @endphp
                <div class="card p-6 sm:p-8">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <p class="font-display text-2xl font-extrabold">{{ $booking->code }}</p>
                        <span class="chip px-4 py-1.5 text-sm {{ $colors[$booking->status] ?? '' }}">{{ $booking->statusLabel() }}</span>
                    </div>
                    <p class="mt-3 text-leaf-950/70">{{ __('site.status_help.'.$booking->status) }}</p>

                    {{-- Linimasa status --}}
                    @unless($booking->status === 'cancelled')
                        @php($order = ['new' => 1, 'confirmed' => 2, 'completed' => 3])
                        <ol class="mt-6 grid grid-cols-3 gap-2 text-center text-xs font-semibold">
                            @foreach($order as $status => $n)
                                <li class="rounded-xl p-2 {{ $order[$booking->status] >= $n ? 'bg-leaf-600 text-white' : 'bg-cream text-leaf-950/45' }}">{{ __('site.status.'.$status) }}</li>
                            @endforeach
                        </ol>
                    @endunless

                    <div class="mt-6">@include('booking.details', ['booking' => $booking])</div>

                    @if($booking->status === 'new')
                        <a href="{{ wa_track('booking', $booking->id, $booking->customerWhatsappMessage()) }}" target="_blank" rel="noopener" class="btn-wa mt-6 w-full !py-3"><x-icons.whatsapp /> {{ __('site.booking.continue_wa') }}</a>
                    @elseif($booking->status === 'completed')
                        <a href="{{ lroute('reviews') }}#tulis" class="btn-primary mt-6 w-full !py-3"><x-heroicon-o-star class="size-5" /> {{ __('site.booking.leave_review') }}</a>
                    @endif
                </div>
            @endif
        </div>
    </section>
@endsection
