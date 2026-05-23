<?php

namespace App\Livewire;

use App\Models\Order;
use App\Support\Charts;
use Livewire\Component;
use Livewire\WithPagination;

class QueueTable extends Component
{
    use WithPagination;

    public string $filter = 'all';
    public array  $selected = [];

    protected $queryString = ['filter'];

    public function updatingFilter(): void { $this->resetPage(); }

    public function toggle(int $id): void
    {
        $i = array_search($id, $this->selected, true);
        if ($i === false) $this->selected[] = $id;
        else array_splice($this->selected, $i, 1);
    }

    public function render()
    {
        $base = Order::query()->with(['customer.customerProfile', 'assignedTuner']);

        $counts = [
            'all'         => Order::count(),
            'queued'      => Order::where('status', 'queued')->count(),
            'in_progress' => Order::where('status', 'in_progress')->count(),
            'review'      => Order::where('status', 'review')->count(),
            'ready'       => Order::whereIn('status', ['ready', 'delivered'])->count(),
            'failed'      => Order::whereIn('status', ['failed', 'refunded'])->count(),
        ];

        $q = clone $base;
        match ($this->filter) {
            'queued', 'in_progress', 'review' => $q->where('status', $this->filter),
            'ready'  => $q->whereIn('status', ['ready', 'delivered']),
            'failed' => $q->whereIn('status', ['failed', 'refunded']),
            default  => null,
        };

        $orders = $q->orderByDesc('reference')->paginate(15);

        return view('livewire.queue-table', [
            'orders'  => $orders,
            'counts'  => $counts,
            'charts'  => [
                'orders'     => Charts::ORDERS_14D,
                'revenue'    => Charts::REVENUE_14D,
                'turnaround' => Charts::TURNAROUND_14D,
                'queue'      => Charts::QUEUE_14D,
                'tuners'     => Charts::TUNERS_14D,
                'disputes'   => Charts::DISPUTES_14D,
            ],
        ]);
    }
}
