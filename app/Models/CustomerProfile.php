<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerProfile extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $casts = ['since_at' => 'datetime', 'credit_balance' => 'integer', 'total_spent_pennies' => 'integer'];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }

    public function totalSpentFormatted(): string
    {
        return '£'.number_format($this->total_spent_pennies / 100, 2);
    }
}
