<?php

namespace App\Models;

use App\Models\Concerns\HasImageVariants;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    use HasImageVariants;

    protected $guarded = [];

    protected array $imageColumns = ['photo_path'];

    protected $casts = [
        'is_approved' => 'boolean',
        'is_featured' => 'boolean',
        'rating' => 'integer',
    ];

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('is_approved', true)->latest();
    }
}
