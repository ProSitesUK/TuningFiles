<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ticket extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function customer(): BelongsTo   { return $this->belongsTo(User::class, 'customer_id'); }
    public function assignedTo(): BelongsTo { return $this->belongsTo(User::class, 'assigned_to_id'); }
    public function order(): BelongsTo      { return $this->belongsTo(Order::class); }
    public function messages(): HasMany     { return $this->hasMany(TicketMessage::class)->orderBy('created_at'); }
}
