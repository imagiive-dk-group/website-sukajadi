<?php

namespace App\Models;

use App\Models\Concerns\HasTranslations;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Addon extends Model
{
    use HasTranslations;

    protected $guarded = [];

    protected array $translatable = ['name', 'description'];

    protected $casts = [
        'is_active' => 'boolean',
        'price' => 'integer',
    ];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)->orderBy('sort');
    }

    public function totalFor(int $pax): int
    {
        return $this->unit === 'per_group' ? $this->price : $this->price * $pax;
    }
}
