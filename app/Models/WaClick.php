<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** Satu klik tombol WhatsApp di website (anonim, untuk monitoring minat). */
class WaClick extends Model
{
    public const UPDATED_AT = null;

    protected $guarded = [];

    protected $casts = ['created_at' => 'datetime'];
}
