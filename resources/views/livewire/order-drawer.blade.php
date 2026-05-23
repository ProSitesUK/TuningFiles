<div x-data="{}" @keydown.escape.window="$wire.close()">
    @php $open = (bool) $order; @endphp

    <div class="drawer-scrim {{ $open ? 'drawer-scrim-on' : '' }}" wire:click="close"></div>

    <aside class="drawer {{ $open ? 'drawer-on' : '' }}" aria-hidden="{{ $open ? 'false' : 'true' }}">
        @if ($order)
            <div class="drawer-head">
                <div class="crumbs-sm">
                    <span>Customers</span>
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M9 6 L15 12 L9 18"/></svg>
                    <span>{{ $order->customer?->name }}</span>
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M9 6 L15 12 L9 18"/></svg>
                    <span class="crumb-active mono">#{{ $order->reference }}</span>
                </div>
                <div class="drawer-head-actions">
                    <button type="button" class="icon-btn" wire:click="close" title="Close">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M5 5 L19 19 M19 5 L5 19"/></svg>
                    </button>
                </div>
            </div>

            <div class="drawer-body">
                <div class="drawer-title-row">
                    <div>
                        <h2 class="drawer-title">Order <span class="mono">#{{ $order->reference }}</span></h2>
                        <div class="drawer-sub">
                            @include('partials.status-badge', ['status' => $order->status])
                            <span class="t-mute mono small">{{ $order->elapsedLabel() }} elapsed</span>
                        </div>
                    </div>
                    <div class="drawer-actions">
                        <button class="ghost-btn ghost-btn-sm">Reassign</button>
                        <button class="ghost-btn ghost-btn-sm">Refund</button>
                        <button class="ghost-btn ghost-btn-sm ghost-btn-accent">Mark ready</button>
                        <button class="primary-btn primary-btn-sm">Flag dispute</button>
                    </div>
                </div>

                <div class="meta-grid">
                    <div class="meta"><div class="metric-label">Vehicle</div><div class="meta-val">{{ $order->vehicle_label }} · {{ $order->vehicle_year }}</div></div>
                    <div class="meta"><div class="metric-label">ECU</div><div class="meta-val mono">{{ $order->ecu_label }}</div></div>
                    <div class="meta"><div class="metric-label">Options</div><div class="meta-val">{{ $order->options_label }}</div></div>
                    <div class="meta"><div class="metric-label">Origin</div><div class="meta-val">{{ $order->origin }}</div></div>
                    <div class="meta"><div class="metric-label">Credits</div><div class="meta-val mono">{{ $order->credits_cost }}</div></div>
                    <div class="meta"><div class="metric-label">SLA</div><div class="meta-val mono">{{ $order->elapsedLabel() }} / {{ $order->sla }}</div></div>
                </div>

                <div class="card card-pad placeholder" style="margin-top:18px">
                    <div class="placeholder-title">Drawer content — Phase 10</div>
                    <div class="t-mute small">File card with checksums, animated timeline, tune preview (torque/boost/fuel/ignition tabs), assignee, customer notes — all coming in Phase 10. Skeleton works.</div>
                </div>
            </div>
        @endif
    </aside>
</div>
