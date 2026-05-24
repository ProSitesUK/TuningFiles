<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ResellerProfile extends Model
{
    protected $guarded = [];
    protected $casts = ['is_active' => 'bool', 'commission_percent' => 'int', 'max_customers' => 'int'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function subCustomers(): HasMany
    {
        return $this->hasMany(User::class, 'reseller_id', 'user_id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'reseller_id', 'user_id');
    }

    public function canAddCustomer(): bool
    {
        return $this->max_customers === 0 || $this->subCustomers()->count() < $this->max_customers;
    }
}
