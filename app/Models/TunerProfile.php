<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TunerProfile extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $casts = ['specialties' => 'array', 'last_active_at' => 'datetime'];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function isLive(): bool    { return $this->status === 'live'; }
}
