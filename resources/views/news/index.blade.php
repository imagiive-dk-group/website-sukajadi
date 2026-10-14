@extends('layouts.app')

@section('title', __('site.news.title'))
@section('description', __('site.news.subtitle'))

@section('content')
    <x-page-hero :title="__('site.news.title')" :subtitle="__('site.news.subtitle')" label="Kegiatan warga & pengunjung di balai desa" :breadcrumbs="[__('site.news.title') => null]" />

    <section class="py-12 sm:py-16">
        <div class="container-site">
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @forelse($posts as $post)
                    <x-post-card :post="$post" />
                @empty
                    <p class="text-leaf-950/60">{{ __('site.common.empty') }}</p>
                @endforelse
            </div>
            <div class="mt-10">{{ $posts->links() }}</div>
        </div>
    </section>
@endsection
