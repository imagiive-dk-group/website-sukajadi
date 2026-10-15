<?php

namespace App\Models;

use App\Models\Concerns\HasImageVariants;
use App\Models\Concerns\HasTranslations;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * Mitra bisnis (wisata sekitar, penginapan/vila, kafe) yang dipasarkan
 * lewat website dengan komisi per penjualan.
 */
class Partner extends Model
{
    use HasImageVariants, HasTranslations;

    protected $guarded = [];

    protected array $translatable = ['summary', 'description', 'opening_hours'];

    protected array $imageColumns = ['cover_path'];

    protected $casts = [
        // [{"id": "...", "en": "..."}, ...]
        'highlights' => 'array',
        'commission_rate' => 'float',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function offers(): HasMany
    {
        return $this->hasMany(PartnerOffer::class)->orderBy('sort')->orderBy('price');
    }

    public function photos(): MorphMany
    {
        return $this->morphMany(Photo::class, 'photoable')->orderBy('sort');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)->orderBy('sort');
    }

    public function startingPrice(): ?int
    {
        return $this->offers->where('is_active', true)->min('price');
    }

    public function highlightList(?string $locale = null): array
    {
        return collect($this->highlights ?? [])->map(fn ($h) => localized($h, $locale))->filter()->values()->all();
    }

    public function url(?string $locale = null): string
    {
        return lroute('partners.show', $this->slugFor($locale), $locale);
    }
}
