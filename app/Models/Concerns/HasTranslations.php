<?php

namespace App\Models\Concerns;

/**
 * Kolom dua bahasa disimpan sebagai JSON {"id": "...", "en": "..."}.
 * Jika versi bahasa aktif kosong, otomatis memakai Bahasa Indonesia.
 */
trait HasTranslations
{
    public function initializeHasTranslations(): void
    {
        foreach ($this->translatable ?? [] as $field) {
            $this->mergeCasts([$field => 'array']);
        }
    }

    public function tr(string $field, ?string $locale = null): mixed
    {
        $value = $this->getAttribute($field);

        return localized($value, $locale);
    }

    public function slugFor(?string $locale = null): string
    {
        $locale ??= app()->getLocale();

        return $locale === 'en' && filled($this->slug_en) ? $this->slug_en : $this->slug;
    }

    public function scopeWhereSlug($query, string $slug, ?string $locale = null)
    {
        $locale ??= app()->getLocale();

        return $query->where($locale === 'en' ? 'slug_en' : 'slug', $slug);
    }
}
