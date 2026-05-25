<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Referral extends Model
{
    protected $guarded = [];

    protected $casts = [
        'credited_at' => 'datetime',
        'referred_total_spend_pennies' => 'int',
        'commission_earned_pennies' => 'int',
        'current_tier' => 'int',
    ];

    public function referrer(): BelongsTo { return $this->belongsTo(User::class, 'referrer_id'); }
    public function referred(): BelongsTo { return $this->belongsTo(User::class, 'referred_id'); }

    public function scopeCredited(Builder $query): Builder { return $query->where('status', 'credited'); }
    public function scopePending(Builder $query): Builder  { return $query->where('status', 'pending'); }

    public function tierLabel(): string
    {
        $tiers = json_decode(\App\Models\SiteSetting::get('referral_commission_tiers', '[]'), true);
        foreach (array_reverse($tiers) as $t) {
            if ($this->referred_total_spend_pennies >= $t['threshold_pennies']) {
                return $t['percent'] . '% (' . $t['label'] . ')';
            }
        }
        return 'No tier yet';
    }

    public function nextTier(): ?array
    {
        $tiers = json_decode(\App\Models\SiteSetting::get('referral_commission_tiers', '[]'), true);
        foreach ($tiers as $t) {
            if ($this->referred_total_spend_pennies < $t['threshold_pennies']) {
                return $t;
            }
        }
        return null; // already at max tier
    }

    public function progressToNextTier(): int
    {
        $next = $this->nextTier();
        if (!$next) return 100;
        $currentTierThreshold = 0;
        $tiers = json_decode(\App\Models\SiteSetting::get('referral_commission_tiers', '[]'), true);
        foreach ($tiers as $t) {
            if ($t['threshold_pennies'] === $next['threshold_pennies']) break;
            $currentTierThreshold = $t['threshold_pennies'];
        }
        $range = $next['threshold_pennies'] - $currentTierThreshold;
        $progress = $this->referred_total_spend_pennies - $currentTierThreshold;
        return min(100, max(0, (int) round($progress / max(1, $range) * 100)));
    }

    public function commissionFormatted(): string
    {
        return number_format($this->commission_earned_pennies / 100, 2);
    }

    public function spendFormatted(): string
    {
        return number_format($this->referred_total_spend_pennies / 100, 2);
    }
}
