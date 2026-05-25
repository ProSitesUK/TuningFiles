<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CreditTransaction extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function user(): BelongsTo       { return $this->belongsTo(User::class); }
    public function order(): BelongsTo      { return $this->belongsTo(Order::class); }
    public function creditPack(): BelongsTo { return $this->belongsTo(CreditPack::class); }
    public function referral(): BelongsTo   { return $this->belongsTo(Referral::class); }
}
