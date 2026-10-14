<?php

namespace App\Models;

use App\Models\Concerns\HasImageVariants;
use App\Models\Concerns\HasTranslations;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Package extends Model
{
    use HasImageVariants, HasTranslations;

    protected $guarded = [];

    protected array $translatable = ['name', 'summary', 'description', 'duration'];

    protected array $imageColumns = ['cover_path'];

    protected $casts = [
        'itinerary' => 'array',
        'facilities' => 'array',
        'target_groups' => 'array',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function tiers(): HasMany
    {
        return $this->hasMany(PackageTier::class)->orderBy('sort')->orderBy('price');
    }

    public function attractions(): BelongsToMany
    {
        return $this->belongsToMany(Attraction::class);
    }

    public function umkms(): BelongsToMany
    {
        return $this->belongsToMany(Umkm::class);
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
        return $this->tiers->min('price');
    }

    public function minPax(): int
    {
        return (int) ($this->tiers->min('min_pax') ?? 1);
    }

    public function url(?string $locale = null): string
    {
        return lroute('packages.show', $this->slugFor($locale), $locale);
    }
}
