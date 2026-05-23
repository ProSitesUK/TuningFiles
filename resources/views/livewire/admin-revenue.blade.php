<div class="page">
    <div class="page-head">
        <div>
            <h1 class="page-title">Revenue</h1>
            <p class="page-sub">Transaction history and revenue metrics — read-only.</p>
        </div>
    </div>

    {{-- KPI strip --}}
    <div class="kpi-row">
        <div class="card card-pad kpi">
            <div class="metric-label">Total revenue</div>
            <div class="kpi-value">&pound;{{ number_format($totalRevenue / 100) }}</div>
        </div>
        <div class="card card-pad kpi">
            <div class="metric-label">Avg order value</div>
            <div class="kpi-value">{{ $avgOrderValue !== null ? number_format($avgOrderValue, 1) : '0' }} <span class="t-mute small">credits</span></div>
        </div>
        <div class="card card-pad kpi">
            <div class="metric-label">Refund rate</div>
            <div class="kpi-value">{{ $refundRate }}%</div>
        </div>
        <div class="card card-pad kpi">
            <div class="metric-label">Active customers</div>
            <div class="kpi-value">{{ number_format($activeCustomers) }}</div>
        </div>
    </div>

    {{-- Filter chips --}}
    <div class="chips" style="margin-bottom: 14px">
        @foreach ([['all','All'],['purchase','Purchase'],['spend','Spend'],['refund','Refund'],['adjust','Adjust']] as [$id,$label])
            <button type="button" wire:click="$set('filter', '{{ $id }}')"
                    class="chip chip-sm {{ $filter === $id ? 'chip-active' : '' }}">{{ $label }}</button>
        @endforeach
    </div>

    {{-- Transaction log --}}
    <div class="card card-table">
        <table class="t">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Customer</th>
                    <th>Type</th>
                    <th class="num">Credits</th>
                    <th class="num">Amount</th>
                    <th>Note</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($transactions as $tx)
                    <tr class="t-row">
                        <td class="mono small">{{ $tx->created_at->format('j M Y H:i') }}</td>
                        <td>{{ $tx->user?->name ?? '—' }}</td>
                        <td>
                            @php
                                $badgeColor = match ($tx->type) {
                                    'purchase' => 'badge-success',
                                    'spend'    => 'badge-blue',
                                    'refund'   => 'badge-danger',
                                    'adjust'   => 'badge-warning',
                                    'promo'    => 'badge-purple',
                                    default    => 'badge-neutral',
                                };
                            @endphp
                            <span class="badge {{ $badgeColor }}">{{ $tx->type }}</span>
                        </td>
                        <td class="num mono">{{ $tx->credits > 0 ? '+' : '' }}{{ $tx->credits }}</td>
                        <td class="num mono">{{ $tx->amount_pennies ? '£'.number_format($tx->amount_pennies / 100, 2) : '—' }}</td>
                        <td class="t-mute small">{{ Str::limit($tx->note, 60) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align:center; padding:32px">
                            <span class="t-mute">No transactions found.</span>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
