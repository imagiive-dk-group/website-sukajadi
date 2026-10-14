<?php

namespace App\Models;

use App\Models\Concerns\HasImageVariants;
use App\Models\Concerns\HasTranslations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Photo extends Model
{
    use HasImageVariants, HasTranslations;

    protected $guarded = [];

    protected array $translatable = ['caption'];

    protected array $imageColumns = ['path'];

    public function photoable(): MorphTo
    {
        return $this->morphTo();
    }
}
