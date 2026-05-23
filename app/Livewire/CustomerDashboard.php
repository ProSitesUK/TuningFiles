<?php

namespace App\Livewire;

use Livewire\Component;

class CustomerDashboard extends Component
{
    public function render()
    {
        $user = auth()->user();
        $orders = $user->orders()->latest('queued_at')->limit(8)->get();
        $profile = $user->customerProfile;

        return view('livewire.customer-dashboard', [
            'user'    => $user,
            'orders'  => $orders,
            'profile' => $profile,
        ]);
    }
}
