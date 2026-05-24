<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    public const STATUSES = ['queued', 'in_progress', 'review', 'ready', 'delivered', 'refunded', 'dispute', 'failed'];

    protected $guarded = [];

    protected $casts = [
        'options' => 'array',
        'breach'  => 'bool',
        'progress' => 'float',
        'queued_at' => 'datetime', 'assigned_at' => 'datetime', 'started_at' => 'datetime',
        'review_at' => 'datetime', 'ready_at' => 'datetime', 'delivered_at' => 'datetime',
        'refunded_at' => 'datetime', 'sla_due_at' => 'datetime',
        'guarantee_expires_at' => 'datetime', 'guarantee_claimed_at' => 'datetime',
        'revision_window_ends_at' => 'datetime',
    ];

    public function customer(): BelongsTo      { return $this->belongsTo(User::class, 'customer_id'); }
    public function assignedTuner(): BelongsTo { return $this->belongsTo(User::class, 'assigned_tuner_id'); }
    public function reseller(): BelongsTo      { return $this->belongsTo(User::class, 'reseller_id'); }
    public function vehicle(): BelongsTo       { return $this->belongsTo(Vehicle::class); }
    public function ecu(): BelongsTo           { return $this->belongsTo(Ecu::class); }
    public function files(): HasMany           { return $this->hasMany(OrderFile::class); }
    public function events(): HasMany          { return $this->hasMany(OrderEvent::class)->orderBy('happened_at'); }
    public function disputes(): HasMany        { return $this->hasMany(Dispute::class); }

    public function originalFile(): ?OrderFile { return $this->files()->where('kind', 'original')->latest()->first(); }
    public function tunedFile(): ?OrderFile    { return $this->files()->where('kind', 'tuned')->latest()->first(); }

    public function isOverdue(): bool
    {
        return $this->sla_due_at && $this->sla_due_at->isPast()
            && ! in_array($this->status, ['delivered', 'refunded', 'failed'], true);
    }

    public function elapsedLabel(): string
    {
        $start = $this->queued_at ?? $this->created_at;
        if (! $start) return '—';
        $mins = (int) $start->diffInMinutes(now(), absolute: true);
        if ($mins < 60) return "{$mins}m";
        $h = intdiv($mins, 60); $m = $mins % 60;
        return sprintf('%dh %02dm', $h, $m);
    }

    public function ageMin(): int
    {
        $start = $this->queued_at ?? $this->created_at;
        return $start ? (int) $start->diffInMinutes(now(), absolute: true) : 0;
    }

    public function underGuarantee(): bool
    {
        return $this->status === 'delivered'
            && $this->guarantee_expires_at
            && $this->guarantee_expires_at->isFuture()
            && ! $this->guarantee_claimed_at;
    }

    public function underRevisionWindow(): bool
    {
        return $this->status === 'delivered'
            && $this->revision_window_ends_at
            && $this->revision_window_ends_at->isFuture()
            && $this->revision_count < $this->max_revisions;
    }
}
