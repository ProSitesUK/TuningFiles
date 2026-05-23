<div class="page" wire:poll.10s>
    <div class="page-head">
        <div>
            <h1 class="page-title">Live queue</h1>
            <p class="page-sub">Updated just now · auto-refresh on</p>
        </div>
        <div class="page-actions">
            <span class="badge badge-green badge-soft"><span class="badge-dot pulse"></span> Live</span>
            <button class="ghost-btn" type="button">Auto-assign · <b>on</b></button>
            <button class="primary-btn" type="button">Pause intake</button>
        </div>
    </div>

    {{-- ─────── Strip stats ─────── --}}
    <div class="strip">
        <div class="strip-stat">
            <div class="metric-label">Intake / hr</div>
            <div class="strip-val strip-val-accent">{{ $intakeHr }}</div>
        </div>
        <div class="strip-stat">
            <div class="metric-label">In progress</div>
            <div class="strip-val">{{ $inProgress }}</div>
        </div>
        <div class="strip-stat {{ $breaches > 0 ? 'strip-stat-warn' : '' }}">
            <div class="metric-label">Breaches</div>
            <div class="strip-val">{{ $breaches }}@if ($breaches > 0)<span class="strip-sub">overdue</span>@endif</div>
        </div>
        <div class="strip-stat">
            <div class="metric-label">SLA today</div>
            <div class="strip-val">98.2%<span class="strip-sub">target 95%</span></div>
        </div>
        <div class="strip-stat">
            <div class="metric-label">Refunds</div>
            <div class="strip-val">£{{ $refundsToday }}<span class="strip-sub">{{ $refundsTodayN }} today</span></div>
        </div>
        <div class="strip-stat">
            <div class="metric-label">Avg first-touch</div>
            <div class="strip-val">3.2m<span class="strip-sub">−40s</span></div>
        </div>
    </div>

    {{-- ─────── Kanban ─────── --}}
    <div class="kanban">
        @foreach ($cols as $colId => $col)
            <div class="kcol">
                <div class="kcol-head">
                    <div class="kcol-title"><span class="dot dot-{{ $col['dot'] }}"></span><span>{{ $col['label'] }}</span></div>
                    <span class="kcol-count">{{ count($col['orders']) }}</span>
                </div>
                <div class="kcol-body">
                    @foreach ($col['orders'] as $o)
                        @php $t = $o->assignedTuner; $tp = $t?->tunerProfile; @endphp
                        <div class="ocard" role="button" tabindex="0" wire:click="$dispatch('order:open', { id: {{ $o->id }} })">
                            <div class="ocard-head">
                                <span class="mono ocard-id">#{{ $o->reference }}</span>
                                <span class="mono ocard-age">{{ $o->elapsedLabel() }}</span>
                            </div>
                            <div class="ocard-veh">{{ $o->vehicle_label }} <span class="t-mute mono">· {{ $o->vehicle_year }}</span></div>
                            <div class="ocard-opt">{{ $o->options_label }}</div>
                            @if ($o->progress > 0 && $o->progress < 1)
                                <div class="pbar" style="height:3px"><div class="pbar-fill pbar-accent" style="width:{{ $o->progress * 100 }}%"></div></div>
                            @endif
                            <div class="ocard-foot">
                                @if ($t)
                                    <span class="avatar" style="width:22px;height:22px;font-size:9px;background:{{ $tp?->status === 'live' ? 'var(--accent)' : 'var(--surface-2)' }};color:{{ $tp?->status === 'live' ? '#fff' : 'var(--ink)' }};border-color:transparent">{{ $t->initials() }}</span>
                                @else
                                    <button class="chip-btn" type="button" wire:click.stop>+ assign</button>
                                @endif
                                <span class="ocard-tag">{{ \App\Support\OrderTag::label($o->options_label) }}</span>
                            </div>
                            @if ($o->breach)
                                <span class="ocard-flag">SLA risk</span>
                            @endif
                        </div>
                    @endforeach
                    @if ($colId === 'queued')
                        <button class="kcol-add" type="button"><x-icon name="plus" size="14" /> Manual order</button>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    {{-- ─────── Tuner workload ─────── --}}
    <div class="card card-pad workload">
        <div class="card-head">
            <div>
                <div class="metric-label">Tuner workload · live</div>
                <div class="metric-mid">{{ $tuners->where('status', 'live')->count() }} active · {{ $tuners->where('status', '!=', 'live')->count() }} off</div>
            </div>
            <div class="card-head-r">
                <button class="ghost-btn" type="button">Round-robin · <b>on</b></button>
                <button class="ghost-btn" type="button">Rebalance</button>
                <button class="ghost-btn ghost-btn-accent" type="button"><x-icon name="plus" size="12" /> Invite tuner</button>
            </div>
        </div>
        <div class="tuner-grid">
            @foreach ($tuners as $tp)
                @php $u = $tp->user; @endphp
                <div class="tuner-row">
                    <div class="tuner-id">
                        <span class="avatar {{ $tp->status === 'live' ? 'avatar-accent' : '' }}" style="width:28px;height:28px;font-size:10px">{{ $u->initials() }}</span>
                        <div>
                            <div class="tuner-name">{{ $u->name }}</div>
                            <div class="tuner-status">
                                <span class="dot dot-{{ $tp->status === 'live' ? 'ok' : ($tp->status === 'busy' ? 'warn' : 'mute') }}"></span>
                                {{ $tp->status }}
                            </div>
                        </div>
                    </div>
                    <div class="tuner-load">
                        <div class="tuner-load-bar">
                            <div class="pbar" style="height:6px"><div class="pbar-fill {{ $tp->workload >= $tp->capacity ? 'pbar-warn' : 'pbar-ink' }}" style="width:{{ $tp->capacity ? ($tp->workload / $tp->capacity * 100) : 0 }}%"></div></div>
                        </div>
                        <span class="mono tuner-load-num">{{ $tp->workload }}/{{ $tp->capacity }}</span>
                    </div>
                    <div class="tuner-num"><b>{{ $tp->active_count }}</b><span class="t-mute">active</span></div>
                    <div class="tuner-num"><b>{{ $tp->idle ?: '—' }}</b><span class="t-mute">idle</span></div>
                </div>
            @endforeach
        </div>
    </div>
</div>
