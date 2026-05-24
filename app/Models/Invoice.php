<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class Invoice extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'amount_pennies' => 'integer',
        'credits'        => 'integer',
        'due_at'         => 'datetime',
        'paid_at'        => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (Invoice $invoice) {
            if (! $invoice->reference) {
                $last = static::query()->orderByDesc('id')->value('reference');
                $next = $last ? ((int) str_replace('INV-', '', $last)) + 1 : 1;
                $invoice->reference = 'INV-' . str_pad($next, 4, '0', STR_PAD_LEFT);
            }
        });
    }

    /* ---- Relationships ---- */
    public function user(): BelongsTo       { return $this->belongsTo(User::class); }
    public function creditPack(): BelongsTo { return $this->belongsTo(CreditPack::class); }

    /* ---- Scopes ---- */
    public function scopePending(Builder $query): Builder
    {
        return $query->whereIn('status', ['sent', 'draft']);
    }

    public function scopeOverdue(Builder $query): Builder
    {
        return $query->where('status', 'sent')
            ->whereNotNull('due_at')
            ->where('due_at', '<', now());
    }

    /* ---- Helpers ---- */
    public function amountFormatted(): string
    {
        return '£' . number_format($this->amount_pennies / 100, 2);
    }
}
