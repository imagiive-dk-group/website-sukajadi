<?php

namespace App\Models;

use App\Models\Concerns\HasTranslations;
use Illuminate\Database\Eloquent\Model;

class ProfileStat extends Model
{
    use HasTranslations;

    protected $guarded = [];

    protected array $translatable = ['label', 'value'];

    protected $casts = ['needs_verification' => 'boolean'];
}
