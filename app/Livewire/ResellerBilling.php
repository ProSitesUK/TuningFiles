<?php

namespace App\Livewire;

use App\Models\SubscriptionPlan;
use Livewire\Component;

class ResellerBilling extends Component
{
    public function render()
    {
        $profile = auth()->user()->resellerProfile;
        $plan = $profile
            ? SubscriptionPlan::where('max_customers', $profile->max_customers)->first()
            : null;

        $customerCount = auth()->user()->subCustomers()->count();

        return view('livewire.reseller-billing', [
            'profile'       => $profile,
            'plan'          => $plan,
            'customerCount' => $customerCount,
        ]);
    }
}
