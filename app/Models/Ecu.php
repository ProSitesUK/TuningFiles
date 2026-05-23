<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Ecu extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $casts = ['supported_tunes' => 'array', 'is_active' => 'bool'];

    public function vehicles(): BelongsToMany { return $this->belongsToMany(Vehicle::class, 'vehicle_ecu'); }
}
