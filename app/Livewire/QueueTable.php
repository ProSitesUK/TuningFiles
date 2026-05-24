<?php

namespace App\Livewire;

use App\Models\CreditTransaction;
use App\Models\Dispute;
use App\Models\Order;
use App\Models\TunerProfile;
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
