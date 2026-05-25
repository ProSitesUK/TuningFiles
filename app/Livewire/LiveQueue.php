<?php

namespace App\Livewire;

use App\Models\Order;
use App\Models\TunerProfile;
use Livewire\Component;

class LiveQueue extends Component
{
    public bool $paused = false;

    public function togglePause(): void
    {
        $this->paused = !$this->paused;
    }

    public function rebalance(): void
    {
        $tuners = TunerProfile::where('status', 'live')->with('user')->get();
        if ($tuners->isEmpty()) return;

        $unassigned = Order::whereNull('assigned_tuner_id')
            ->where('status', 'queued')
            ->get();

        $i = 0;
        foreach ($unassigned as $order) {
            $tuner = $tuners[$i % $tuners->count()];
            $order->update(['assigned_tuner_id' => $tuner->user_id, 'assigned_at' => now()]);
            $i++;
        }
    }

    public function render()
    {
        $orders = Order::with('customer', 'assignedTuner.tunerProfile')
            ->latest('queued_at')
            ->limit(40)
            ->get();

        $cols = [
            'queued'      => ['label' => 'Incoming',           'dot' => 'mute', 'orders' => []],
            'in_progress' => ['label' => 'In progress',        'dot' => 'warn', 'orders' => []],
            'review'      => ['label' => 'Review',             'dot' => 'err',  'orders' => []],
            'delivered'   => ['label' => 'Ready / Delivered',  'dot' => 'ok',   'orders' => []],
        ];

        foreach ($orders as $o) {
            $bucket = match ($o->status) {
                'queued'      => 'queued',
                'in_progress' => 'in_progress',
                'review'      => 'review',
                'ready', 'delivered' => 'delivered',
                default => null,
            };
            if ($bucket) $cols[$bucket]['orders'][] = $o;
        }

        $tuners = TunerProfile::with('user')->get()->sortBy(fn ($t) => match ($t->status) { 'live' => 0, 'busy' => 1, 'away' => 2, default => 3 });

        // strip stats
        $intakeHr      = Order::where('queued_at', '>=', now()->subHour())->count();
        $inProgress    = $cols['in_progress']['orders'] ? count($cols['in_progress']['orders']) : 0;
        $breaches      = Order::where('breach', true)->count();
        $refundsToday  = Order::whereDate('refunded_at', today())->sum('credits_cost');
        $refundsTodayN = Order::whereDate('refunded_at', today())->count();

        return view('livewire.live-queue', [
            'cols'          => $cols,
            'tuners'        => $tuners,
            'intakeHr'      => $intakeHr,
            'inProgress'    => count($cols['in_progress']['orders']),
            'breaches'      => $breaches,
            'refundsToday'  => $refundsToday,
            'refundsTodayN' => $refundsTodayN,
        ]);
    }
}
