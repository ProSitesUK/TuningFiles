<div class="page">
    <div class="page-head">
        <div>
            <h1 class="page-title">Resellers</h1>
            <p class="page-sub">Performance and management information</p>
        </div>
    </div>

    {{-- KPI strip --}}
    <div class="kpi-row" style="grid-template-columns: repeat(4, 1fr); margin-bottom:18px">
        <div class="card card-pad kpi">
            <div class="metric-label">Active resellers</div>
            <div class="kpi-value">{{ $totalResellers }}</div>
        </div>
        <div class="card card-pad kpi">
            <div class="metric-label">Sub-customers</div>
            <div class="kpi-value">{{ $totalSubCustomers }}</div>
        </div>
        <div class="card card-pad kpi">
            <div class="metric-label">Orders via resellers</div>
            <div class="kpi-value">{{ number_format($totalResellerOrders) }}</div>
        </div>
        <div class="card card-pad kpi">
            <div class="metric-label">Revenue via resellers</div>
            <div class="kpi-value">&pound;{{ number_format($totalResellerRevenue / 100) }}</div>
        </div>
    </div>

    {{-- Search --}}
    <div class="pane-search" style="margin-bottom:14px; max-width:400px">
        <x-icon name="search" size="13" />
        <input wire:model.live.debounce.250ms="search" placeholder="Search resellers..." />
    </div>

    <div class="grid-2" style="grid-template-columns: 1fr 1fr; align-items:start">
        {{-- Reseller table --}}
        <div class="card card-table" style="overflow-x:auto">
            <table class="t" style="width:100%">
                <thead><tr>
                    <th>Business</th>
                    <th>Owner</th>
                    <th class="num">Customers</th>
                    <th class="num">Orders</th>
                    <th class="num">Revenue</th>
                    <th>Status</th>
                </tr></thead>
                <tbody>
                @foreach ($resellers as $r)
                    <tr class="t-row t-row-link {{ $selected === $r->user_id ? 'cust-row-active' : '' }}"
                        wire:click="selectReseller({{ $r->user_id }})">
                        <td>
                            <div style="font-weight:600">{{ $r->business_name }}</div>
                            <div class="t-mute small mono">/t/{{ $r->slug }}</div>
                        </td>
                        <td>{{ $r->user?->name }}<br><span class="t-mute small">{{ $r->user?->email }}</span></td>
                        <td class="num">{{ $r->customer_count }}</td>
                        <td class="num">{{ $r->order_count }}</td>
                        <td class="num mono">&pound;{{ number_format($r->revenue_pennies / 100) }}</td>
                        <td>
                            @if ($r->subscription_status === 'active')
                                <span class="badge badge-success">active</span>
                            @elseif ($r->subscription_status === 'trialing')
                                <span class="badge badge-warning">trial</span>
                            @else
                                <span class="badge badge-neutral">{{ $r->subscription_status ?? 'none' }}</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        {{-- Detail panel --}}
        <div>
            @if ($selReseller)
                <div class="card card-pad">
                    <h2 style="font-size:18px; font-weight:600; margin:0 0 4px">{{ $selReseller->business_name }}</h2>
                    <div class="t-mute small">{{ $selReseller->user?->name }} &middot; {{ $selReseller->user?->email }}</div>
                    @if ($selReseller->website)
                        <div class="t-mute small mono" style="margin-top:2px">{{ $selReseller->website }}</div>
                    @endif

                    <div class="kpi-row" style="grid-template-columns:repeat(3,1fr); margin:14px 0">
                        <div class="card card-pad kpi" style="background:var(--surface-2)">
                            <div class="metric-label">Customers</div>
                            <div class="kpi-value">{{ $selReseller->customer_count }}</div>
                        </div>
                        <div class="card card-pad kpi" style="background:var(--surface-2)">
                            <div class="metric-label">Orders</div>
                            <div class="kpi-value">{{ $selReseller->order_count }}</div>
                        </div>
                        <div class="card card-pad kpi" style="background:var(--surface-2)">
                            <div class="metric-label">Commission paid</div>
                            <div class="kpi-value">&pound;{{ number_format($selCommissionPaid / 100) }}</div>
                        </div>
                    </div>

                    <div class="metric-label" style="margin-bottom:6px">Top customers</div>
                    @foreach ($selCustomers as $c)
                        <div style="display:flex; justify-content:space-between; padding:4px 0; font-size:13px">
                            <span>{{ $c->name }}</span>
                            <span class="mono t-mute">{{ $c->orders_count }} orders</span>
                        </div>
                    @endforeach

                    <div class="metric-label" style="margin-top:14px; margin-bottom:6px">Recent orders</div>
                    @foreach ($selRecentOrders as $o)
                        <div style="display:flex; justify-content:space-between; padding:4px 0; font-size:13px">
                            <span class="mono">#{{ $o->reference }}</span>
                            <span>{{ $o->customer?->name }}</span>
                            <span class="badge badge-{{ $o->status === 'delivered' ? 'success' : 'neutral' }}">{{ $o->status }}</span>
                            <span class="t-mute">{{ $o->created_at?->diffForHumans(short:true) }}</span>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="card card-pad" style="text-align:center; padding:40px">
                    <div class="empty-title">Select a reseller</div>
                    <div class="t-mute small">Click a row to see their performance detail.</div>
                </div>
            @endif
        </div>
    </div>
</div>
