<div>
    <div class="page">
        <div class="page-head">
            <div>
                <h1 class="page-title">Dashboard</h1>
                <p class="page-sub">Overview of your reseller account and customer activity.</p>
            </div>
            <div class="page-actions">
                <a href="{{ route('reseller.customers') }}" class="ghost-btn" style="text-decoration:none">View customers</a>
                <a href="{{ route('reseller.invite') }}" class="primary-btn" style="text-decoration:none"><x-icon name="plus" size="14" /> Invite customer</a>
            </div>
        </div>

        {{-- KPIs --}}
        <div class="kpi-row" style="grid-template-columns: repeat(4, 1fr); margin-bottom:18px">
            <div class="card card-pad kpi">
                <div class="metric-label">Total customers</div>
                <div class="kpi-value">{{ $totalCustomers }}</div>
            </div>
            <div class="card card-pad kpi">
                <div class="metric-label">Total orders</div>
                <div class="kpi-value">{{ $totalOrders }}</div>
            </div>
            <div class="card card-pad kpi">
                <div class="metric-label">Orders this month</div>
                <div class="kpi-value">{{ $ordersThisMonth }}</div>
            </div>
            <div class="card card-pad kpi">
                <div class="metric-label">Active customers</div>
                <div class="kpi-value">{{ $activeCustomers }} <span class="t-mute small">/ 30d</span></div>
            </div>
        </div>

        {{-- Recent orders --}}
        <div class="card card-table">
            <div class="card-head card-pad-x">
                <div>
                    <div class="metric-label">Recent orders</div>
                    <div class="metric-mid">Last 20</div>
                </div>
                <div class="card-head-r">
                    <a href="{{ route('reseller.orders') }}" class="ghost-btn" style="text-decoration:none">View all</a>
                </div>
            </div>
            <table class="t">
                <thead><tr>
                    <th>Ref</th>
                    <th>Customer</th>
                    <th>Vehicle</th>
                    <th>Status</th>
                    <th class="num">Credits</th>
                    <th class="num">Date</th>
                </tr></thead>
                <tbody>
                    @forelse ($recentOrders as $o)
                        <tr class="t-row">
                            <td class="mono">#{{ $o->reference }}</td>
                            <td>{{ $o->customer?->name ?? '—' }}</td>
                            <td>{{ $o->vehicle_label }}</td>
                            <td>@include('partials.status-badge', ['status' => $o->status])</td>
                            <td class="num mono">{{ $o->credits_cost }}</td>
                            <td class="num">{{ $o->created_at->format('d M Y') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="empty-cell">
                            No orders yet. Your customers' orders will appear here.
                        </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
