<?php

namespace App\Livewire;

use App\Models\Dispute;
use Livewire\Component;

class AdminDisputes extends Component
{
    public string $filter     = 'open';
    public ?int   $selected   = null;
    public string $resolution = '';

    public function selectDispute(int $id): void
    {
        $this->selected = $id;
        $dispute = Dispute::find($id);
        $this->resolution = $dispute?->resolution ?? '';
    }

    public function markInvestigating(): void
    {
        if (! $this->selected) return;
        Dispute::findOrFail($this->selected)->update(['status' => 'investigating']);
    }

    public function resolve(): void
    {
        if (! $this->selected) return;
        if (trim($this->resolution) === '') return;

        Dispute::findOrFail($this->selected)->update([
            'status'     => 'resolved',
            'resolution' => trim($this->resolution),
            'resolved_at' => now(),
        ]);
    }

    public function reject(): void
    {
        if (! $this->selected) return;

        Dispute::findOrFail($this->selected)->update([
            'status'     => 'rejected',
            'resolution' => trim($this->resolution) !== '' ? trim($this->resolution) : null,
            'resolved_at' => now(),
        ]);
    }

    public function render()
    {
        $q = Dispute::with('order:id,reference,customer_id', 'order.customer:id,name');

        if ($this->filter !== 'all') {
            $q->where('status', $this->filter);
        }

        $disputes = $q->orderByDesc('created_at')->get();

        $selDispute = $this->selected
            ? Dispute::with('order:id,reference,customer_id', 'order.customer:id,name')->find($this->selected)
            : null;

        return view('livewire.admin-disputes', [
            'disputes'   => $disputes,
            'selDispute' => $selDispute,
        ]);
    }
}
