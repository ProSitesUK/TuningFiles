<?php

namespace App\Services;

use App\Models\CreditTransaction;
use App\Models\CustomerProfile;
use App\Models\Order;
use App\Models\Referral;
use App\Models\SiteSetting;

class ReferralService
{
    public static function creditReferral(Order $order): void
    {
        if (SiteSetting::get('referral_enabled', 'false') !== 'true') {
            return;
        }

        $referral = Referral::where('referred_id', $order->customer_id)
            ->where('status', 'pending')
            ->first();

        if (! $referral) {
            return;
        }

        // Only credit on first delivered/ready order
        $deliveredCount = Order::where('customer_id', $order->customer_id)
            ->whereIn('status', ['ready', 'delivered'])
            ->count();
        if ($deliveredCount > 1) {
            return;
        }

        $referrerCredits = (int) SiteSetting::get('referral_credits_referrer', '10');
        $referredCredits = (int) SiteSetting::get('referral_credits_referred', '10');

        // Grant to referrer
        $referrerProfile = $referral->referrer->customerProfile
            ?? CustomerProfile::create([
                'user_id'        => $referral->referrer_id,
                'plan'           => 'Pro',
                'credit_balance' => 0,
            ]);
        $referrerProfile->increment('credit_balance', $referrerCredits);
        CreditTransaction::create([
            'user_id'       => $referral->referrer_id,
            'type'          => 'promo',
            'credits'       => $referrerCredits,
            'balance_after' => $referrerProfile->fresh()->credit_balance,
            'note'          => "Referral bonus: {$referral->referred->name} placed first order",
        ]);

        // Grant to referred
        $referredProfile = $referral->referred->customerProfile
            ?? CustomerProfile::create([
                'user_id'        => $referral->referred_id,
                'plan'           => 'Pro',
                'credit_balance' => 0,
            ]);
        $referredProfile->increment('credit_balance', $referredCredits);
        CreditTransaction::create([
            'user_id'       => $referral->referred_id,
            'type'          => 'promo',
            'credits'       => $referredCredits,
            'balance_after' => $referredProfile->fresh()->credit_balance,
            'note'          => "Referral welcome bonus: referred by {$referral->referrer->name}",
        ]);

        $referral->update(['status' => 'credited', 'credited_at' => now()]);
    }

    public static function processOrderCommission(Order $order): void
    {
        $enabled = SiteSetting::get('referral_enabled', 'false') === 'true';
        if (!$enabled) return;

        $referral = Referral::where('referred_id', $order->customer_id)
            ->where('status', 'credited')
            ->first();
        if (!$referral) return;

        $creditRatePennies = (int) SiteSetting::get('credit_rate_pennies', '100');
        $orderSpendPennies = $order->credits_cost * $creditRatePennies;
        if ($orderSpendPennies <= 0) return;

        $tiers = json_decode(SiteSetting::get('referral_commission_tiers', '[]'), true);
        if (empty($tiers)) return;

        $newTotal = $referral->referred_total_spend_pennies + $orderSpendPennies;

        // Find applicable rate (highest crossed threshold)
        $applicableRate = 0;
        foreach ($tiers as $tier) {
            if ($newTotal >= $tier['threshold_pennies']) {
                $applicableRate = $tier['percent'];
            }
        }

        $commissionPennies = 0;
        if ($applicableRate > 0) {
            $commissionPennies = (int) round($orderSpendPennies * $applicableRate / 100);
            $commissionCredits = max(1, (int) round($commissionPennies / $creditRatePennies));

            // Grant to referrer
            $referrerProfile = $referral->referrer->customerProfile
                ?? CustomerProfile::create(['user_id' => $referral->referrer_id, 'plan' => 'Pro', 'credit_balance' => 0]);
            $referrerProfile->increment('credit_balance', $commissionCredits);

            CreditTransaction::create([
                'user_id'       => $referral->referrer_id,
                'type'          => 'promo',
                'credits'       => $commissionCredits,
                'balance_after' => $referrerProfile->fresh()->credit_balance,
                'amount_pennies' => $commissionPennies,
                'referral_id'   => $referral->id,
                'note'          => "Referral commission ({$applicableRate}%) on #{$order->reference} — {$referral->referred->name}",
            ]);
        }

        $referral->update([
            'referred_total_spend_pennies' => $newTotal,
            'commission_earned_pennies' => $referral->commission_earned_pennies + $commissionPennies,
            'current_tier' => count(array_filter($tiers, fn($t) => $newTotal >= $t['threshold_pennies'])),
        ]);
    }
}
