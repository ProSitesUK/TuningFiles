<?php

namespace App\Livewire;

use App\Models\DownloadLog;
use App\Models\Order;
use App\Models\OrderFile;
use Livewire\Component;

class ResellerOrderDetail extends Component
{
    public int $orderId;

    public function mount(int $orderId): void
    {
        $this->orderId = $orderId;

        $order = Order::findOrFail($orderId);
        abort_unless($order->reseller_id === auth()->id(), 403);
    }

    public function getOrderProperty(): Order
    {
        return Order::with(['customer', 'events.actor', 'files', 'assignedTuner', 'vehicle', 'ecu'])
            ->findOrFail($this->orderId);
    }

    public function downloadOriginal()
    {
        $order = $this->getOrderProperty();
        abort_unless($order->reseller_id === auth()->id(), 403);

        $f = $order->originalFile();
        abort_unless($f, 404);

        DownloadLog::create([
            'order_file_id' => $f->id,
            'user_id'       => auth()->id(),
            'ip_address'    => request()->ip(),
            'user_agent'    => request()->userAgent(),
            'downloaded_at' => now(),
        ]);

        return response()->download(storage_path('app/private/' . $f->path), $f->original_name ?? "original_{$order->reference}.bin");
    }

    public function downloadTuned()
    {
        $order = $this->getOrderProperty();
        abort_unless($order->reseller_id === auth()->id(), 403);

        $f = $order->tunedFile();
        abort_unless($f, 404);

        DownloadLog::create([
            'order_file_id' => $f->id,
            'user_id'       => auth()->id(),
            'ip_address'    => request()->ip(),
            'user_agent'    => request()->userAgent(),
            'downloaded_at' => now(),
        ]);

        return response()->download(storage_path('app/private/' . $f->path), "tuned_{$order->reference}.bin");
    }

    public function render()
    {
        return view('livewire.reseller-order-detail', [
            'order' => $this->getOrderProperty(),
        ]);
    }
}
