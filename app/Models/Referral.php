<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Referral extends Model
{
    protected $guarded = [];

    protected $casts = [
        'credited_at' => 'datetime',
    ];

    public function referrer(): BelongsTo { return $this->belongsTo(User::class, 'referrer_id'); }
    public function referred(): BelongsTo { return $this->belongsTo(User::class, 'referred_id'); }

    public function scopeCredited(Builder $query): Builder { return $query->where('status', 'credited'); }
    public function scopePending(Builder $query): Builder  { return $query->where('status', 'pending'); }
}
