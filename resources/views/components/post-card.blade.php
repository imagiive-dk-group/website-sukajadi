@props(['post'])
<article class="card-hover group flex flex-col">
    <a href="{{ $post->url() }}" class="block overflow-hidden">
        <x-img :path="$post->cover_path" :label="$post->placeholder_label" :alt="$post->tr('title')" ratio="16/10" class="transition duration-500 group-hover:scale-105" />
    </a>
    <div class="flex flex-1 flex-col p-5">
        @if($post->published_at)
            <time datetime="{{ $post->published_at->toDateString() }}" class="text-xs font-semibold text-sun-600">{{ $post->published_at->translatedFormat('j F Y') }}</time>
        @endif
        <h3 class="mt-1.5 text-lg leading-snug font-bold"><a href="{{ $post->url() }}" class="hover:text-leaf-600">{{ $post->tr('title') }}</a></h3>
        <p class="mt-2 line-clamp-3 text-sm text-leaf-950/65">{{ $post->tr('excerpt') }}</p>
        <a href="{{ $post->url() }}" class="mt-auto inline-flex items-center gap-1 pt-4 text-sm font-semibold text-leaf-700 hover:text-leaf-900">{{ __('site.common.read_more') }} <x-heroicon-m-arrow-right class="size-4" /></a>
    </div>
</article>
