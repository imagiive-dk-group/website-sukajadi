@extends('layouts.app')

@section('title', __('site.faq.title'))
@section('description', __('site.faq.subtitle'))

@push('head')
    <script type="application/ld+json">{!! json_encode([
        '@context' => 'https://schema.org',
        '@type' => 'FAQPage',
        'mainEntity' => $faqs->flatten()->map(fn ($faq) => [
            '@type' => 'Question',
            'name' => $faq->tr('question'),
            'acceptedAnswer' => ['@type' => 'Answer', 'text' => $faq->tr('answer')],
        ])->values(),
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
@endpush

@section('content')
    <x-page-hero :title="__('site.faq.title')" :subtitle="__('site.faq.subtitle')" label="Pemandu desa menjawab pertanyaan pengunjung" :breadcrumbs="[__('site.nav.faq') => null]" />

    <section class="py-12 sm:py-16">
        <div class="container-site max-w-3xl space-y-10">
            @foreach($faqs as $category => $items)
                <div>
                    <h2 class="text-xl font-bold">{{ __('site.faq.categories.'.$category) }}</h2>
                    <div class="mt-4 space-y-3">
                        @foreach($items as $faq)
                            <div x-data="{ open: false }" class="card">
                                <button type="button" @click="open = !open" :aria-expanded="open" class="flex w-full items-center justify-between gap-4 p-5 text-left font-display font-semibold text-leaf-900">
                                    {{ $faq->tr('question') }}
                                    <x-heroicon-m-chevron-down class="size-5 shrink-0 transition" ::class="open && 'rotate-180'" />
                                </button>
                                <div x-show="open" x-collapse x-cloak>
                                    <p class="px-5 pb-5 text-leaf-950/70">{{ $faq->tr('answer') }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach

            <div class="rounded-3xl bg-leaf-700 p-8 text-center text-white">
                <p class="text-lg">{{ __('site.faq.subtitle') }}</p>
                <a href="{{ wa_link(null, __('site.contact.wa_default')) }}" target="_blank" rel="noopener" class="btn-wa mt-5"><x-icons.whatsapp /> {{ __('site.common.chat_whatsapp') }}</a>
            </div>
        </div>
    </section>
@endsection
