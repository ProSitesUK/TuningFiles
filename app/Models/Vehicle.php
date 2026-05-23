<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class Vehicle extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $casts = ['is_active' => 'bool'];
    protected $with = ['vehicleModel.make'];

    public function vehicleModel(): BelongsTo
    {
        return $this->belongsTo(VehicleModel::class, 'model_id');
    }

    public function makeRelation(): HasOneThrough
    {
        return $this->hasOneThrough(
            VehicleMake::class,
            VehicleModel::class,
            'id',
            'id',
            'model_id',
            'make_id',
        );
    }

    public function ecus(): BelongsToMany { return $this->belongsToMany(Ecu::class, 'vehicle_ecu'); }
    public function orders(): HasMany     { return $this->hasMany(Order::class); }

    protected function make(): Attribute
    {
        return Attribute::get(fn () => $this->vehicleModel?->make?->name);
    }

    protected function model(): Attribute
    {
        return Attribute::get(fn () => $this->vehicleModel?->name);
    }

    public function displayName(): string
    {
        return trim("{$this->make} {$this->model} ".($this->generation ?? ''));
    }

    public function yearRange(): string
    {
        return $this->year_end ? "{$this->year_start}–{$this->year_end}" : (string) $this->year_start;
    }
}
