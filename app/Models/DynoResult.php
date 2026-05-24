<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DynoResult extends Model
{
    protected $guarded = [];

    protected $casts = [
        'is_approved' => 'bool',
        'is_public'   => 'bool',
    ];

    public function user(): BelongsTo  { return $this->belongsTo(User::class); }
    public function order(): BelongsTo { return $this->belongsTo(Order::class); }

    public function scopeApproved(Builder $query): Builder { return $query->where('is_approved', true)->where('is_public', true); }
    public function scopePending(Builder $query): Builder  { return $query->where('is_approved', false); }

    public function hpGain(): int { return $this->tuned_hp - $this->stock_hp; }

    public function torqueGain(): ?int
    {
        if ($this->stock_torque === null || $this->tuned_torque === null) {
            return null;
        }

        return $this->tuned_torque - $this->stock_torque;
    }
}
