<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderFile extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function order(): BelongsTo      { return $this->belongsTo(Order::class); }
    public function uploadedBy(): BelongsTo { return $this->belongsTo(User::class, 'uploaded_by_id'); }

    public function humanSize(): string
    {
        $b = $this->size;
        if ($b >= 1_048_576) return number_format($b / 1_048_576, 2).' MB';
        if ($b >= 1024)      return number_format($b / 1024, 1).' KB';
        return $b.' B';
    }
}
