<div>
    <div class="page-head" style="margin-bottom:18px">
        <div>
            <h1 class="page-title">Welcome back, {{ explode(' ', $user->name)[0] }}</h1>
            <p class="page-sub">Pick up where you left off — or upload a new file.</p>
            @if (auth()->user()->hasReseller())
                @php $rp = auth()->user()->reseller?->resellerProfile; @endphp
                @if ($rp)
                    <div class="reseller-badge">
                        @if ($rp->logo_url)
                            <img src="{{ $rp->logo_url }}" alt="{{ $rp->business_name }}" class="reseller-badge-logo" />
                        @endif
                        <span>Managed by {{ $rp->business_name }}</span>
                    </div>
                @endif
            @endif
        </div>
        <div class="page-actions">
            @php $ss = \App\Models\User::supportStatus(); @endphp
            <span class="support-pill support-pill-{{ $ss }}">
                <span class="dot dot-{{ $ss === 'online' ? 'ok' : ($ss === 'away' ? 'warn' : 'mute') }}"></span>
                Support is {{ $ss }}
            </span>
            <a href="{{ route('app.credits') }}" class="ghost-btn" style="text-decoration:none">Buy credits</a>
            <a href="{{ route('app.orders.new') }}" class="primary-btn" style="text-decoration:none"><x-icon name="plus" size="14" /> New tune</a>
        </div>
    </div>

    {{-- KPIs --}}
    <div class="kpi-row" style="grid-template-columns: repeat(4, 1fr); margin-bottom:18px">
        <div class="card card-pad kpi">
            <div class="metric-label">Credit balance</div>
            <div class="kpi-value">{{ $profile?->credit_balance ?? 0 }} <span class="t-mute small">cr</span></div>
        </div>
        <div class="card card-pad kpi">
            <div class="metric-label">Plan</div>
            <div class="kpi-value">{{ $profile?->plan ?? 'Pro' }}</div>
        </div>
        <div class="card card-pad kpi">
            <div class="metric-label">Files tuned</div>
            <div class="kpi-value">{{ $orders->count() }}</div>
        </div>
        <div class="card card-pad kpi">
            <div class="metric-label">Total spent</div>
            <div class="kpi-value">£{{ number_format(($profile?->total_spent_pennies ?? 0) / 100) }}</div>
        </div>
    </div>

    {{-- Recent orders --}}
    <div class="card card-table">
        <div class="card-head card-pad-x">
            <div>
                <div class="metric-label">Recent orders</div>
                <div class="metric-mid">Last 8</div>
            </div>
            <div class="card-head-r">
                <a href="{{ route('app.orders.index') }}" class="ghost-btn" style="text-decoration:none">View all</a>
            </div>
        </div>
        <table class="t">
            <thead><tr>
                <th>Order</th>
                <th>Vehicle</th>
                <th>Options</th>
                <th>Status</th>
                <th class="num">Credits</th>
                <th class="num">Elapsed</th>
            </tr></thead>
            <tbody>
                @forelse ($orders as $o)
                    <tr class="t-row t-row-link" wire:click.window="$dispatch('navigate-order', { id: {{ $o->id }} })">
                        <td class="mono">#{{ $o->reference }}</td>
                        <td>{{ $o->vehicle_label }} <span class="t-mute mono small">· {{ $o->vehicle_year }}</span></td>
                        <td>{{ $o->options_label }}</td>
                        <td>@include('partials.status-badge', ['status' => $o->status])</td>
                        <td class="num mono">{{ $o->credits_cost }}</td>
                        <td class="num mono">{{ $o->elapsedLabel() }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="empty-cell">
                        No orders yet. <a href="{{ route('app.orders.new') }}" style="color:var(--accent)">Start your first tune →</a>
                    </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
