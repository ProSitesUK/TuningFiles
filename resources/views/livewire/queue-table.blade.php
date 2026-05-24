<div class="page">
    <div class="page-head">
        <div>
            <h1 class="page-title">Queue</h1>
            <p class="page-sub">Updated just now · auto-refresh on · {{ $orders->total() }} of {{ $counts['all'] }} files</p>
        </div>
        <div class="page-actions">
            <button class="ghost-btn" type="button">Today · {{ now()->format('M j') }} ▾</button>
            <button class="ghost-btn" type="button">All tuners ▾</button>
            <button class="ghost-btn" type="button"><x-icon name="download" size="14" /> Export CSV</button>
            <button class="primary-btn" type="button"><x-icon name="plus" size="14" /> Manual order</button>
        </div>
    </div>

    {{-- ─────── KPI strip ─────── --}}
    <div class="kpi-row kpi-row-tight">
        @php
            $kpis = [
                ['Orders / 24h',   (string) $ordersToday,                          '',  'up',   $charts['orders'],     'var(--accent)', false],
                ['Revenue / 24h',  $revenueToday,                                  '',  'up',   $charts['revenue'],    'var(--ink)',    false],
                ['Avg turnaround', $avgTurnaroundLabel,                             '',  'up',   $charts['turnaround'], 'var(--ink)',    false],
                ['Queue depth',    (string) $counts['all'],                         '',  'warn', $charts['queue'],      'var(--ink)',    false],
                ['Active tuners',  $activeTuners . ' / ' . $totalTuners, $idleTuners . ' idle', 'warn', $charts['tuners'], 'var(--ink)', false],
                ['Disputes open',  (string) $openDisputes,                          '',  'warn', $charts['disputes'],   'var(--ink)',    false],
            ];
        @endphp
        @foreach ($kpis as [$label, $val, $delta, $deltaKind, $series, $color, $fill])
            <div class="card card-pad kpi">
                <div class="card-head">
                    <div class="metric-label">{{ $label }}</div>
                    <button class="more-btn" type="button"><x-icon name="more" size="14" /></button>
                </div>
                <div class="kpi-value">{{ $val }} <span class="delta delta-{{ $deltaKind }}">{{ $delta }}</span></div>
                <div class="kpi-spark"><x-sparkline :data="$series" :color="$color" :fill="$fill" :height="36" /></div>
            </div>
        @endforeach
    </div>

    {{-- ─────── Filter bar ─────── --}}
    <div class="filterbar">
        <div class="chips">
            @foreach ([
                ['all',         'All'],
                ['queued',      'Queued'],
                ['in_progress', 'In progress'],
                ['review',      'Review'],
                ['ready',       'Ready'],
                ['failed',      'Failed'],
            ] as [$id, $label])
                <button type="button" wire:click="$set('filter', '{{ $id }}')"
                        class="chip {{ $filter === $id ? 'chip-active' : '' }}">
                    {{ $label }} <span class="chip-count">{{ $counts[$id] }}</span>
                </button>
            @endforeach
        </div>
        <div class="chips chips-r">
            <button class="ghost-btn ghost-btn-sm" type="button">Stage ▾</button>
            <button class="ghost-btn ghost-btn-sm" type="button">Tuner ▾</button>
            <button class="ghost-btn ghost-btn-sm" type="button">ECU vendor ▾</button>
            <button class="ghost-btn ghost-btn-sm" type="button">Date ▾</button>
            <button class="ghost-btn ghost-btn-sm ghost-btn-accent" type="button">Save view</button>
        </div>
    </div>

    {{-- ─────── Table ─────── --}}
    <div class="card card-table">
        <table class="t t-queue">
            <thead><tr>
                <th style="width:32px"><input type="checkbox" class="ck" disabled /></th>
                <th>Order ▾</th>
                <th>Customer</th>
                <th>Vehicle</th>
                <th>ECU</th>
                <th>Options</th>
                <th>Status</th>
                <th>Assignee</th>
                <th class="num">Credits</th>
                <th class="num">Elapsed</th>
                <th style="width:24px"></th>
            </tr></thead>
            <tbody>
                @forelse ($orders as $o)
                    @php $c = $o->customer; $t = $o->assignedTuner; @endphp
                    <tr class="t-row t-row-link" wire:click="$dispatch('order:open', { id: {{ $o->id }} })">
                        <td wire:click.stop>
                            <input type="checkbox" class="ck"
                                   wire:click="toggle({{ $o->id }})"
                                   {{ in_array($o->id, $selected) ? 'checked' : '' }} />
                        </td>
                        <td class="mono">#{{ $o->reference }}</td>
                        <td><div class="cell-cust"><span class="avatar" style="width:22px;height:22px;font-size:9px">{{ $c?->initials() }}</span><span>{{ $c?->name }}</span></div></td>
                        <td><div>{{ $o->vehicle_label }}<div class="t-mute mono small">{{ $o->vehicle_year }}</div></div></td>
                        <td class="mono small">{{ $o->ecu_label }}</td>
                        <td>{{ $o->options_label }}</td>
                        <td>@include('partials.status-badge', ['status' => $o->status])</td>
                        <td>
                            @if ($t)
                                <div class="cell-cust"><span class="avatar" style="width:20px;height:20px;font-size:9px">{{ $t->initials() }}</span><span>{{ explode(' ', $t->name)[0] }}</span></div>
                            @else
                                <span class="t-mute">unassigned</span>
                            @endif
                        </td>
                        <td class="num mono">{{ $o->credits_cost }}</td>
                        <td class="num mono">{{ $o->elapsedLabel() }}</td>
                        <td wire:click.stop><button class="more-btn" type="button"><x-icon name="more" size="14" /></button></td>
                    </tr>
                @empty
                    <tr><td colspan="11" class="empty-cell">No orders match this view</td></tr>
                @endforelse
            </tbody>
        </table>

        <div class="table-foot">
            <span class="t-mute">{{ $orders->total() }} of {{ $counts['all'] }} · {{ count($selected) }} selected</span>
            <div class="chips chips-r">
                <button class="ghost-btn ghost-btn-sm" type="button" {{ count($selected) ? '' : 'disabled' }}>Bulk assign</button>
                <button class="ghost-btn ghost-btn-sm" type="button" {{ count($selected) ? '' : 'disabled' }}>Export</button>
                <div class="pager">{!! $orders->onEachSide(1)->links() !!}</div>
            </div>
        </div>
    </div>
</div>
