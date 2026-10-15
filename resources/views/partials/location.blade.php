<section class="py-16 sm:py-24">
    <div class="container-site grid items-center gap-10 lg:grid-cols-5">
        <div class="lg:col-span-2">
            <x-section-heading :eyebrow="__('site.home.location_eyebrow')" :title="__('site.home.location_title')" />
            <ul class="mt-6 space-y-4 text-sm">
                <li class="flex gap-3"><x-heroicon-o-map-pin class="size-6 shrink-0 text-leaf-600" /> <span>{{ setting_tr('address') }}</span></li>
                <li class="flex gap-3"><x-heroicon-o-clock class="size-6 shrink-0 text-leaf-600" /> <span>{{ setting_tr('opening_hours') }}</span></li>
                <li class="flex gap-3"><x-icons.whatsapp class="size-6 shrink-0 text-[#25D366]" /> <a href="{{ wa_track('general', null, __('site.contact.wa_default')) }}" target="_blank" rel="noopener" class="font-semibold hover:text-leaf-600">{{ setting('whatsapp') }}</a></li>
            </ul>
            <a href="{{ setting('maps_link') }}" target="_blank" rel="noopener" class="btn-green mt-6"><x-heroicon-o-map class="size-5" /> {{ __('site.common.open_maps') }}</a>
        </div>
        <div class="overflow-hidden rounded-3xl bg-leaf-100 shadow-sm ring-1 ring-leaf-900/5 lg:col-span-3">
            <iframe src="{{ setting('maps_embed_url') }}" class="aspect-[4/3] w-full sm:aspect-[16/10]" loading="lazy" referrerpolicy="no-referrer-when-downgrade" title="Google Maps — {{ setting('site_name') }}"></iframe>
        </div>
    </div>
</section>
