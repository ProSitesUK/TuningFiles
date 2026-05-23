<div class="page">
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
                    <div class="metric-big">988 <span class="badge badge-green badge-soft"><span class="badge-dot"></span> +18% vs prev</span></div>
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
                    <x-donut
                        :slices="[
                            ['value' => 38, 'color' => 'var(--accent)'],
                            ['value' => 28, 'color' => 'var(--ink)'],
                            ['value' => 18, 'color' => 'var(--muted)'],
                            ['value' => 16, 'color' => 'var(--border-strong)'],
                        ]"
                        :size="150" :thickness="20" />
                    <div class="donut-center">
                        <div class="donut-big">412</div>
                        <div class="donut-sub">files / day</div>
                    </div>
                </div>
                <div class="legend">
                    <div class="legend-row"><span class="legend-sw" style="background:var(--accent)"></span><span>Stage 1</span><b>38%</b></div>
                    <div class="legend-row"><span class="legend-sw" style="background:var(--ink)"></span><span>Stage 2</span><b>28%</b></div>
                    <div class="legend-row"><span class="legend-sw" style="background:var(--muted)"></span><span>Custom</span><b>18%</b></div>
                    <div class="legend-row"><span class="legend-sw" style="background:var(--border-strong)"></span><span>Other</span><b>16%</b></div>
                </div>
            </div>
        </div>
    </div>

    {{-- ─────── KPI row ─────── --}}
    <div class="kpi-row">
        @php
            $kpis = [
                ['MRR',           '£48.4k', '+6.2% mom', 'up', $series['revenue'],  'var(--accent)', true],
                ['ARPU',          '£34',    '+£2',       'up', $series['revenue'],  'var(--ink)',    false],
                ['Refund rate',   '0.8%',   '−0.3pp',    'up', $series['disputes'], 'var(--ink)',    false],
                ['SLA hit',       '98.2%',  '+0.4pp',    'up', $series['tuners'],   'var(--ink)',    false],
                ['NPS · 30d',     '62',     '+4',        'up', $series['tuners'],   'var(--ink)',    false],
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
                    <div class="metric-label">Top customers · 30d</div>
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
                @foreach ($activity as [$dot, $text, $ago])
                    <div class="act-row">
                        <span class="dot dot-{{ $dot }}"></span>
                        <div class="act-text">
                            <div>{{ $text }}</div>
                            <div class="t-mute">{{ $ago }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
