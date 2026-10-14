@extends('layouts.app')

@section('title', __('site.reviews.title'))
@section('description', __('site.reviews.subtitle'))

@section('content')
    <x-page-hero :title="__('site.reviews.title')" :subtitle="__('site.reviews.subtitle')" label="Rombongan pengunjung tersenyum setelah kegiatan" :breadcrumbs="[__('site.nav.reviews') => null]" />

    <section class="py-12 sm:py-16">
        <div class="container-site grid gap-10 lg:grid-cols-3">
            <div class="lg:col-span-2">
                <div class="grid gap-5 sm:grid-cols-2">
                    @forelse($testimonials as $testimonial)
                        @include('partials.testimonial', ['testimonial' => $testimonial])
                    @empty
                        <p class="text-leaf-950/60">{{ __('site.reviews.empty') }}</p>
                    @endforelse
                </div>
                <div class="mt-8">{{ $testimonials->links() }}</div>
            </div>

            <aside class="lg:sticky lg:top-28 lg:self-start">
                <div class="card p-6" id="tulis">
                    <h2 class="text-xl font-bold">{{ __('site.reviews.form_title') }}</h2>
                    @if(session('status'))
                        <div class="mt-4 flex gap-3 rounded-2xl bg-leaf-50 p-4 text-sm text-leaf-800" role="status"><x-heroicon-s-check-circle class="size-5 shrink-0 text-leaf-600" /> {{ session('status') }}</div>
                    @endif
                    <form method="POST" action="{{ lroute('reviews.store') }}#tulis" class="mt-5 space-y-4" x-data="{ rating: {{ (int) old('rating', 5) }} }">
                        @csrf
                        <input type="text" name="website" class="hidden" tabindex="-1" autocomplete="off" aria-hidden="true">
                        <div>
                            <label class="label" for="r-name">{{ __('site.reviews.name') }}</label>
                            <input id="r-name" name="name" class="input" value="{{ old('name') }}" required maxlength="120">
                            @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="label" for="r-org">{{ __('site.reviews.organization') }} <span class="font-normal text-leaf-950/45">({{ __('site.common.optional') }})</span></label>
                            <input id="r-org" name="organization" class="input" value="{{ old('organization') }}" maxlength="150">
                        </div>
                        <div>
                            <span class="label">{{ __('site.reviews.rating') }}</span>
                            <input type="hidden" name="rating" :value="rating">
                            <div class="flex gap-1">
                                @for($i = 1; $i <= 5; $i++)
                                    <button type="button" @click="rating = {{ $i }}" class="text-sun-500" aria-label="{{ $i }}/5">
                                        <x-heroicon-s-star class="size-8 transition" ::class="rating >= {{ $i }} ? 'opacity-100' : 'opacity-25'" />
                                    </button>
                                @endfor
                            </div>
                        </div>
                        <div>
                            <label class="label" for="r-content">{{ __('site.reviews.content') }}</label>
                            <textarea id="r-content" name="content" rows="4" class="input" required minlength="10" maxlength="1500">{{ old('content') }}</textarea>
                            @error('content')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <button class="btn-green w-full !py-3">{{ __('site.common.send') }}</button>
                    </form>
                </div>
            </aside>
        </div>
    </section>
@endsection
