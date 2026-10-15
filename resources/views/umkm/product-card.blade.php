<article class="card flex flex-col">
    <x-img :path="$product->image_path" :label="$product->placeholder_label" :alt="$product->tr('name')" ratio="4/3" sizes="(min-width: 1024px) 25vw, 50vw" />
    <div class="flex flex-1 flex-col p-4 sm:p-5">
        @if($showUmkm ?? true)
            <a href="{{ $umkm->url() }}" class="text-xs font-semibold text-sun-600 hover:underline">{{ __('site.umkm.by', ['name' => $umkm->name]) }}</a>
        @endif
        <h3 class="mt-1 font-display text-base font-bold leading-snug">{{ $product->tr('name') }}</h3>
        @if($product->tr('description'))
            <p class="mt-1 line-clamp-2 text-sm text-leaf-950/60">{{ $product->tr('description') }}</p>
        @endif
        @if($price = $product->priceLabel())
            <p class="mt-2 font-display font-bold text-leaf-700">{{ $price }}</p>
        @endif
        <div class="mt-auto pt-4">
            <a href="{{ $umkm->whatsappLink(__('site.umkm.wa_product', ['product' => $product->tr('name'), 'name' => $umkm->name]), 'product', $product->id) }}" target="_blank" rel="noopener" class="btn-wa w-full !py-2 text-xs sm:text-sm">
                <x-icons.whatsapp class="size-4" /> {{ __('site.umkm.order') }}
            </a>
        </div>
    </div>
</article>
