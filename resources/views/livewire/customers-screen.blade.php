<div class="page page-flush">
    <div class="three-pane {{ $selected ? 'tp-make' : '' }}">
        {{-- LEFT --}}
        <div class="pane pane-l">
            <div class="pane-head">
                <h2 class="pane-title">Customers</h2>
                <span class="t-mute mono">{{ $customers->count() }}</span>
            </div>
            <div class="pane-search">
                <x-icon name="search" size="13" />
                <input wire:model.live.debounce.250ms="search" placeholder="Find customer…" />
            </div>
            <div class="chips chips-tight">
                @foreach ([
                    ['all',   'All'],
                    ['Pro',   'Pro'],
                    ['Trade', 'Trade'],
                    ['VIP',   'VIP'],
                    ['flag',  'Flag'],
                ] as [$id, $label])
                    <button type="button" wire:click="$set('filter', '{{ $id }}')"
                            class="chip chip-sm {{ $filter === $id ? 'chip-active' : '' }}">{{ $label }}</button>
                @endforeach
            </div>
            <div class="cust-list">
                @foreach ($customers as $u)
                    @php $cp = $u->customerProfile; @endphp
                    <button type="button" wire:click="selectCustomer({{ $u->id }})"
                            class="cust-row {{ $u->id === $selected ? 'cust-row-active' : '' }}">
                        <span class="avatar" style="width:32px;height:32px;font-size:11px">{{ $u->initials() }}</span>
                        <div class="cust-row-text">
                            <div class="cust-row-name">
                                {{ $u->name }}
                                @php $kind = $cp?->plan === 'VIP' ? 'purple' : ($cp?->plan === 'Trade' ? 'blue' : 'neutral'); @endphp
                                <span class="badge badge-{{ $kind }}">{{ $cp?->plan ?? 'Pro' }}</span>
                            </div>
                            <div class="cust-row-meta mono small">{{ $u->orders_count }} orders · £{{ number_format(($cp?->total_spent_pennies ?? 0) / 100) }}</div>
                        </div>
                        <span class="dot dot-ok"></span>
                    </button>
                @endforeach
            </div>
        </div>

        {{-- MIDDLE --}}
        <div class="pane pane-m">
            @if ($sel)
                @php $cp = $sel->customerProfile; @endphp
                <div class="pane-head">
                    <div style="display:flex; align-items:center; gap:8px">
                        <button type="button" wire:click="$set('selected', null)" class="tp-back">← List</button>
                        <div class="crumbs-sm">
                            <span>Customers</span>
                            <x-icon name="chevron" size="12" />
                            <span class="crumb-active">{{ $sel->name }}</span>
                        </div>
                    </div>
                    <button class="ghost-btn ghost-btn-sm" type="button">Impersonate</button>
                </div>
                <div class="pane-content">
                    <div class="cust-head">
                        <span class="avatar" style="width:44px;height:44px;font-size:14px">{{ $sel->initials() }}</span>
                        <div>
                            @php $kind = $cp?->plan === 'VIP' ? 'purple' : ($cp?->plan === 'Trade' ? 'blue' : 'neutral'); @endphp
                            <h2 class="cust-name">{{ $sel->name }} <span class="badge badge-{{ $kind }}">{{ $cp?->plan ?? 'Pro' }}</span></h2>
                            <div class="cust-meta mono small">
                                id {{ $sel->id }} · since {{ $cp?->since_at?->format('M Y') ?? '—' }} · {{ $cp?->credit_balance ?? 0 }} cr
                            </div>
                        </div>
                    </div>
                    <div class="cust-kpis">
                        <div class="cust-kpi"><div class="metric-label">Orders</div><div class="cust-kpi-val">{{ $sel->orders_count }}</div></div>
                        <div class="cust-kpi"><div class="metric-label">Revenue</div><div class="cust-kpi-val">£{{ number_format(($cp?->total_spent_pennies ?? 0) / 100) }}</div></div>
                        <div class="cust-kpi {{ $disputesCount > 0 ? 'cust-kpi-warn' : '' }}"><div class="metric-label">Disputes</div><div class="cust-kpi-val">{{ $disputesCount }}</div></div>
                        <div class="cust-kpi"><div class="metric-label">Refunds</div><div class="cust-kpi-val">{{ $refundsTotal > 0 ? '£'.$refundsTotal : '£0' }}</div></div>
                    </div>

                    <div class="cust-orders-head">
                        <div class="metric-label">Order history</div>
                        <span class="t-mute small">last 12</span>
                    </div>
                    <table class="t t-orders">
                        <thead><tr>
                            <th>Order</th>
                            <th>Vehicle / opt</th>
                            <th>Status</th>
                            <th class="num">Credits</th>
                        </tr></thead>
                        <tbody>
                            @forelse ($orders as $o)
                                <tr class="t-row t-row-link" wire:click="$dispatch('order:open', { id: {{ $o->id }} })">
                                    <td>
                                        <div class="mono">#{{ $o->reference }}</div>
                                        <div class="t-mute small">{{ $o->elapsedLabel() }} ago</div>
                                    </td>
                                    <td>
                                        <div>{{ $o->vehicle_label }}</div>
                                        <div class="t-mute small">{{ $o->options_label }}</div>
                                    </td>
                                    <td>@include('partials.status-badge', ['status' => $o->status])</td>
                                    <td class="num mono">{{ $o->credits_cost }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="empty-cell">No orders in current view</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            @else
                <div class="pane-empty">
                    <div class="empty-title">No customer selected</div>
                    <div class="t-mute small">Pick a customer from the left to see their detail.</div>
                </div>
            @endif
        </div>

        {{-- RIGHT --}}
        <div class="pane pane-r">
            @php $o = $orders->first(); @endphp
            @if ($o)
                <div class="pane-content">
                    <div class="crumbs-sm">
                        <span>Orders</span>
                        <x-icon name="chevron" size="12" />
                        <span class="crumb-active mono">#{{ $o->reference }}</span>
                    </div>
                    <h2 class="cust-name">Order #{{ $o->reference }}</h2>
                    <div class="cust-meta">
                        @include('partials.status-badge', ['status' => $o->status])
                        <span class="mono small t-mute">{{ $o->elapsedLabel() }} elapsed</span>
                    </div>

                    <div class="metas">
                        <div class="meta"><div class="metric-label">Vehicle</div><div class="meta-val">{{ $o->vehicle_label }} · {{ $o->vehicle_year }}</div></div>
                        <div class="meta"><div class="metric-label">ECU</div><div class="meta-val">{{ $o->ecu_label }}</div></div>
                        <div class="meta"><div class="metric-label">Options</div><div class="meta-val">{{ $o->options_label }}</div></div>
                        <div class="meta"><div class="metric-label">Origin</div><div class="meta-val">{{ $o->origin }}</div></div>
                        <div class="meta"><div class="metric-label">Credits</div><div class="meta-val mono">{{ $o->credits_cost }}</div></div>
                        <div class="meta"><div class="metric-label">SLA</div><div class="meta-val mono">{{ $o->elapsedLabel() }} / {{ $o->sla }}</div></div>
                    </div>

                    <div class="metric-label" style="margin-top:16px">Tune preview</div>
                    <div class="tune-mini">
                        <x-tune-chart :width="320" :height="120" :compact="true" />
                        <div class="tune-mini-foot">
                            <span class="legend-row legend-row-sm"><span class="legend-sw" style="background:var(--muted)"></span>Stock</span>
                            <span class="legend-row legend-row-sm"><span class="legend-sw" style="background:var(--accent)"></span>Tuned</span>
                            <span class="badge badge-green badge-soft">+52 hp</span>
                        </div>
                    </div>
                </div>
            @else
                <div class="pane-empty">
                    <div class="empty-title">No active order</div>
                    <div class="t-mute small">Select an order from the middle pane to see file details, timeline and a tune preview here.</div>
                </div>
            @endif
        </div>
    </div>
</div>
