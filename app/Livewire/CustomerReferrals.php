<?php

namespace App\Livewire;

use App\Models\Referral;
use Livewire\Component;

class CustomerReferrals extends Component
{
    public function render()
    {
        $referrals = Referral::where('referrer_id', auth()->id())
            ->with('referred:id,name,email')
            ->orderByDesc('created_at')
            ->get();

        $totalCommission = $referrals->sum('commission_earned_pennies');
        $totalReferred = $referrals->count();
        $activeReferrals = $referrals->where('status', 'credited')->count();

        return view('livewire.customer-referrals', [
            'referrals' => $referrals,
            'totalCommission' => $totalCommission,
            'totalReferred' => $totalReferred,
            'activeReferrals' => $activeReferrals,
        ]);
    }
}
