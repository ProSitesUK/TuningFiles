<?php

namespace App\Livewire;

use App\Models\Order;
use App\Models\TunerProfile;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class AdminReports extends Component
{
    public function render()
    {
        // --- Top KPI strip ---
        $totalOrders = Order::count();

        $isSqlite = DB::connection()->getDriverName() === 'sqlite';
        $diffExpr = $isSqlite
            ? '(julianday(delivered_at) - julianday(queued_at)) * 1440'
            : 'TIMESTAMPDIFF(MINUTE, queued_at, delivered_at)';

        $avgTurnaround = Order::where('status', 'delivered')
            ->whereNotNull('queued_at')
            ->whereNotNull('delivered_at')
            ->selectRaw("AVG({$diffExpr}) as avg_mins")
            ->value('avg_mins');
        $avgTurnaround = $avgTurnaround ? round($avgTurnaround) : 0;

        // SLA hit rate: delivered orders where delivered_at <= sla_due_at / total delivered
        $totalDelivered = Order::where('status', 'delivered')->count();
        $slaHits = Order::where('status', 'delivered')
            ->whereNotNull('sla_due_at')
            ->whereNotNull('delivered_at')
            ->whereColumn('delivered_at', '<=', 'sla_due_at')
            ->count();
        $slaRate = $totalDelivered > 0 ? round(($slaHits / $totalDelivered) * 100, 1) : 0;

        // Files tuned today
        $tunedToday = Order::where('status', 'delivered')
            ->whereDate('delivered_at', today())
            ->count();

        // Active tuners
        $activeTuners = TunerProfile::where('status', 'live')->count();

        // --- Orders by status ---
        $statusCounts = Order::select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        // --- Top 10 vehicles ---
        $topVehicles = Order::select('vehicle_label', DB::raw('COUNT(*) as order_count'))
            ->whereNotNull('vehicle_label')
            ->where('vehicle_label', '!=', '')
            ->groupBy('vehicle_label')
            ->orderByDesc('order_count')
            ->limit(10)
            ->get();

        // --- Top 5 tuners ---
        $topTuners = Order::where('status', 'delivered')
            ->whereNotNull('assigned_tuner_id')
            ->whereNotNull('queued_at')
            ->whereNotNull('delivered_at')
            ->select(
                'assigned_tuner_id',
                DB::raw('COUNT(*) as completed'),
                DB::raw("AVG({$diffExpr}) as avg_mins"),
            )
            ->groupBy('assigned_tuner_id')
            ->orderByDesc('completed')
            ->limit(5)
            ->get();

        // Load tuner user names
        $tunerIds = $topTuners->pluck('assigned_tuner_id');
        $tunerNames = User::whereIn('id', $tunerIds)->pluck('name', 'id');

        return view('livewire.admin-reports', [
            'totalOrders'    => $totalOrders,
            'avgTurnaround'  => $avgTurnaround,
            'slaRate'        => $slaRate,
            'tunedToday'     => $tunedToday,
            'activeTuners'   => $activeTuners,
            'statusCounts'   => $statusCounts,
            'topVehicles'    => $topVehicles,
            'topTuners'      => $topTuners,
            'tunerNames'     => $tunerNames,
        ]);
    }
}
