<?php

namespace App\Models;

use App\Models\Concerns\HasTranslations;
use Illuminate\Database\Eloquent\Model;

class ProfileSection extends Model
{
    use HasTranslations;

    protected $guarded = [];

    protected array $translatable = ['title', 'body'];

    protected $casts = ['is_active' => 'boolean'];
}
