<?php

namespace App\Models;

use App\Models\Concerns\HasImageVariants;
use App\Models\Concerns\HasTranslations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PartnerOffer extends Model
{
    use HasImageVariants, HasTranslations;

    public const UNITS = ['per_pax', 'per_ticket', 'per_night', 'per_unit', 'per_group'];

    protected $guarded = [];

    protected array $translatable = ['name', 'description'];

    protected array $imageColumns = ['image_path'];

    protected $casts = [
        'price' => 'integer',
        'min_qty' => 'integer',
        'is_dummy_price' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class);
    }

    public function isPerNight(): bool
    {
        return $this->unit === 'per_night';
    }

    /** Total harga untuk jumlah (orang/tiket/unit) dan malam tertentu. */
    public function totalFor(int $qty, ?int $nights = null): int
    {
        return $this->price * max(0, $qty) * ($this->isPerNight() ? max(1, (int) $nights) : 1);
    }

    public function unitLabel(?string $locale = null): string
    {
        return __('site.units.'.$this->unit, locale: $locale ?? app()->getLocale());
    }
}
