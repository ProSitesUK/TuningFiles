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
            <div class="chips chips-tight" style="display:flex; align-items:center; flex-wrap:wrap">
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
                <select wire:model.live="resellerFilter" style="padding:5px 8px; border:1px solid var(--border); border-radius:var(--r-sm); background:var(--surface-2); font-size:12px; color:var(--ink); margin-left:8px">
                    <option value="all">All sources</option>
                    <option value="direct">Direct customers</option>
                    @foreach ($resellers as $r)
                        <option value="{{ $r->user_id }}">{{ $r->business_name }}</option>
                    @endforeach
                </select>
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
                            <div class="cust-row-meta mono small">{{ $u->orders_count }} orders · £{{ number_format(($cp?->total_spent_pennies ?? 0) / 100) }}
                                @if ($u->reseller_id)
                                    · <span class="badge badge-neutral" style="font-size:9px">{{ $u->reseller?->resellerProfile?->business_name ?? 'reseller' }}</span>
                                @endif
                            </div>
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

                    @if ($sel->reseller_id)
                        <div style="margin-bottom:14px; padding:8px 12px; background:var(--surface-2); border-radius:var(--r-sm); font-size:12.5px">
                            <span class="t-mute">Reseller:</span> <strong>{{ $sel->reseller?->resellerProfile?->business_name ?? '—' }}</strong>
                        </div>
                    @endif

                    {{-- Invoice permission toggle --}}
                    <div style="display:flex; align-items:center; gap:10px; margin-top:8px; padding:8px 12px; background:var(--bg); border:1px solid var(--border); border-radius:6px">
                        <label style="display:flex; align-items:center; gap:6px; cursor:pointer; font-size:13px; margin:0">
                            <input type="checkbox"
                                   wire:click="toggleCanInvoice({{ $sel->id }})"
                                   {{ ($cp?->can_invoice ?? false) ? 'checked' : '' }}
                                   style="margin:0" />
                            <span>Allow invoice payments</span>
                        </label>
                        <span class="t-mute small">Customer can request invoices instead of paying upfront</span>
                    </div>

                    {{-- Reseller status --}}
                    <div style="margin-top:14px; padding-top:14px; border-top:1px solid var(--border)">
                        <div class="metric-label" style="margin-bottom:8px">Reseller status</div>
                        @if ($sel->hasRole('reseller'))
                            <div style="display:flex; align-items:center; gap:8px; margin-bottom:8px">
                                <span class="badge badge-success">Reseller</span>
                                @if ($sel->resellerProfile)
                                    <span class="t-mute small">{{ $sel->resellerProfile->business_name }} · {{ $sel->subCustomers()->count() }} customers</span>
                                @endif
                            </div>
                            <button type="button" wire:click="removeReseller({{ $sel->id }})" wire:confirm="Remove reseller role? Their profile data will be kept." class="ghost-btn ghost-btn-sm" style="color:var(--danger)">Remove reseller role</button>
                        @else
                            <p class="t-mute small" style="margin-bottom:8px">This customer is not a reseller. Upgrading gives them their own tenant portal at /reseller.</p>
                            <button type="button" wire:click="makeReseller({{ $sel->id }})" class="primary-btn primary-btn-sm">Upgrade to reseller</button>
                        @endif
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
