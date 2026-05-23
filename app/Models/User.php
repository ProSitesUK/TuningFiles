<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Cashier\Billable;
use Spatie\Permission\Traits\HasRoles;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasRoles, Billable;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function customerProfile(): HasOne     { return $this->hasOne(CustomerProfile::class); }
    public function tunerProfile(): HasOne        { return $this->hasOne(TunerProfile::class); }
    public function orders(): HasMany             { return $this->hasMany(Order::class, 'customer_id'); }
    public function assignedOrders(): HasMany     { return $this->hasMany(Order::class, 'assigned_tuner_id'); }
    public function creditTransactions(): HasMany { return $this->hasMany(CreditTransaction::class); }
    public function tickets(): HasMany            { return $this->hasMany(Ticket::class, 'customer_id'); }

    public function isAdmin(): bool    { return $this->hasAnyRole(['admin', 'operations']); }
    public function isTuner(): bool    { return $this->hasRole('tuner'); }
    public function isCustomer(): bool { return $this->hasRole('customer'); }

    public function initials(): string
    {
        return collect(explode(' ', trim($this->name)))
            ->map(fn ($w) => strtoupper(substr($w, 0, 1)))
            ->take(2)
            ->implode('');
    }

    public function creditBalance(): int
    {
        return (int) ($this->customerProfile?->credit_balance ?? 0);
    }
}
