<?php

namespace App\Livewire;

use App\Models\CreditTransaction;
use App\Models\CustomerProfile;
use App\Models\Order;
use App\Models\OrderEvent;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Livewire\Component;

class AdminOverview extends Component
{
    public string $range = '7d';
    public string $chartRange = '14d';
    public string $customerSort = 'revenue';

    /**
     * Return [$start, $end, $prevStart, $prevEnd] for the selected range.
     * $end is always "now"; $prevStart/$prevEnd cover an equally-sized
     * window immediately before $start for delta calculations.
     */
    private function dateRange(): array
    {
        $end = now();

        $start = match ($this->range) {
            '24h' => now()->subHours(24),
            '7d'  => now()->subDays(7),
            '30d' => now()->subDays(30),
            'QTD' => now()->startOfQuarter(),
            'YTD' => now()->startOfYear(),
            default => now()->subDays(7),
        };

        $spanSeconds = $end->diffInSeconds($start);
        $prevEnd     = $start->copy();
        $prevStart   = $start->copy()->subSeconds($spanSeconds);

        return [$start, $end, $prevStart, $prevEnd];
    }

    /**
     * Compute a percentage delta between two values.
     * Returns an integer (rounded) — positive = growth, negative = decline.
     */
    private function delta(float|int $current, float|int $previous): int
    {
        if ($previous == 0) {
            return $current > 0 ? 100 : 0;
        }

        return (int) round((($current - $previous) / abs($previous)) * 100);
    }

    /**
     * Format a pennies amount as a human-readable GBP string.
     *   < 1 000 00 → £XXX
     *   >= 1 000 00 → £X.Xk
     */
    private function formatPennies(int $pennies): string
    {
        $pounds = $pennies / 100;
        if ($pounds >= 1000) {
            return '£' . number_format($pounds / 1000, 1) . 'k';
        }

        return '£' . number_format($pounds, 0);
    }

    /**
     * Format a credits value as a GBP amount (1 credit = £1).
     */
    private function formatCredits(float|null $credits): string
    {
        return '£' . number_format($credits ?? 0, 0);
    }

    /**
     * Build a daily series of counts/sums over the given period.
     * Returns an array of numbers, one per day.
     */
    private function dailySeries(string $model, string $dateCol, Carbon $from, Carbon $to, string $aggregate = 'count', string $valueCol = '*'): array
    {
        $query = $model::whereBetween($dateCol, [$from, $to]);

        if ($aggregate === 'count') {
            $rows = $query
                ->selectRaw("DATE({$dateCol}) as d, COUNT(*) as v")
                ->groupByRaw("DATE({$dateCol})")
                ->orderBy('d')
                ->pluck('v', 'd');
        } else {
            $rows = $query
                ->selectRaw("DATE({$dateCol}) as d, {$aggregate}({$valueCol}) as v")
                ->groupByRaw("DATE({$dateCol})")
                ->orderBy('d')
                ->pluck('v', 'd');
        }

        // Fill in zero-gaps for every day in the range
        $series = [];
        $cursor = $from->copy()->startOfDay();
        $endDay = $to->copy()->startOfDay();
        while ($cursor->lte($endDay)) {
            $key      = $cursor->format('Y-m-d');
            $series[] = (float) ($rows[$key] ?? 0);
            $cursor->addDay();
        }

        return $series;
    }

