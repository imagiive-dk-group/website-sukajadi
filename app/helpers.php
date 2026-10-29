<?php

use App\Models\Setting;
use Illuminate\Support\Facades\Route;

if (! function_exists('localized')) {
    /**
     * Ambil teks sesuai bahasa aktif dari nilai {"id": ..., "en": ...}.
     * Jatuh ke Bahasa Indonesia bila versi bahasa aktif kosong.
     */
    function localized(mixed $value, ?string $locale = null): mixed
    {
        if (! is_array($value)) {
            return $value;
        }

        $locale ??= app()->getLocale();

        foreach ([$locale, 'id', 'en'] as $key) {
            if (array_key_exists($key, $value) && filled($value[$key])) {
                return $value[$key];
            }
        }

        return null;
    }
}

if (! function_exists('lroute')) {
    /**
     * Nama route tanpa prefix bahasa, misal lroute('packages.show', $package).
     */
    function lroute(string $name, mixed $parameters = [], ?string $locale = null, bool $absolute = true): string
    {
        $locale ??= app()->getLocale();

        return route($locale.'.'.$name, $parameters, $absolute);
    }
}

if (! function_exists('setting')) {
    function setting(string $key, mixed $default = null): mixed
    {
        return Setting::get($key, $default);
    }
}

if (! function_exists('setting_tr')) {
    function setting_tr(string $key, ?string $default = null): ?string
    {
        return localized(Setting::get($key)) ?? $default;
    }
}

if (! function_exists('rupiah')) {
    function rupiah(int|float|null $amount): string
    {
        return 'Rp'.number_format((float) $amount, 0, ',', '.');
    }
}

if (! function_exists('wa_number')) {
    /** Ubah 0857.. / +62 857-.. menjadi 62857.. untuk tautan wa.me. */
    function wa_number(?string $number): ?string
    {
        if (blank($number)) {
            return null;
        }

        $digits = preg_replace('/\D+/', '', $number);
        if (str_starts_with($digits, '0')) {
            $digits = '62'.substr($digits, 1);
        }

        return $digits;
    }
}

if (! function_exists('wa_link')) {
    function wa_link(?string $number = null, ?string $text = null): string
    {
        $number = wa_number($number ?? setting('whatsapp'));
        $url = 'https://wa.me/'.$number;

        return filled($text) ? $url.'?text='.rawurlencode($text) : $url;
    }
}

if (! function_exists('wa_track')) {
    /**
     * Tautan WhatsApp lewat /wa agar setiap klik tercatat untuk monitoring,
     * lalu diteruskan ke wa.me. $number kosong = WhatsApp admin desa.
     */
    function wa_track(string $type = 'general', ?int $id = null, ?string $text = null, ?string $number = null): string
    {
        return route('wa', array_filter([
            'c' => $type,
            'i' => $id,
            'n' => wa_number($number),
            't' => $text,
            'l' => app()->getLocale(),
        ], fn ($v) => $v !== null && $v !== ''));
    }
}

if (! function_exists('tiktok_url')) {
    function tiktok_url(?string $handle): ?string
    {
        if (blank($handle)) {
            return null;
        }

        return 'https://www.tiktok.com/@'.ltrim(trim($handle), '@');
    }
}

if (! function_exists('instagram_url')) {
    function instagram_url(?string $handle): ?string
    {
        if (blank($handle)) {
            return null;
        }

        return 'https://instagram.com/'.ltrim(trim($handle), '@');
    }
}

if (! function_exists('alternate_locale_url')) {
    /**
     * URL halaman yang sama dalam bahasa lain (untuk tombol ID | EN dan hreflang).
     */
    function alternate_locale_url(string $locale): string
    {
        $alternates = view()->shared('alternateUrls', []);
        if (isset($alternates[$locale])) {
            return $alternates[$locale];
        }

        $route = Route::current();
        $name = $route?->getName();
        if ($name && preg_match('/^(id|en)\.(.+)$/', $name, $m) && Route::has($locale.'.'.$m[2])) {
            return route($locale.'.'.$m[2], $route->parameters()).(request()->getQueryString() ? '?'.request()->getQueryString() : '');
        }

        return route($locale.'.home');
    }
}
