<?php

namespace App\Models;

use App\Models\Concerns\HasImageVariants;
use App\Models\Concerns\HasTranslations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UmkmProduct extends Model
{
    use HasImageVariants, HasTranslations;

    protected $guarded = [];

    protected array $translatable = ['name', 'description', 'unit'];

    protected array $imageColumns = ['image_path'];

    protected $casts = [
        'is_active' => 'boolean',
        'price_min' => 'integer',
        'price_max' => 'integer',
    ];

    public function umkm(): BelongsTo
    {
        return $this->belongsTo(Umkm::class);
    }

    public function priceLabel(): ?string
    {
        if (! $this->price_min) {
            return null;
        }

        $label = rupiah($this->price_min);
        if ($this->price_max && $this->price_max > $this->price_min) {
            $label .= ' – '.rupiah($this->price_max);
        }

        $unit = $this->tr('unit');

        return $unit ? $label.' / '.$unit : $label;
    }
}