    public function render()
    {
        [$start, $end, $prevStart, $prevEnd] = $this->dateRange();

        // ── KPIs — current period ────────────────────────────────────
        $ordersCount = Order::whereBetween('created_at', [$start, $end])->count();

        $revenue = (int) CreditTransaction::where('type', 'purchase')
            ->whereBetween('created_at', [$start, $end])
            ->sum('amount_pennies');

        $deliveredCount = Order::where('status', 'delivered')
            ->whereBetween('delivered_at', [$start, $end])
            ->count();

        $arpu = Order::where('status', 'delivered')
            ->whereBetween('delivered_at', [$start, $end])
            ->avg('credits_cost');

        $refundedCount = Order::where('status', 'refunded')
            ->whereBetween('created_at', [$start, $end])
            ->count();

        $refundRate = $deliveredCount > 0
            ? ($refundedCount / max(1, $deliveredCount)) * 100
            : 0;

        $slaHitCount = Order::where('status', 'delivered')
            ->whereColumn('delivered_at', '<=', 'sla_due_at')
            ->whereBetween('delivered_at', [$start, $end])
            ->count();

        $slaHit = $deliveredCount > 0
            ? ($slaHitCount / max(1, $deliveredCount)) * 100
            : 0;

        // ── KPIs — previous period ───────────────────────────────────
        $prevOrdersCount = Order::whereBetween('created_at', [$prevStart, $prevEnd])->count();

        $prevRevenue = (int) CreditTransaction::where('type', 'purchase')
            ->whereBetween('created_at', [$prevStart, $prevEnd])
            ->sum('amount_pennies');

        $prevDeliveredCount = Order::where('status', 'delivered')
            ->whereBetween('delivered_at', [$prevStart, $prevEnd])
            ->count();

        $prevArpu = Order::where('status', 'delivered')
            ->whereBetween('delivered_at', [$prevStart, $prevEnd])
            ->avg('credits_cost') ?? 0;

        $prevRefundedCount = Order::where('status', 'refunded')
            ->whereBetween('created_at', [$prevStart, $prevEnd])
            ->count();

        $prevRefundRate = $prevDeliveredCount > 0
            ? ($prevRefundedCount / max(1, $prevDeliveredCount)) * 100
            : 0;

        $prevSlaHitCount = Order::where('status', 'delivered')
            ->whereColumn('delivered_at', '<=', 'sla_due_at')
            ->whereBetween('delivered_at', [$prevStart, $prevEnd])
            ->count();

        $prevSlaHit = $prevDeliveredCount > 0
            ? ($prevSlaHitCount / max(1, $prevDeliveredCount)) * 100
            : 0;

        // ── Deltas ───────────────────────────────────────────────────
        $ordersCountDelta  = $this->delta($ordersCount, $prevOrdersCount);
        $revenueDelta      = $this->delta($revenue, $prevRevenue);
        $arpuDelta         = $this->delta($arpu ?? 0, $prevArpu);
        $refundRateDelta   = round($refundRate - $prevRefundRate, 1);   // pp change
        $slaHitDelta       = round($slaHit - $prevSlaHit, 1);          // pp change

        // ── Formatted KPIs ───────────────────────────────────────────
        $revenueFormatted  = $this->formatPennies($revenue);
        $arpuFormatted     = $this->formatCredits($arpu);
        $refundRateFormatted = number_format($refundRate, 1) . '%';
        $slaHitFormatted   = number_format($slaHit, 1) . '%';

        // ── Donut: files by stage ────────────────────────────────────
        $stageLabels = [
            'stage_1' => 'Stage 1',
            'stage_2' => 'Stage 2',
            'custom'  => 'Custom',
        ];
        $donutColors = [
            'var(--accent)',
            'var(--ink)',
            'var(--muted)',
            'var(--border-strong)',
        ];

        $stageCounts = Order::whereBetween('created_at', [$start, $end])
            ->whereNotNull('options')
            ->get(['options'])
            ->flatMap(fn ($o) => $o->options ?? [])
            ->countBy()
            ->sortDesc()
            ->take(4);

        $donutTotal  = $stageCounts->sum() ?: 1;
        $donutSlices = [];
        $donutLegend = [];
        $idx = 0;
        foreach ($stageCounts as $key => $count) {
            $label   = $stageLabels[$key] ?? ucfirst(str_replace('_', ' ', $key));
            $percent = round(($count / $donutTotal) * 100);
            $color   = $donutColors[$idx] ?? 'var(--border)';

            $donutSlices[] = ['value' => $count, 'color' => $color];
            $donutLegend[] = ['label' => $label, 'percent' => $percent, 'color' => $color];
            $idx++;
        }

        // Fallback if no data
        if (empty($donutSlices)) {
            $donutSlices = [['value' => 1, 'color' => 'var(--border)']];
            $donutLegend = [['label' => 'No data', 'percent' => 100, 'color' => 'var(--border)']];
        }

        $donutCenter = $stageCounts->sum();

        // ── Activity feed ────────────────────────────────────────────
        $activity = OrderEvent::with('order:id,reference')
            ->latest('happened_at')
            ->limit(10)
            ->get()
            ->map(fn ($e) => [
                $e->state === 'done' ? 'ok' : ($e->state === 'active' ? 'warn' : 'mute'),
                "#{$e->order?->reference} — {$e->stage}" . ($e->note ? " · {$e->note}" : ''),
                $e->happened_at->diffForHumans(short: true),
            ])
            ->toArray();

        // ── Chart series (period based on chartRange) ────────────────
        $chartDays = match ($this->chartRange) {
            '7d'  => 6,
            '14d' => 13,
            '30d' => 29,
            default => 13,
        };
        $chartStart = now()->subDays($chartDays)->startOfDay();
        $chartEnd   = now();

        $orderSeries     = $this->dailySeries(Order::class, 'created_at', $chartStart, $chartEnd);
        $revenueSeries   = $this->dailySeries(CreditTransaction::class, 'created_at', $chartStart, $chartEnd, 'sum', 'amount_pennies');
        // Convert revenue series from pennies to pounds for display
        $revenueSeries   = array_map(fn ($v) => round($v / 100, 2), $revenueSeries);

        // Turnaround: average minutes between queued_at and delivered_at per day
        $turnaroundSeries = $this->turnaroundSeries($chartStart, $chartEnd);

        // Disputes per day
        $disputesSeries = Order::where('status', 'dispute')
            ->whereBetween('created_at', [$chartStart, $chartEnd])
            ->selectRaw("DATE(created_at) as d, COUNT(*) as v")
            ->groupByRaw("DATE(created_at)")
            ->orderBy('d')
            ->pluck('v', 'd');

        $disputesArr = [];
        $cursor = $chartStart->copy()->startOfDay();
        while ($cursor->lte($chartEnd->copy()->startOfDay())) {
            $disputesArr[] = (int) ($disputesSeries[$cursor->format('Y-m-d')] ?? 0);
            $cursor->addDay();
        }

        // Active tuners per day (distinct assigned_tuner_id on orders started that day)
        $tunersSeries = Order::whereNotNull('assigned_tuner_id')
            ->whereBetween('created_at', [$chartStart, $chartEnd])
            ->selectRaw("DATE(created_at) as d, COUNT(DISTINCT assigned_tuner_id) as v")
            ->groupByRaw("DATE(created_at)")
            ->orderBy('d')
            ->pluck('v', 'd');

        $tunersArr = [];
        $cursor = $chartStart->copy()->startOfDay();
        while ($cursor->lte($chartEnd->copy()->startOfDay())) {
            $tunersArr[] = (int) ($tunersSeries[$cursor->format('Y-m-d')] ?? 0);
            $cursor->addDay();
        }

        $series = [
            'orders'     => $orderSeries,
            'revenue'    => $revenueSeries,
            'turnaround' => $turnaroundSeries,
            'tuners'     => $tunersArr,
            'disputes'   => $disputesArr,
        ];

        // ── Top customers ────────────────────────────────────────────
        $topCustomersQuery = CustomerProfile::with('user');
        if ($this->customerSort === 'orders') {
            $topCustomersQuery->orderByDesc(
                Order::selectRaw('count(*)')
                    ->whereColumn('orders.customer_id', 'customer_profiles.user_id')
            );
        } else {
            $topCustomersQuery->orderByDesc('total_spent_pennies');
        }
        $topCustomers = $topCustomersQuery->limit(7)->get();

        // Compute delta for each top customer: current vs previous period spend
        $deltas = $topCustomers->map(function ($cp) use ($start, $end, $prevStart, $prevEnd) {
            $userId = $cp->user_id;

            $currentSpend = (int) CreditTransaction::where('user_id', $userId)
                ->where('type', 'purchase')
                ->whereBetween('created_at', [$start, $end])
                ->sum('amount_pennies');

            $prevSpend = (int) CreditTransaction::where('user_id', $userId)
                ->where('type', 'purchase')
                ->whereBetween('created_at', [$prevStart, $prevEnd])
                ->sum('amount_pennies');

            return $this->delta($currentSpend, $prevSpend);
        })->toArray();

        return view('livewire.admin-overview', [
            'ordersCount'          => $ordersCount,
            'ordersCountDelta'     => $ordersCountDelta,
            'revenueFormatted'     => $revenueFormatted,
            'revenueDelta'         => $revenueDelta,
            'arpuFormatted'        => $arpuFormatted,
            'arpuDelta'            => $arpuDelta,
            'refundRateFormatted'  => $refundRateFormatted,
            'refundRateDelta'      => $refundRateDelta,
            'slaHitFormatted'      => $slaHitFormatted,
            'slaHitDelta'          => $slaHitDelta,
            'donutSlices'          => $donutSlices,
            'donutLegend'          => $donutLegend,
            'donutCenter'          => $donutCenter,
            'topCustomers'         => $topCustomers,
            'deltas'               => $deltas,
            'series'               => $series,
            'activity'             => $activity,
        ]);
    }

    /**
     * Average turnaround in minutes per day for delivered orders.
     */
    private function turnaroundSeries(Carbon $from, Carbon $to): array
    {
        $rows = Order::where('status', 'delivered')
            ->whereNotNull('queued_at')
            ->whereNotNull('delivered_at')
            ->whereBetween('delivered_at', [$from, $to])
            ->selectRaw("DATE(delivered_at) as d, AVG((JULIANDAY(delivered_at) - JULIANDAY(queued_at)) * 1440) as v")
            ->groupByRaw("DATE(delivered_at)")
            ->orderBy('d')
            ->pluck('v', 'd');

        $series = [];
        $cursor = $from->copy()->startOfDay();
        $endDay = $to->copy()->startOfDay();
        while ($cursor->lte($endDay)) {
            $series[] = round((float) ($rows[$cursor->format('Y-m-d')] ?? 0), 1);
            $cursor->addDay();
        }

        return $series;
    }
}
