<section class="pb-20">
    <div class="container-site">
        <div class="relative isolate overflow-hidden rounded-[2rem] bg-gradient-to-br from-sun-500 to-sun-700 px-6 py-12 text-center text-white sm:px-12 sm:py-16">
            <div class="pointer-events-none absolute -bottom-20 -left-16 -z-10 size-72 rounded-full bg-white/10"></div>
            <div class="pointer-events-none absolute -top-16 -right-10 -z-10 size-56 rounded-full bg-white/10"></div>
            <h2 class="font-hand text-5xl !text-white sm:text-6xl">{{ __('site.home.cta_title') }}</h2>
            <p class="mx-auto mt-3 max-w-xl text-white/90">{{ __('site.home.cta_text') }}</p>
            <div class="mt-8 flex flex-wrap justify-center gap-3">
                <a href="{{ lroute('booking.create') }}" class="btn !bg-white !px-7 !py-3.5 !text-base !text-sun-700 hover:!bg-sun-50">{{ __('site.nav.book_now') }}</a>
                <a href="{{ wa_link(null, __('site.contact.wa_default')) }}" target="_blank" rel="noopener" class="btn-ghost-light !px-7 !py-3.5 !text-base"><x-icons.whatsapp /> {{ __('site.common.chat_whatsapp') }}</a>
            </div>
        </div>
    </div>
</section>
