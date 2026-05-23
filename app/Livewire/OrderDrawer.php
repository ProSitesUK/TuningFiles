<?php

namespace App\Livewire;

use App\Models\CreditTransaction;
use App\Models\Order;
use App\Models\OrderEvent;
use App\Models\OrderFile;
use App\Models\User;
use App\Notifications\OrderReady;
use App\Notifications\OrderRefunded;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;

class OrderDrawer extends Component
{
    use WithFileUploads;

    public ?int $orderId = null;
    public $tunedUpload = null;
    public ?int $reassignTo = null;

    #[On('order:open')]
    public function open(int $id): void
    {
        $this->orderId = $id;
        $this->tunedUpload = null;
        $this->reassignTo = null;
    }

    public function close(): void
    {
        $this->orderId = null;
    }

    public function getOrderProperty(): ?Order
    {
        if (! $this->orderId) return null;
        return Order::with(['customer.customerProfile', 'assignedTuner.tunerProfile', 'vehicle', 'ecu', 'files', 'events.actor'])
            ->find($this->orderId);
    }

    public function uploadTuned(): void
    {
        abort_unless(auth()->user()->isAdmin() || auth()->user()->isTuner(), 403);
        $this->validate(['tunedUpload' => 'required|file|max:10240']);

        $order = $this->getOrderProperty();
        if (! $order) return;

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

    public function markReady(): void
    {
        abort_unless(auth()->user()->isAdmin(), 403);
        $order = $this->getOrderProperty();
        if (! $order) return;

        $order->update(['status' => 'ready', 'ready_at' => now()]);
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
    }

    public function refund(): void
    {
        abort_unless(auth()->user()->isAdmin(), 403);
        $order = $this->getOrderProperty();
        if (! $order) return;

        DB::transaction(function () use ($order) {
            // restore credits
            if ($order->credits_cost > 0 && $order->customer?->customerProfile) {
                $order->customer->customerProfile->increment('credit_balance', $order->credits_cost);
                CreditTransaction::create([
                    'user_id'       => $order->customer_id,
                    'order_id'      => $order->id,
                    'type'          => 'refund',
                    'credits'       => $order->credits_cost,
                    'balance_after' => $order->customer->customerProfile->fresh()->credit_balance,
                    'note'          => "Refund for order #{$order->reference}",
                ]);
            }

            $order->update(['status' => 'refunded', 'refunded_at' => now()]);
            OrderEvent::create([
                'order_id'    => $order->id,
                'actor_id'    => auth()->id(),
                'stage'       => 'refund',
                'state'       => 'done',
                'note'        => 'credits restored · '.$order->credits_cost.' cr',
                'happened_at' => now(),
            ]);
        });

        if ($order->customer) {
            $order->customer->notify(new OrderRefunded($order));
        }
    }

    public function reassign(): void
    {
        abort_unless(auth()->user()->isAdmin(), 403);
        $order = $this->getOrderProperty();
        if (! $order || ! $this->reassignTo) return;

        $tuner = User::find($this->reassignTo);
        if (! $tuner) return;

        $order->update(['assigned_tuner_id' => $tuner->id, 'assigned_at' => now()]);
        OrderEvent::create([
            'order_id'    => $order->id,
            'actor_id'    => auth()->id(),
            'stage'       => 'reassigned',
            'state'       => 'done',
            'note'        => 'reassigned to '.$tuner->name,
            'happened_at' => now(),
        ]);
        $this->reassignTo = null;
    }

    public function render()
    {
        $tuners = collect();
        if (auth()->user()?->isAdmin()) {
            $tuners = User::role('tuner')->with('tunerProfile')->get();
        }

        return view('livewire.order-drawer', [
            'order'  => $this->getOrderProperty(),
            'tuners' => $tuners,
        ]);
    }
}
