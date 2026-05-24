<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;

class ResellerCustomers extends Component
{
    public string $search = '';

    public function removeCustomer(int $userId): void
    {
        $user = User::where('reseller_id', auth()->id())->findOrFail($userId);
        $user->update(['reseller_id' => null]);
        session()->flash('message', "{$user->name} has been removed from your customers.");
    }

    public function render()
    {
        $query = User::where('reseller_id', auth()->id())
            ->with('customerProfile')
            ->withCount('orders');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('email', 'like', "%{$this->search}%");
            });
        }

        $customers = $query->orderBy('name')->get();

        return view('livewire.reseller-customers', [
            'customers' => $customers,
        ]);
    }
}
