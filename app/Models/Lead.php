<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Prospek/calon pelanggan untuk dikelola & dipantau di admin. */
class Lead extends Model
{
    public const STATUSES = ['new', 'contacted', 'negotiating', 'won', 'lost'];

    public const SOURCES = ['booking', 'contact', 'whatsapp', 'manual'];

    protected $guarded = [];

    protected $casts = [
        'follow_up_at' => 'date',
        'estimated_value' => 'integer',
    ];

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class);
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function message(): BelongsTo
    {
        return $this->belongsTo(Message::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}
