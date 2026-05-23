<div>
    <div class="page-head" style="margin-bottom:18px">
        <div>
            <h1 class="page-title">Credits</h1>
            <p class="page-sub">Buy a pack — spend it as you go.</p>
        </div>
    </div>

    @if (session('status'))
        <div class="card card-pad" style="background:var(--success-soft); border-color:transparent; margin-bottom:18px; color:var(--success)">
            {{ session('status') }}
        </div>
    @endif

    <div class="card card-pad" style="margin-bottom:18px">
        <div class="metric-label">Current balance</div>
        <div class="metric-big">{{ $balance }} <span class="t-mute small" style="font-size:14px; font-family:var(--font-sans)">credits</span></div>
    </div>

    <div class="metric-label" style="margin-bottom:10px">Credit packs</div>
    <div class="mk-tiers" style="margin-bottom:24px">
        @foreach ($packs as $p)
            <div class="mk-tier {{ $p->slug === 'trade' ? 'mk-tier-featured' : '' }}">
                @if ($p->slug === 'trade') <div class="mk-tier-flag">Most popular</div> @endif
                <div class="mk-tier-head">
                    <div class="mk-tier-plan">{{ $p->name }}</div>
                    <div class="mk-tier-price"><span class="mk-tier-num">{{ $p->priceFormatted() }}</span></div>
                    <p class="mk-tier-blurb">{{ $p->credits }} credits · roughly {{ floor($p->credits / 32) }} Stage 1 tunes</p>
                </div>
                <form method="POST" action="{{ route('app.checkout.start', $p) }}">
                    @csrf
                    <button type="submit" class="{{ $p->slug === 'trade' ? 'primary-btn primary-btn-lg' : 'ghost-btn ghost-btn-lg' }}" style="width:100%; justify-content:center">
                        Buy {{ $p->name }}
                    </button>
                </form>
            </div>
        @endforeach
    </div>

    <div class="card card-table">
        <div class="card-head card-pad-x">
            <div class="metric-label">Recent transactions</div>
            <div class="card-head-r t-mute small">last 15</div>
        </div>
        <table class="t">
            <thead><tr>
                <th>When</th>
                <th>Type</th>
                <th>Note</th>
                <th class="num">Credits</th>
                <th class="num">Balance after</th>
            </tr></thead>
            <tbody>
                @forelse ($tx as $t)
                    <tr class="t-row">
                        <td class="mono small">{{ $t->created_at?->diffForHumans() }}</td>
                        <td>{{ $t->type }}</td>
                        <td class="t-mute small">{{ $t->note }}</td>
                        <td class="num mono">{{ $t->credits >= 0 ? '+' : '' }}{{ $t->credits }}</td>
                        <td class="num mono">{{ $t->balance_after }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="empty-cell">No credit transactions yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
