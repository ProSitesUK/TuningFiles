<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ResellerProfile extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $casts = [
        'is_active'          => 'bool',
        'commission_percent' => 'int',
        'max_customers'      => 'int',
        'domain_verified'    => 'bool',
        'trial_ends_at'      => 'datetime',
    ];

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

    public function isSubscribed(): bool
    {
        return in_array($this->subscription_status, ['active', 'trialing']);
    }

    public function hasExpired(): bool
    {
        return in_array($this->subscription_status, ['past_due', 'cancelled', null, 'none']);
    }

    public function tenantUrl(): string
    {
        return url('/t/' . $this->slug);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
