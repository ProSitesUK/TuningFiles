<?php

namespace App\Livewire;

use App\Models\CreditTransaction;
use App\Models\Dispute;
use App\Models\Order;
use App\Models\TunerProfile;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class QueueTable extends Component
{
    use WithPagination;

    public string $filter = 'all';
    public array  $selected = [];

    // Top filter bar (Buttons 10-11)
    public string $dateFilter = 'all';
    public string $tunerFilter = 'all';

    // Column sorting (Buttons 12-15)
    public string $sortBy = 'reference';
    public string $sortDir = 'desc';

    // Bulk assign (Button 17)
    public array $selectedOrders = [];
    public ?int $bulkTunerId = null;

    protected $queryString = ['filter'];

    public function updatingFilter(): void { $this->resetPage(); }
    public function updatingDateFilter(): void { $this->resetPage(); }
    public function updatingTunerFilter(): void { $this->resetPage(); }

    public function toggle(int $id): void
    {
        $i = array_search($id, $this->selected, true);
        if ($i === false) $this->selected[] = $id;
        else array_splice($this->selected, $i, 1);
    }

    public function sortColumn(string $col): void
    {
        if ($this->sortBy === $col) {
            $this->sortDir = $this->sortDir === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $col;
            $this->sortDir = 'desc';
        }
    }

    public function toggleSelect(int $orderId): void
    {
        if (in_array($orderId, $this->selectedOrders)) {
            $this->selectedOrders = array_values(array_diff($this->selectedOrders, [$orderId]));
        } else {
            $this->selectedOrders[] = $orderId;
        }
    }

    public function bulkAssign(): void
    {
        if (empty($this->selectedOrders) || !$this->bulkTunerId) return;
        abort_unless(auth()->user()->isAdmin(), 403);
        Order::whereIn('id', $this->selectedOrders)
            ->update(['assigned_tuner_id' => $this->bulkTunerId, 'assigned_at' => now()]);
        $this->selectedOrders = [];
        $this->bulkTunerId = null;
    }

    public function export()
    {
        $q = Order::with('customer:id,name', 'assignedTuner:id,name')
            ->orderByDesc('reference');

        // Apply same filters as the view
        match ($this->filter) {
            'queued', 'in_progress', 'review' => $q->where('status', $this->filter),
            'ready'  => $q->whereIn('status', ['ready', 'delivered']),
            'failed' => $q->whereIn('status', ['failed', 'refunded']),
            default  => null,
        };

        if ($this->dateFilter === 'today') $q->whereDate('created_at', today());
        elseif ($this->dateFilter === '7d') $q->where('created_at', '>=', now()->subDays(7));

        if ($this->tunerFilter !== 'all') $q->where('assigned_tuner_id', (int) $this->tunerFilter);

        $orders = $q->get();

        $csv = "Reference,Customer,Vehicle,ECU,Options,Status,Credits,Tuner,Date\n";
        foreach ($orders as $o) {
            $csv .= implode(',', [
                $o->reference,
                '"' . ($o->customer?->name ?? '') . '"',
                '"' . ($o->vehicle_label ?? '') . '"',
                '"' . ($o->ecu_label ?? '') . '"',
                '"' . ($o->options_label ?? '') . '"',
                $o->status,
                $o->credits_cost,
                '"' . ($o->assignedTuner?->name ?? '') . '"',
                $o->created_at?->format('Y-m-d H:i'),
            ]) . "\n";
        }

        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="orders-export-' . now()->format('Y-m-d') . '.csv"',
        ]);
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

        // Date filter (Button 10)
        if ($this->dateFilter === 'today') $q->whereDate('created_at', today());
        elseif ($this->dateFilter === '7d') $q->where('created_at', '>=', now()->subDays(7));

        // Tuner filter (Button 11)
        if ($this->tunerFilter !== 'all') $q->where('assigned_tuner_id', (int) $this->tunerFilter);

        // Column sorting (Buttons 12-15)
        $orders = $q->orderBy($this->sortBy, $this->sortDir)->paginate(15);

        // Tuners list for filter dropdown and bulk assign
        $tunerUsers = User::role('tuner')->orderBy('name')->get();

        // ── KPI values ──────────────────────────────────────────
        $ordersToday = Order::whereDate('created_at', today())->count();

        $revenueTodayPennies = CreditTransaction::where('type', 'purchase')
            ->whereDate('created_at', today())
            ->sum('amount_pennies');
        $revenueToday = '£' . number_format($revenueTodayPennies / 100, 0);

        $avgTurnaround = (int) Order::where('status', 'delivered')
            ->whereNotNull('queued_at')
            ->whereNotNull('delivered_at')
            ->selectRaw('AVG((julianday(delivered_at) - julianday(queued_at)) * 1440) as avg_mins')
            ->value('avg_mins');
        $avgTurnaroundLabel = $avgTurnaround < 60
            ? "{$avgTurnaround}m"
            : intdiv($avgTurnaround, 60) . 'h ' . ($avgTurnaround % 60) . 'm';

        $activeTuners = TunerProfile::where('status', 'live')->count();
        $totalTuners  = TunerProfile::count();
        $idleTuners   = $totalTuners - $activeTuners;

        $openDisputes = Dispute::where('status', 'open')->count();

        // ── 14-day sparkline series (real data) ─────────────────
        $dateFrom = now()->subDays(13)->startOfDay();
        $orderSpark = collect(range(0, 13))->map(function ($i) use ($dateFrom) {
            $day = $dateFrom->copy()->addDays($i);
            return Order::whereDate('created_at', $day)->count();
        })->all();

        $revenueSpark = collect(range(0, 13))->map(function ($i) use ($dateFrom) {
            $day = $dateFrom->copy()->addDays($i);
            return round(CreditTransaction::where('type', 'purchase')
                ->whereDate('created_at', $day)
                ->sum('amount_pennies') / 100, 2);
        })->all();

        $turnaroundSpark = collect(range(0, 13))->map(function ($i) use ($dateFrom) {
            $day = $dateFrom->copy()->addDays($i);
            return (int) (Order::where('status', 'delivered')
                ->whereNotNull('queued_at')
                ->whereNotNull('delivered_at')
                ->whereDate('delivered_at', $day)
                ->selectRaw('AVG((julianday(delivered_at) - julianday(queued_at)) * 1440) as avg_mins')
                ->value('avg_mins') ?? 0);
        })->all();

        $queueSpark = collect(range(0, 13))->map(function ($i) use ($dateFrom) {
            $day = $dateFrom->copy()->addDays($i);
            return Order::whereDate('created_at', '<=', $day)
                ->whereIn('status', ['queued', 'in_progress', 'review'])
                ->count();
        })->all();

        $tunersSpark = collect(range(0, 13))->map(fn () => $activeTuners)->all();

        $disputesSpark = collect(range(0, 13))->map(function ($i) use ($dateFrom) {
            $day = $dateFrom->copy()->addDays($i);
            return Dispute::where('status', 'open')
                ->whereDate('created_at', '<=', $day)
                ->count();
        })->all();

        return view('livewire.queue-table', [
            'orders'  => $orders,
            'counts'  => $counts,
            'tunerUsers' => $tunerUsers,
            // KPI headline values
            'ordersToday'         => $ordersToday,
            'revenueToday'        => $revenueToday,
            'avgTurnaroundLabel'  => $avgTurnaroundLabel,
            'activeTuners'        => $activeTuners,
            'totalTuners'         => $totalTuners,
            'idleTuners'          => $idleTuners,
            'openDisputes'        => $openDisputes,
            // sparkline series
            'charts'  => [
                'orders'     => $orderSpark,
                'revenue'    => $revenueSpark,
                'turnaround' => $turnaroundSpark,
                'queue'      => $queueSpark,
                'tuners'     => $tunersSpark,
                'disputes'   => $disputesSpark,
            ],
        ]);
    }
}
