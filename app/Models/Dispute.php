<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Dispute extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $casts = ['resolved_at' => 'datetime'];

    public function order(): BelongsTo      { return $this->belongsTo(Order::class); }
    public function openedBy(): BelongsTo   { return $this->belongsTo(User::class, 'opened_by_id'); }
    public function resolvedBy(): BelongsTo { return $this->belongsTo(User::class, 'resolved_by_id'); }
}
