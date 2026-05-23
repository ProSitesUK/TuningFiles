<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;

class CustomerOrders extends Component
{
    use WithPagination;

    public function render()
    {
        $orders = auth()->user()->orders()->latest('queued_at')->paginate(20);

        return view('livewire.customer-orders', ['orders' => $orders]);
    }
}
