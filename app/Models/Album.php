<?php

namespace App\Models;

use App\Models\Concerns\HasTranslations;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Album extends Model
{
    use HasTranslations;

    protected $guarded = [];

    protected array $translatable = ['title', 'description'];

    protected $casts = ['is_active' => 'boolean'];

    public function photos(): MorphMany
    {
        return $this->morphMany(Photo::class, 'photoable')->orderBy('sort');
    }

    public function coverPhoto(): MorphOne
    {
        return $this->morphOne(Photo::class, 'photoable')->orderBy('sort');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)->orderBy('sort');
    }

    public function url(?string $locale = null): string
    {
        return lroute('gallery.show', $this->slugFor($locale), $locale);
    }
}
