<footer class="relative mt-auto bg-leaf-900 text-white/80">
    <div class="container-site grid gap-10 py-14 sm:grid-cols-2 lg:grid-cols-4">
        <div class="sm:col-span-2 lg:col-span-1">
            <a href="{{ lroute('home') }}" class="flex items-center gap-3">
                <img src="{{ asset('images/logo.webp') }}" alt="" class="size-16" width="64" height="64" loading="lazy">
                <span class="font-display text-xl leading-tight font-extrabold text-white">Desa Wisata<br><span class="text-sun-400">Sukajadi</span></span>
            </a>
            <p class="mt-4 text-sm leading-relaxed">{{ __('site.footer.about') }}</p>
            <p class="mt-4 font-hand text-2xl text-sun-300">{{ setting_tr('motto') }}</p>
        </div>

        <div>
            <h2 class="font-display text-sm font-bold tracking-widest !text-white uppercase">{{ __('site.footer.explore') }}</h2>
            <ul class="mt-4 space-y-2.5 text-sm">
                @foreach(['packages.index' => 'packages', 'custom' => 'custom', 'attractions.index' => 'attractions', 'umkm.index' => 'umkm', 'products' => 'products', 'gallery.index' => 'gallery'] as $route => $key)
                    <li><a href="{{ lroute($route) }}" class="hover:text-white">{{ __('site.nav.'.$key) }}</a></li>
                @endforeach
            </ul>
        </div>

        <div>
            <h2 class="font-display text-sm font-bold tracking-widest !text-white uppercase">{{ __('site.footer.info') }}</h2>
            <ul class="mt-4 space-y-2.5 text-sm">
                @foreach(['profile' => 'profile', 'about' => 'tourism_village', 'news.index' => 'news', 'reviews' => 'reviews', 'faq' => 'faq', 'booking.check' => 'check_booking'] as $route => $key)
                    <li><a href="{{ lroute($route) }}" class="hover:text-white">{{ __('site.nav.'.$key) }}</a></li>
                @endforeach
            </ul>
        </div>

        <div>
            <h2 class="font-display text-sm font-bold tracking-widest !text-white uppercase">{{ __('site.footer.contact') }}</h2>
            <ul class="mt-4 space-y-3 text-sm">
                <li>
                    <a href="{{ wa_link(null, __('site.contact.wa_default')) }}" target="_blank" rel="noopener" class="flex items-start gap-3 hover:text-white">
                        <x-icons.whatsapp class="mt-0.5 size-5 shrink-0 text-[#25D366]" /> {{ setting('whatsapp') }}
                    </a>
                </li>
                @if(setting('instagram'))
                    <li>
                        <a href="{{ instagram_url(setting('instagram')) }}" target="_blank" rel="noopener" class="flex items-start gap-3 hover:text-white">
                            <x-icons.instagram class="mt-0.5 size-5 shrink-0 text-sun-300" /> {{ '@'.ltrim(setting('instagram'), '@') }}
                        </a>
                    </li>
                @endif
                @if(setting('email'))
                    <li>
                        <a href="mailto:{{ setting('email') }}" class="flex items-start gap-3 break-all hover:text-white">
                            <x-heroicon-o-envelope class="mt-0.5 size-5 shrink-0 text-sky-soft" /> {{ setting('email') }}
                        </a>
                    </li>
                @endif
                <li class="flex items-start gap-3">
                    <x-heroicon-o-map-pin class="mt-0.5 size-5 shrink-0 text-sun-300" />
                    <a href="{{ setting('maps_link') }}" target="_blank" rel="noopener" class="hover:text-white">{{ setting_tr('address') }}</a>
                </li>
            </ul>
        </div>
    </div>
    <div class="border-t border-white/10">
        <div class="container-site flex flex-col gap-2 py-5 text-xs text-white/55 sm:flex-row sm:items-center sm:justify-between">
            <p>© {{ now()->year }} {{ setting('site_name', 'Desa Wisata Sukajadi') }}. {{ __('site.footer.rights') }}</p>
            <p>{{ __('site.footer.support') }}</p>
        </div>
    </div>
</footer>
