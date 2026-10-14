<?php

namespace App\Models;

use App\Models\Concerns\HasTranslations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PackageTier extends Model
{
    use HasTranslations;

    protected $guarded = [];

    protected array $translatable = ['name', 'duration', 'badge'];

    protected $casts = [
        // [{"id": "...", "en": "..."}, ...]
        'inclusions' => 'array',
        'is_dummy_price' => 'boolean',
        'price' => 'integer',
        'min_pax' => 'integer',
    ];

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    public function inclusionList(?string $locale = null): array
    {
        return collect($this->inclusions ?? [])
            ->map(fn ($item) => localized($item, $locale))
            ->filter()
            ->values()
            ->all();
    }
}
