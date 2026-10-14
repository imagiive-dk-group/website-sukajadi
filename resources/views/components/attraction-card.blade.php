@props(['attraction'])
<a href="{{ $attraction->url() }}" class="group relative block overflow-hidden rounded-3xl bg-leaf-900 shadow-sm">
    <x-img :path="$attraction->cover_path" :label="$attraction->placeholder_label" :alt="$attraction->tr('name')" ratio="4/5" class="transition duration-700 group-hover:scale-105" />
    <div class="pointer-events-none absolute inset-x-0 bottom-0 bg-gradient-to-t from-leaf-950/90 via-leaf-950/50 to-transparent p-5 pt-20">
        <span class="chip bg-white/15 text-white backdrop-blur">{{ __('site.categories.'.$attraction->category) }}</span>
        <h3 class="mt-2 text-lg leading-snug font-bold !text-white">{{ $attraction->tr('name') }}</h3>
        <p class="mt-1 line-clamp-2 text-sm text-white/75">{{ $attraction->tr('summary') }}</p>
    </div>
</a>
