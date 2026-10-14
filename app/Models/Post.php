<?php

namespace App\Models;

use App\Models\Concerns\HasImageVariants;
use App\Models\Concerns\HasTranslations;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasImageVariants, HasTranslations;

    protected $guarded = [];

    protected array $translatable = ['title', 'excerpt', 'body'];

    protected array $imageColumns = ['cover_path'];

    protected $casts = [
        'is_published' => 'boolean',
        'published_at' => 'datetime',
    ];

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true)
            ->where(fn ($q) => $q->whereNull('published_at')->orWhere('published_at', '<=', now()))
            ->orderByDesc('published_at');
    }

    public function url(?string $locale = null): string
    {
        return lroute('news.show', $this->slugFor($locale), $locale);
    }
}
