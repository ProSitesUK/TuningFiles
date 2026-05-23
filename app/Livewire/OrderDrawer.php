<?php

namespace App\Livewire;

use App\Models\Order;
use Livewire\Attributes\On;
use Livewire\Component;

class OrderDrawer extends Component
{
    public ?int $orderId = null;

    #[On('order:open')]
    public function open(int $id): void
    {
        $this->orderId = $id;
    }

    public function close(): void
    {
        $this->orderId = null;
    }

    public function getOrderProperty(): ?Order
    {
        if (! $this->orderId) return null;
        return Order::with(['customer', 'assignedTuner', 'vehicle', 'ecu', 'files', 'events.actor'])
            ->find($this->orderId);
    }

    public function render()
    {
        return view('livewire.order-drawer', ['order' => $this->getOrderProperty()]);
    }
}
