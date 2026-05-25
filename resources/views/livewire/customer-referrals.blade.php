<div class="page">
    <div class="page-head" style="margin-bottom:18px">
        <div>
            <h1 class="page-title">Referrals</h1>
            <p class="page-sub">Track your referrals and commission earnings.</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('app.dashboard') }}" class="ghost-btn" style="text-decoration:none">Back to dashboard</a>
        </div>
    </div>

    {{-- KPIs --}}
    <div class="kpi-row" style="grid-template-columns: repeat(3, 1fr); margin-bottom:18px">
        <div class="card card-pad kpi">
            <div class="metric-label">Total referred</div>
            <div class="kpi-value">{{ $totalReferred }}</div>
        </div>
        <div class="card card-pad kpi">
            <div class="metric-label">Active referrals</div>
            <div class="kpi-value">{{ $activeReferrals }}</div>
        </div>
        <div class="card card-pad kpi">
            <div class="metric-label">Total commission earned</div>
            <div class="kpi-value">&pound;{{ number_format($totalCommission / 100, 2) }}</div>
        </div>
    </div>

    {{-- Referral link --}}
    <div class="card card-pad" style="margin-bottom:18px">
        <div class="metric-label" style="margin-bottom:6px">Your referral link</div>
        <p class="t-mute small" style="margin-bottom:8px">Share this link with other workshops. You'll earn ongoing commission on everything they spend.</p>
        <div style="display:flex; gap:8px">
            <input type="text" readonly value="{{ auth()->user()->referralUrl() }}" style="flex:1; padding:7px 10px; border:1px solid var(--border); border-radius:var(--r-sm); background:var(--surface-2); font-size:12px; font-family:var(--font-mono)" />
            <button type="button" onclick="navigator.clipboard.writeText('{{ auth()->user()->referralUrl() }}')" class="ghost-btn ghost-btn-sm">Copy</button>
        </div>
    </div>

    {{-- Referrals table --}}
    <div class="card card-table">
        <div class="card-head card-pad-x">
            <div>
                <div class="metric-label">Your referrals</div>
            </div>
        </div>
        <table class="t">
            <thead><tr>
                <th>Name</th>
                <th>Status</th>
                <th class="num">Total spend</th>
                <th class="num">Commission earned</th>
                <th>Current tier</th>
                <th>Progress to next</th>
            </tr></thead>
            <tbody>
                @forelse ($referrals as $ref)
                    <tr>
                        <td>{{ $ref->referred?->name ?? 'Unknown' }}</td>
                        <td>
                            <span class="status-badge status-{{ $ref->status === 'credited' ? 'ready' : 'queued' }}">{{ ucfirst($ref->status) }}</span>
                        </td>
                        <td class="num mono">&pound;{{ $ref->spendFormatted() }}</td>
                        <td class="num mono">&pound;{{ $ref->commissionFormatted() }}</td>
                        <td>{{ $ref->tierLabel() }}</td>
                        <td style="min-width:140px">
                            @php $progress = $ref->progressToNextTier(); $next = $ref->nextTier(); @endphp
                            @if ($next)
                                <div style="display:flex; align-items:center; gap:8px">
                                    <div style="flex:1; height:6px; background:var(--surface-2); border-radius:3px; overflow:hidden">
                                        <div style="width:{{ $progress }}%; height:100%; background:var(--accent); border-radius:3px"></div>
                                    </div>
                                    <span class="t-mute small">{{ $progress }}%</span>
                                </div>
                                <span class="t-mute small">Next: {{ $next['label'] }} ({{ $next['percent'] }}%)</span>
                            @else
                                <span class="t-mute small">Max tier reached</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="empty-cell">
                        No referrals yet. Share your link above to start earning commission.
                    </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
