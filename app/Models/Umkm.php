<?php

namespace App\Models;

use App\Models\Concerns\HasImageVariants;
use App\Models\Concerns\HasTranslations;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Umkm extends Model
{
    use HasImageVariants, HasTranslations;

    protected $guarded = [];

    protected array $translatable = ['summary', 'description', 'opening_hours'];

    protected array $imageColumns = ['cover_path'];

    protected $casts = [
        'accepts_orders' => 'boolean',
        'accepts_visits' => 'boolean',
        'show_contact' => 'boolean',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function products(): HasMany
    {
        return $this->hasMany(UmkmProduct::class)->orderBy('sort');
    }

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

    /** Kontak UMKM tampil hanya jika pemilik sudah mengizinkan & nomornya ada. */
    public function hasPublicContact(): bool
    {
        return $this->show_contact && filled($this->whatsapp);
    }

    /** Tautan WA ke UMKM, atau ke admin desa bila kontak UMKM belum boleh ditampilkan. */
    public function whatsappLink(?string $text = null, string $context = 'umkm', ?int $contextId = null): string
    {
        $text ??= __('site.umkm.wa_message', ['name' => $this->name]);

        return wa_track($context, $contextId ?? $this->id, $text, $this->hasPublicContact() ? $this->whatsapp : null);
    }

    public function url(?string $locale = null): string
    {
        return lroute('umkm.show', $this->slugFor($locale), $locale);
    }
}
