<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionPlan extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'features'      => 'array',
        'is_active'     => 'bool',
        'price_pennies' => 'int',
        'max_customers' => 'int',
    ];

    public function priceFormatted(): string
    {
        return '£' . number_format($this->price_pennies / 100, 2) . '/mo';
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function isUnlimited(): bool
    {
        return $this->max_customers === 0;
    }
}
