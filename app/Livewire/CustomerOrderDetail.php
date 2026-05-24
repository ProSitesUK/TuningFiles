<?php

namespace App\Livewire;

use App\Models\CreditTransaction;
use App\Models\DownloadLog;
use App\Models\DynoResult;
use App\Models\Order;
use App\Models\OrderEvent;
use App\Models\SiteSetting;
use Livewire\Component;

class CustomerOrderDetail extends Component
{
    public Order $order;
    public string $guaranteeReason = '';
    public bool $showGuaranteeForm = false;

    // Revision window
    public string $revisionNotes = '';

    // Dyno result submission
    public string $dynoStockHp = '';
    public string $dynoTunedHp = '';
    public string $dynoTuneType = '';
    public string $dynoNotes = '';

    public function mount(Order $order): void
    {
        abort_unless($order->customer_id === auth()->id() || auth()->user()->isAdmin(), 403);
        $this->order = $order->load('events.actor', 'files', 'assignedTuner', 'vehicle', 'ecu');
    }

    public function downloadOriginal()
    {
        $f = $this->order->originalFile();
        abort_unless($f, 404);

        DownloadLog::create([
            'order_file_id' => $f->id,
            'user_id' => auth()->id(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'downloaded_at' => now(),
        ]);

        return response()->download(storage_path('app/private/'.$f->path), $f->original_name ?? "original_{$this->order->reference}.bin");
    }

    public function downloadTuned()
    {
        $f = $this->order->tunedFile();
        abort_unless($f, 404);

        DownloadLog::create([
            'order_file_id' => $f->id,
            'user_id' => auth()->id(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'downloaded_at' => now(),
        ]);

        return response()->download(storage_path('app/private/'.$f->path), "tuned_{$this->order->reference}.bin");
    }

    public function claimGuarantee(string $reason): void
    {
        abort_unless($this->order->customer_id === auth()->id(), 403);
        abort_unless($this->order->underGuarantee(), 403);

        $this->order->update(['guarantee_claimed_at' => now()]);

        // Restore credits to customer
        if ($this->order->credits_cost > 0 && $this->order->customer?->customerProfile) {
            $this->order->customer->customerProfile->increment('credit_balance', $this->order->credits_cost);

            CreditTransaction::create([
                'user_id'       => $this->order->customer_id,
                'order_id'      => $this->order->id,
                'type'          => 'refund',
                'credits'       => $this->order->credits_cost,
                'balance_after' => $this->order->customer->customerProfile->fresh()->credit_balance ?? 0,
                'note'          => "Guarantee claim for order #{$this->order->reference}: {$reason}",
            ]);
        }

        OrderEvent::create([
            'order_id'    => $this->order->id,
            'actor_id'    => auth()->id(),
            'stage'       => 'guarantee claim',
            'state'       => 'done',
            'note'        => "customer claimed guarantee: {$reason}",
            'happened_at' => now(),
        ]);

        $this->order->refresh();
        $this->showGuaranteeForm = false;
        $this->guaranteeReason = '';
    }

    public function requestRevision(): void
    {
        $order = $this->order;
        if (!$order->underRevisionWindow()) {
            $this->addError('revision', 'Revision window has expired.');
            return;
        }

        $this->validate(['revisionNotes' => 'required|string|min:10|max:1000']);

        $order->increment('revision_count');
        $order->update(['status' => 'in_progress']);

        OrderEvent::create([
            'order_id'    => $order->id,
            'actor_id'    => auth()->id(),
            'stage'       => 'revision requested',
            'state'       => 'active',
            'note'        => $this->revisionNotes,
            'happened_at' => now(),
        ]);

        $this->revisionNotes = '';
        $this->order->refresh();
    }

    public function submitDynoResult(): void
    {
        $this->validate([
            'dynoStockHp'  => 'required|integer|min:1|max:9999',
            'dynoTunedHp'  => 'required|integer|min:1|max:9999',
            'dynoTuneType' => 'required|string|max:100',
            'dynoNotes'    => 'nullable|string|max:1000',
        ]);

        $order = $this->order;
        abort_unless($order->customer_id === auth()->id(), 403);
        abort_unless($order->status === 'delivered', 403);

        DynoResult::create([
            'user_id'       => auth()->id(),
            'order_id'      => $order->id,
            'vehicle_label' => $order->vehicle_label,
            'vehicle_year'  => $order->vehicle_year,
            'stock_hp'      => (int) $this->dynoStockHp,
            'tuned_hp'      => (int) $this->dynoTunedHp,
            'tune_type'     => $this->dynoTuneType,
            'notes'         => $this->dynoNotes ?: null,
            'is_approved'   => false,
            'is_public'     => true,
        ]);

        // Grant 5 bonus credits
        if ($order->customer?->customerProfile) {
            $order->customer->customerProfile->increment('credit_balance', 5);
            CreditTransaction::create([
                'user_id'       => auth()->id(),
                'order_id'      => $order->id,
                'type'          => 'bonus',
                'credits'       => 5,
                'balance_after' => $order->customer->customerProfile->fresh()->credit_balance ?? 0,
                'note'          => "Dyno result bonus for order #{$order->reference}",
            ]);
        }

        $this->dynoStockHp = '';
        $this->dynoTunedHp = '';
        $this->dynoTuneType = '';
        $this->dynoNotes = '';
    }

    public function render()
    {
        return view('livewire.customer-order-detail');
    }
}
