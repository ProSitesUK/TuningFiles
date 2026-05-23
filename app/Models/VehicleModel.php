<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model as EloquentModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VehicleModel extends EloquentModel
{
    use HasFactory;

    protected $table = 'vehicle_models';
    protected $guarded = [];
    protected $casts = ['is_active' => 'bool'];

    public function make(): BelongsTo
    {
        return $this->belongsTo(VehicleMake::class, 'make_id');
    }

    public function variants(): HasMany
    {
        return $this->hasMany(Vehicle::class, 'model_id')->orderBy('year_start', 'desc');
    }
}
