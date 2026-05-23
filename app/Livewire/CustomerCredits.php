<?php

namespace App\Livewire;

use App\Models\CreditPack;
use App\Models\CreditTransaction;
use Livewire\Component;

class CustomerCredits extends Component
{
    public function render()
    {
        $packs = CreditPack::where('is_active', true)->orderBy('credits')->get();
        $tx = CreditTransaction::where('user_id', auth()->id())
            ->latest()->limit(15)->get();

        return view('livewire.customer-credits', [
            'packs' => $packs,
            'tx'    => $tx,
            'balance' => (int) (auth()->user()->customerProfile?->credit_balance ?? 0),
        ]);
    }
}
