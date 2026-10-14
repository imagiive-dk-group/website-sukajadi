<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Booking extends Model
{
    protected $guarded = [];

    protected $casts = [
        'visit_date' => 'date',
        'addons' => 'array',
        'custom_items' => 'array',
        'confirmed_at' => 'datetime',
        'pax' => 'integer',
        'price_per_pax' => 'integer',
        'subtotal' => 'integer',
        'addons_total' => 'integer',
        'total' => 'integer',
    ];

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    public function tier(): BelongsTo
    {
        return $this->belongsTo(PackageTier::class, 'package_tier_id');
    }

    /** Booking yang masih memakai kuota (belum dibatalkan). */
    public function scopeHoldingCapacity(Builder $query): Builder
    {
        return $query->where('status', '!=', 'cancelled');
    }

    public static function generateCode(): string
    {
        do {
            // Tanpa huruf/angka yang mudah tertukar (0/O, 1/I).
            $code = 'SKJ-'.now()->format('ymd').'-'.Str::upper(substr(str_shuffle(str_repeat('23456789ABCDEFGHJKLMNPQRSTUVWXYZ', 2)), 0, 4));
        } while (static::query()->where('code', $code)->exists());

        return $code;
    }

    public function statusLabel(?string $locale = null): string
    {
        return __('site.status.'.$this->status, locale: $locale ?? app()->getLocale());
    }

    /** Pesan WhatsApp dari pemesan ke admin desa. */
    public function customerWhatsappMessage(): string
    {
        return __('site.booking.wa_customer', $this->messageParams(), $this->locale);
    }

    /** Pesan WhatsApp dari admin ke pemesan. */
    public function adminWhatsappMessage(): string
    {
        return __('site.booking.wa_admin', $this->messageParams(), $this->locale);
    }

    protected function messageParams(): array
    {
        return [
            'code' => $this->code,
            'name' => $this->customer_name,
            'package' => $this->package_name,
            'date' => $this->visit_date->locale($this->locale)->translatedFormat('l, j F Y'),
            'pax' => $this->pax,
            'total' => rupiah($this->total),
            'organization' => $this->organization ?: '-',
        ];
    }
}
