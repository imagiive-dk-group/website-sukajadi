<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BlockedDate extends Model
{
    protected $guarded = [];

    protected $casts = ['date' => 'date'];

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }
}
