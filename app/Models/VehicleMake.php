<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class VehicleMake extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $casts = ['is_active' => 'bool', 'sort_order' => 'int'];

    public function models(): HasMany
    {
        return $this->hasMany(VehicleModel::class, 'make_id')->orderBy('name');
    }

    public function variants(): HasManyThrough
    {
        return $this->hasManyThrough(Vehicle::class, VehicleModel::class, 'make_id', 'model_id');
    }
}
