<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Cashier\Billable;
use Spatie\Permission\Traits\HasRoles;

#[Fillable(['name', 'email', 'password', 'status', 'reseller_id'])]
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
    public function resellerProfile(): HasOne     { return $this->hasOne(ResellerProfile::class); }
    public function reseller(): BelongsTo         { return $this->belongsTo(User::class, 'reseller_id'); }
    public function subCustomers(): HasMany       { return $this->hasMany(User::class, 'reseller_id'); }
    public function orders(): HasMany             { return $this->hasMany(Order::class, 'customer_id'); }
    public function assignedOrders(): HasMany     { return $this->hasMany(Order::class, 'assigned_tuner_id'); }
    public function creditTransactions(): HasMany { return $this->hasMany(CreditTransaction::class); }
    public function tickets(): HasMany            { return $this->hasMany(Ticket::class, 'customer_id'); }

    public function isAdmin(): bool      { return $this->hasAnyRole(['admin', 'operations']); }
    public function isTuner(): bool      { return $this->hasRole('tuner'); }
    public function isCustomer(): bool   { return $this->hasRole('customer'); }
    public function isReseller(): bool   { return $this->hasRole('reseller'); }
    public function hasReseller(): bool  { return $this->reseller_id !== null; }

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

    public function isOnline(): bool  { return $this->status === 'online'; }
    public function isAway(): bool    { return in_array($this->status, ['away', 'busy', 'holiday']); }
    public function isOffline(): bool { return ! $this->isOnline() && ! $this->isAway(); }

    public function statusDot(): string
    {
        if ($this->isOnline()) return 'ok';
        if ($this->isAway()) return 'warn';
        return 'mute';
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'online'  => 'Online',
            'away'    => 'Away',
            'busy'    => 'Busy',
            'holiday' => 'Holiday',
            default   => 'Offline',
        };
    }

    public static function supportIsOnline(): bool
    {
        return self::role(['admin', 'operations', 'tuner'])
            ->where('status', 'online')
            ->exists();
    }

    public static function supportStatus(): string
    {
        if (self::supportIsOnline()) return 'online';
        if (self::role(['admin', 'operations', 'tuner'])->whereIn('status', ['away', 'busy'])->exists()) return 'away';
        return 'offline';
    }
}
