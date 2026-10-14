<?php

namespace App\Models;

use App\Models\Concerns\HasImageVariants;
use App\Models\Concerns\HasTranslations;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Attraction extends Model
{
    use HasImageVariants, HasTranslations;

    protected $guarded = [];

    protected array $translatable = ['name', 'summary', 'description'];

    protected array $imageColumns = ['cover_path'];

    protected $casts = [
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'custom_price' => 'integer',
    ];

    public function packages(): BelongsToMany
    {
        return $this->belongsToMany(Package::class);
    }

    public function photos(): MorphMany
    {
        return $this->morphMany(Photo::class, 'photoable')->orderBy('sort');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)->orderBy('sort');
    }

    public function url(?string $locale = null): string
    {
        return lroute('attractions.show', $this->slugFor($locale), $locale);
    }
}
