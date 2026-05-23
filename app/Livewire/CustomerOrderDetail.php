<?php

namespace App\Livewire;

use App\Models\Order;
use Livewire\Component;

class CustomerOrderDetail extends Component
{
    public Order $order;

    public function mount(Order $order): void
    {
        abort_unless($order->customer_id === auth()->id() || auth()->user()->isAdmin(), 403);
        $this->order = $order->load('events.actor', 'files', 'assignedTuner', 'vehicle', 'ecu');
    }

    public function downloadOriginal()
    {
        $f = $this->order->originalFile();
        abort_unless($f, 404);
        return response()->download(storage_path('app/private/'.$f->path), $f->original_name ?? "original_{$this->order->reference}.bin");
    }

    public function downloadTuned()
    {
        $f = $this->order->tunedFile();
        abort_unless($f, 404);
        return response()->download(storage_path('app/private/'.$f->path), "tuned_{$this->order->reference}.bin");
    }

    public function render()
    {
        return view('livewire.customer-order-detail');
    }
}
