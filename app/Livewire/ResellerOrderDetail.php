<?php

namespace App\Livewire;

use App\Models\CreditTransaction;
use App\Models\CustomerProfile;
use App\Models\DownloadLog;
use App\Models\Order;
use App\Models\OrderEvent;
use App\Models\OrderFile;
use App\Models\SiteSetting;
use App\Notifications\OrderReady;
use Livewire\Component;
use Livewire\WithFileUploads;

class ResellerOrderDetail extends Component
{
    use WithFileUploads;

    public int $orderId;
    public $tunedUpload = null;

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

    public function saveFileNote(int $fileId, string $notes): void
    {
        $file = OrderFile::findOrFail($fileId);
        $order = $file->order;
        abort_unless($order && $order->reseller_id === auth()->id(), 403);
        $file->update(['notes' => trim($notes) ?: null]);
    }

    public function uploadTuned(): void
    {
        $this->validate(['tunedUpload' => 'required|file|max:10240']);

        $order = $this->getOrderProperty();
        abort_unless($order->reseller_id === auth()->id(), 403);

        $path = $this->tunedUpload->store('ecu-files/'.now()->format('Y/m'), 'local');
        $md5  = md5_file($this->tunedUpload->getRealPath());

        OrderFile::create([
            'order_id'       => $order->id,
            'uploaded_by_id' => auth()->id(),
            'kind'           => 'tuned',
            'disk'           => 'local',
            'path'           => $path,
            'original_name'  => $this->tunedUpload->getClientOriginalName(),
            'size'           => $this->tunedUpload->getSize(),
            'md5'            => $md5,
            'mime'           => $this->tunedUpload->getMimeType(),
        ]);

        $order->update(['status' => 'review', 'review_at' => now(), 'progress' => 1]);
        OrderEvent::create([
            'order_id'    => $order->id,
            'actor_id'    => auth()->id(),
            'stage'       => 'review',
            'state'       => 'active',
            'note'        => 'tuned file uploaded · awaiting QA',
            'happened_at' => now(),
        ]);

        $this->tunedUpload = null;
    }

    public function changeStatus(string $status): void
    {
        $order = $this->getOrderProperty();
        abort_unless($order->reseller_id === auth()->id(), 403);
        if (! in_array($status, Order::STATUSES, true)) return;

        $from = $order->status;
        $order->update(['status' => $status]);

        OrderEvent::create([
            'order_id'    => $order->id,
            'actor_id'    => auth()->id(),
            'stage'       => 'status changed',
            'state'       => 'done',
            'note'        => "{$from} → {$status} by ".auth()->user()->name,
            'happened_at' => now(),
        ]);
    }

    public function markReady(): void
    {
        $order = $this->getOrderProperty();
        abort_unless($order->reseller_id === auth()->id(), 403);

        $guaranteeDays = (int) SiteSetting::get('guarantee_days', '30');
        $revisionHours = (int) SiteSetting::get('revision_window_hours', '24');
        $maxRevisions = (int) SiteSetting::get('max_free_revisions', '1');

        $order->update([
            'status'                  => 'ready',
            'ready_at'                => now(),
            'guarantee_expires_at'    => now()->addDays($guaranteeDays),
            'revision_window_ends_at' => now()->addHours($revisionHours),
            'max_revisions'           => $maxRevisions,
        ]);

        OrderEvent::create([
            'order_id'    => $order->id,
            'actor_id'    => auth()->id(),
            'stage'       => 'ready',
            'state'       => 'done',
            'note'        => 'approved by '.auth()->user()->name,
            'happened_at' => now(),
        ]);

        if ($order->customer) {
            $order->customer->notify(new OrderReady($order));
        }

        \App\Services\ReferralService::creditReferral($order);
        \App\Services\ReferralService::processOrderCommission($order);
    }

    public function render()
    {
        $order = $this->getOrderProperty();
        $canUpload = in_array($order->status, ['queued', 'in_progress', 'review'], true);

        return view('livewire.reseller-order-detail', [
            'order'     => $order,
            'canUpload' => $canUpload,
        ]);
    }
}
