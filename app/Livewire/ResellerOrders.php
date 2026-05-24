<?php

namespace App\Livewire;

use App\Models\Order;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class ResellerOrders extends Component
{
    use WithPagination;

    public string $status = '';
    public string $customerId = '';

    public function updatingStatus(): void
    {
        $this->resetPage();
    }

    public function updatingCustomerId(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $resellerId = auth()->id();

        $query = Order::where('reseller_id', $resellerId)
            ->with('customer:id,name')
            ->orderByDesc('created_at');

        if ($this->status) {
            $query->where('status', $this->status);
        }

        if ($this->customerId) {
            $query->where('customer_id', $this->customerId);
        }

        $orders = $query->paginate(20);

        $subCustomers = User::where('reseller_id', $resellerId)
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('livewire.reseller-orders', [
            'orders'       => $orders,
            'subCustomers' => $subCustomers,
        ]);
    }
}
