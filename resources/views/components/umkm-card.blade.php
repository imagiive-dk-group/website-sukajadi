@props(['umkm'])
<article class="card-hover group flex flex-col">
    <a href="{{ $umkm->url() }}" class="relative block overflow-hidden">
        <x-img :path="$umkm->cover_path" :label="$umkm->placeholder_label" :alt="$umkm->name" ratio="3/2" class="transition duration-500 group-hover:scale-105" />
        <span class="chip absolute top-3 left-3 bg-white/90 text-leaf-800 backdrop-blur">{{ __('site.umkm_categories.'.$umkm->category) }}</span>
    </a>
    <div class="flex flex-1 flex-col p-5">
        <h3 class="text-lg font-bold"><a href="{{ $umkm->url() }}" class="hover:text-leaf-600">{{ $umkm->name }}</a></h3>
        <p class="mt-1.5 line-clamp-2 text-sm text-leaf-950/65">{{ $umkm->tr('summary') }}</p>
        <div class="mt-auto flex flex-wrap items-center gap-2 pt-4">
            <a href="{{ $umkm->whatsappLink() }}" target="_blank" rel="noopener" class="btn-wa !px-4 !py-2 text-xs">
                <x-icons.whatsapp class="size-4" /> {{ __('site.umkm.whatsapp') }}
            </a>
            <a href="{{ $umkm->url() }}" class="btn-outline !px-4 !py-2 text-xs">{{ __('site.common.see_detail') }}</a>
        </div>
    </div>
</article>
