<div class="page">
    <div class="page-head">
        <div>
            <h1 class="page-title">Reports</h1>
            <p class="page-sub">Operational analytics and performance metrics — read-only.</p>
        </div>
    </div>

    {{-- Top KPI strip --}}
    <div class="kpi-row">
        <div class="card card-pad kpi">
            <div class="metric-label">Total orders</div>
            <div class="kpi-value">{{ number_format($totalOrders) }}</div>
        </div>
        <div class="card card-pad kpi">
            <div class="metric-label">Avg turnaround</div>
            <div class="kpi-value">
                @if ($avgTurnaround >= 60)
                    {{ intdiv($avgTurnaround, 60) }}h {{ $avgTurnaround % 60 }}m
                @else
                    {{ $avgTurnaround }}m
                @endif
            </div>
        </div>
        <div class="card card-pad kpi">
            <div class="metric-label">SLA hit rate</div>
            <div class="kpi-value">{{ $slaRate }}%</div>
        </div>
        <div class="card card-pad kpi">
            <div class="metric-label">Files tuned today</div>
            <div class="kpi-value">{{ number_format($tunedToday) }}</div>
        </div>
        <div class="card card-pad kpi">
            <div class="metric-label">Active tuners</div>
            <div class="kpi-value">{{ number_format($activeTuners) }}</div>
        </div>
    </div>

    <div class="grid-2" style="margin-top: 16px">
        {{-- Orders by status --}}
        <div class="card card-table">
            <div class="card-head card-pad-x">
                <div class="metric-label">Orders by status</div>
            </div>
            <table class="t">
                <thead>
                    <tr>
                        <th>Status</th>
                        <th class="num">Count</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach (['queued','in_progress','review','ready','delivered','refunded'] as $status)
                        <tr class="t-row">
                            <td>
                                @php
                                    $badgeColor = match ($status) {
                                        'queued'      => 'badge-neutral',
                                        'in_progress' => 'badge-blue',
                                        'review'      => 'badge-warning',
                                        'ready'       => 'badge-purple',
                                        'delivered'   => 'badge-success',
                                        'refunded'    => 'badge-danger',
                                        default       => 'badge-neutral',
                                    };
                                @endphp
                                <span class="badge {{ $badgeColor }}">{{ str_replace('_', ' ', $status) }}</span>
                            </td>
                            <td class="num mono">{{ number_format($statusCounts[$status] ?? 0) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Top 5 tuners --}}
        <div class="card card-table">
            <div class="card-head card-pad-x">
                <div class="metric-label">Top 5 tuners</div>
            </div>
            <table class="t">
                <thead>
                    <tr>
                        <th>Tuner</th>
                        <th class="num">Completed</th>
                        <th class="num">Avg turnaround</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($topTuners as $tuner)
                        <tr class="t-row">
                            <td>{{ $tunerNames[$tuner->assigned_tuner_id] ?? '—' }}</td>
                            <td class="num mono">{{ number_format($tuner->completed) }}</td>
                            <td class="num mono">
                                @php $m = round($tuner->avg_mins); @endphp
                                @if ($m >= 60)
                                    {{ intdiv($m, 60) }}h {{ $m % 60 }}m
                                @else
                                    {{ $m }}m
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="t-mute" style="text-align:center; padding:24px">No delivered orders yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Top 10 vehicles --}}
    <div class="card card-table" style="margin-top: 16px">
        <div class="card-head card-pad-x">
            <div class="metric-label">Top 10 vehicles</div>
        </div>
        <table class="t">
            <thead>
                <tr>
                    <th style="width:28px">#</th>
                    <th>Vehicle</th>
                    <th class="num">Orders</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($topVehicles as $i => $v)
                    <tr class="t-row">
                        <td class="t-mute">{{ $i + 1 }}</td>
                        <td>{{ $v->vehicle_label }}</td>
                        <td class="num mono">{{ number_format($v->order_count) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="t-mute" style="text-align:center; padding:24px">No orders with vehicle data yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
