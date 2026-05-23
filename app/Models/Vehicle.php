<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vehicle extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $casts = ['is_active' => 'bool'];

    public function ecus(): BelongsToMany { return $this->belongsToMany(Ecu::class, 'vehicle_ecu'); }
    public function orders(): HasMany     { return $this->hasMany(Order::class); }

    public function displayName(): string
    {
        return trim("{$this->make} {$this->model} ".($this->generation ?? ''));
    }

    public function yearRange(): string
    {
        return $this->year_end ? "{$this->year_start}–{$this->year_end}" : (string) $this->year_start;
    }
}
