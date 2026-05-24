<div class="page" wire:poll.30s>
    <div class="page-head">
        <div>
            <h1 class="page-title">Overview</h1>
            <p class="page-sub">Updated just now · auto-refresh on</p>
        </div>
        <div class="seg">
            @foreach (['24h','7d','30d','QTD','YTD'] as $r)
                <button type="button" wire:click="$set('range', '{{ $r }}')"
                        class="seg-btn {{ $range === $r ? 'seg-btn-active' : '' }}">{{ $r }}</button>
            @endforeach
            <span class="seg-vs">vs prev period</span>
        </div>
    </div>

    {{-- ─────── Top row: bar chart + donut ─────── --}}
    <div class="grid-2">
        <div class="card card-pad">
            <div class="card-head">
                <div>
                    <div class="metric-label">Orders / day</div>
                    <div class="metric-big">{{ number_format($ordersCount) }} <span class="badge badge-{{ $ordersCountDelta >= 0 ? 'green' : 'red' }} badge-soft"><span class="badge-dot"></span> {{ $ordersCountDelta >= 0 ? '+' : '' }}{{ $ordersCountDelta }}% vs prev</span></div>
                </div>
                <div class="seg seg-sm">
                    <button class="seg-btn seg-btn-active" type="button">last 14d</button>
                </div>
            </div>
            <x-bar-chart :data="$series['orders']" />
        </div>

        <div class="card card-pad">
            <div class="card-head">
                <div class="metric-label">Files by stage</div>
                <button class="more-btn" type="button"><x-icon name="more" size="14" /></button>
            </div>
            <div class="donut-row">
                <div class="donut-wrap">
                    <x-donut :slices="$donutSlices" :size="150" :thickness="20" />
                    <div class="donut-center">
                        <div class="donut-big">{{ number_format($donutCenter) }}</div>
                        <div class="donut-sub">files total</div>
                    </div>
                </div>
                <div class="legend">
                    @foreach ($donutLegend as $item)
                        <div class="legend-row"><span class="legend-sw" style="background:{{ $item['color'] }}"></span><span>{{ $item['label'] }}</span><b>{{ $item['percent'] }}%</b></div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- ─────── KPI row ─────── --}}
    <div class="kpi-row">
        @php
            $kpis = [
                ['Revenue',       $revenueFormatted,      ($revenueDelta >= 0 ? '+' : '') . $revenueDelta . '%',    $revenueDelta >= 0 ? 'up' : 'down',    $series['revenue'],  'var(--accent)', true],
                ['ARPU',          $arpuFormatted,          ($arpuDelta >= 0 ? '+' : '') . $arpuDelta . '%',          $arpuDelta >= 0 ? 'up' : 'down',       $series['revenue'],  'var(--ink)',    false],
                ['Refund rate',   $refundRateFormatted,    ($refundRateDelta >= 0 ? '+' : '') . round($refundRateDelta, 1) . 'pp', $refundRateDelta <= 0 ? 'up' : 'down', $series['disputes'], 'var(--ink)', false],
                ['SLA hit',       $slaHitFormatted,        ($slaHitDelta >= 0 ? '+' : '') . round($slaHitDelta, 1) . 'pp',         $slaHitDelta >= 0 ? 'up' : 'down',    $series['tuners'],   'var(--ink)',    false],
            ];
        @endphp
        @foreach ($kpis as [$label, $val, $delta, $deltaKind, $data, $color, $fill])
            <div class="card card-pad kpi">
                <div class="card-head">
                    <div class="metric-label">{{ $label }}</div>
                    <button class="more-btn" type="button"><x-icon name="more" size="14" /></button>
                </div>
                <div class="kpi-value">{{ $val }} <span class="delta delta-{{ $deltaKind }}">{{ $delta }}</span></div>
                <div class="kpi-spark"><x-sparkline :data="$data" :color="$color" :fill="$fill" :height="36" /></div>
            </div>
        @endforeach
    </div>

    {{-- ─────── Bottom row: top customers + activity ─────── --}}
    <div class="grid-2-7-5">
        <div class="card">
            <div class="card-head card-pad-x">
                <div>
                    <div class="metric-label">Top customers · {{ $range }}</div>
                    <div class="metric-mid">By revenue</div>
                </div>
                <div class="card-head-r">
                    <button class="ghost-btn" type="button">by revenue ▾</button>
                    <button class="ghost-btn" type="button">View all</button>
                </div>
            </div>
            <table class="t">
                <thead><tr>
                    <th style="width:28px">#</th>
                    <th>Customer</th>
                    <th>Plan</th>
                    <th class="num">Orders</th>
                    <th class="num">Revenue</th>
                    <th class="num">Δ</th>
                </tr></thead>
                <tbody>
                    @foreach ($topCustomers as $i => $cp)
                        @php $u = $cp->user; $d = $deltas[$i] ?? 0; @endphp
                        <tr class="t-row">
                            <td class="t-mute">{{ $i + 1 }}</td>
                            <td><div class="cell-cust"><span class="avatar" style="width:24px;height:24px;font-size:9px">{{ $u?->initials() }}</span><span>{{ $u?->name }}</span></div></td>
                            <td>
                                @php $kind = $cp->plan === 'VIP' ? 'purple' : ($cp->plan === 'Trade' ? 'blue' : 'neutral'); @endphp
                                <span class="badge badge-{{ $kind }}">{{ $cp->plan }}</span>
                            </td>
                            <td class="num">{{ $u?->orders()->count() ?? 0 }}</td>
                            <td class="num mono">£{{ number_format($cp->total_spent_pennies / 100) }}</td>
                            <td class="num"><span class="delta delta-{{ $d >= 0 ? 'up' : 'down' }}">{{ $d >= 0 ? '+' : '' }}{{ $d }}%</span></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="card card-pad">
            <div class="card-head">
                <div class="metric-label">Live activity</div>
                <span class="badge badge-green badge-soft"><span class="badge-dot pulse"></span> streaming</span>
            </div>
            <div class="activity">
                @forelse ($activity as [$dot, $text, $ago])
                    <div class="act-row">
                        <span class="dot dot-{{ $dot }}"></span>
                        <div class="act-text">
                            <div>{{ $text }}</div>
                            <div class="t-mute">{{ $ago }}</div>
                        </div>
                    </div>
                @empty
                    <div class="act-row">
                        <span class="dot dot-mute"></span>
                        <div class="act-text">
                            <div>No recent activity</div>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
