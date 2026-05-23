<div x-data="{}" @keydown.escape.window="$wire.close()">
    @php $open = (bool) $order; @endphp

    <div class="drawer-scrim {{ $open ? 'drawer-scrim-on' : '' }}" wire:click="close"></div>

    <aside class="drawer {{ $open ? 'drawer-on' : '' }}" aria-hidden="{{ $open ? 'false' : 'true' }}">
        @if ($order)
            @php
                $tuner = $order->assignedTuner;
                $tp    = $tuner?->tunerProfile;
                $c     = $order->customer;
                $original = $order->originalFile() ?? null;
            @endphp

            <div class="drawer-head">
                <div class="crumbs-sm">
                    <span>Customers</span>
                    <x-icon name="chevron" size="12" />
                    <span>{{ $c?->name }}</span>
                    <x-icon name="chevron" size="12" />
                    <span class="crumb-active mono">#{{ $order->reference }}</span>
                </div>
                <div class="drawer-head-actions">
                    <button type="button" class="icon-btn" wire:click="close" title="Close">
                        <x-icon name="close" size="15" />
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
                        <button class="ghost-btn ghost-btn-sm" type="button">Reassign</button>
                        <button class="ghost-btn ghost-btn-sm" type="button"><x-icon name="refund" size="13" /> Refund</button>
                        <button class="ghost-btn ghost-btn-sm ghost-btn-accent" type="button"><x-icon name="check" size="13" /> Mark ready</button>
                        <button class="primary-btn primary-btn-sm" type="button"><x-icon name="flag" size="13" /> Flag dispute</button>
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

                <div class="drawer-grid">
                    {{-- LEFT: file + timeline --}}
                    <div>
                        <div class="card card-pad">
                            <div class="card-head">
                                <div class="metric-label">File</div>
                                <button class="ghost-btn ghost-btn-sm" type="button"><x-icon name="download" size="13" /> Original</button>
                            </div>
                            <div class="file-row">
                                <div class="file-mark"><x-icon name="files" size="18" /></div>
                                <div>
                                    <div class="mono">{{ strtolower(str_replace(' ', '_', $order->vehicle_label ?? 'file')) }}_{{ $order->reference }}.bin</div>
                                    <div class="t-mute small mono">{{ $order->file_size ?? '—' }} · md5 {{ $order->md5_status }} · received {{ $order->elapsedLabel() }} ago</div>
                                </div>
                            </div>
                            <div class="checksum-row">
                                <span class="chip chip-sm chip-static">ECU id matches</span>
                                <span class="chip chip-sm chip-static">checksum ok</span>
                                <span class="chip chip-sm chip-static">no DTC mods</span>
                            </div>
                        </div>

                        <div class="card card-pad" style="margin-top:14px">
                            <div class="card-head">
                                <div class="metric-label">Timeline</div>
                                <button class="ghost-btn ghost-btn-sm" type="button">All events</button>
                            </div>
                            <div class="timeline">
                                @php $events = $order->events; $count = $events->count(); @endphp
                                @foreach ($events as $i => $e)
                                    @php $state = $e->state; @endphp
                                    <div class="tl-row tl-row-{{ $state }}">
                                        <div class="tl-dot-col">
                                            <span class="tl-dot tl-dot-{{ $state }}"></span>
                                            @if ($i < $count - 1)
                                                <span class="tl-line"></span>
                                            @endif
                                        </div>
                                        <div class="tl-text">
                                            <div class="tl-stage">{{ $e->stage }} <span class="t-mute small">· {{ $e->happened_at?->diffForHumans() }}</span></div>
                                            <div class="t-mute small">{{ $e->note }}</div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- RIGHT: tune + assignee + notes --}}
                    <div>
                        <div class="card card-pad">
                            <div class="card-head">
                                <div class="metric-label">Tune preview</div>
                                <div class="seg seg-sm">
                                    <button class="seg-btn seg-btn-active" type="button">Torque</button>
                                    <button class="seg-btn" type="button">Boost</button>
                                    <button class="seg-btn" type="button">Fuel</button>
                                    <button class="seg-btn" type="button">Ignition</button>
                                </div>
                            </div>
                            <x-tune-chart :width="520" :height="180" />
                            <div class="tune-foot">
                                <div class="tune-stat"><span class="t-mute small">peak HP</span><b>+52</b></div>
                                <div class="tune-stat"><span class="t-mute small">peak Nm</span><b>+78</b></div>
                                <div class="tune-stat"><span class="t-mute small">rev limit</span><b>7,200</b></div>
                                <div class="tune-stat"><span class="t-mute small">map slots</span><b>3</b></div>
                            </div>
                        </div>

                        <div class="card card-pad" style="margin-top:14px">
                            <div class="card-head"><div class="metric-label">Assignee</div></div>
                            @if ($tuner)
                                <div class="assignee">
                                    <span class="avatar avatar-accent" style="width:40px;height:40px;font-size:13px">{{ $tuner->initials() }}</span>
                                    <div class="assignee-text">
                                        <div class="assignee-name">{{ $tuner->name }}</div>
                                        <div class="t-mute small">
                                            <span class="dot dot-{{ $tp?->status === 'live' ? 'ok' : 'mute' }}"></span>
                                            {{ $tp?->status ?? 'off' }} · {{ $tp?->workload ?? 0 }}/{{ $tp?->capacity ?? 0 }} workload
                                        </div>
                                    </div>
                                    <button class="ghost-btn ghost-btn-sm" type="button">Change</button>
                                </div>
                            @else
                                <button class="ghost-btn" type="button">+ Auto-assign</button>
                            @endif
                        </div>

                        <div class="card card-pad" style="margin-top:14px">
                            <div class="card-head"><div class="metric-label">Customer notes</div></div>
                            <div class="quote">
                                "Stock car, fresh service. Pump 99 only. Please keep DPF in place — vehicle is MOT'd next week."
                            </div>
                            <div class="t-mute small">— {{ $c?->name }}, on upload</div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </aside>
</div>
