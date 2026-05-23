<div x-data="{ reassignOpen: false }" @keydown.escape.window="$wire.close()">
    @php $open = (bool) $order; @endphp

    <div class="drawer-scrim {{ $open ? 'drawer-scrim-on' : '' }}" wire:click="close"></div>

    <aside class="drawer {{ $open ? 'drawer-on' : '' }}" aria-hidden="{{ $open ? 'false' : 'true' }}">
        @if ($order)
            @php
                $tuner = $order->assignedTuner;
                $tp    = $tuner?->tunerProfile;
                $c     = $order->customer;
                $isAdmin = auth()->user()->isAdmin();
                $isTuner = auth()->user()->isTuner();
                $canUpload = ($isAdmin || ($isTuner && $tuner?->id === auth()->id())) && in_array($order->status, ['in_progress','review','queued'], true);
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
                    @if ($isAdmin)
                        <div class="drawer-actions">
                            <button class="ghost-btn ghost-btn-sm" type="button" @click="reassignOpen = !reassignOpen">Reassign</button>
                            <button class="ghost-btn ghost-btn-sm" type="button" wire:click="refund" wire:confirm="Refund this order and restore credits to the customer?"><x-icon name="refund" size="13" /> Refund</button>
                            <button class="ghost-btn ghost-btn-sm ghost-btn-accent" type="button" wire:click="markReady" wire:confirm="Mark this order ready for delivery?"><x-icon name="check" size="13" /> Mark ready</button>
                            <button class="primary-btn primary-btn-sm" type="button"><x-icon name="flag" size="13" /> Flag dispute</button>
                        </div>
                    @endif
                </div>

                @if ($isAdmin)
                    <div x-show="reassignOpen" x-transition class="card card-pad" style="margin-bottom:14px">
                        <div class="card-head"><div class="metric-label">Reassign to</div></div>
                        <div style="display:flex; gap:8px; align-items:center">
                            <select wire:model="reassignTo" style="flex:1; padding:7px 10px; border:1px solid var(--border); border-radius:var(--r-sm); background:var(--surface)">
                                <option value="">Choose a tuner…</option>
                                @foreach ($tuners as $u)
                                    <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->tunerProfile?->status ?? 'off' }})</option>
                                @endforeach
                            </select>
                            <button type="button" class="primary-btn primary-btn-sm" wire:click="reassign">Reassign</button>
                        </div>
                    </div>
                @endif

                <div class="meta-grid">
                    <div class="meta"><div class="metric-label">Vehicle</div><div class="meta-val">{{ $order->vehicle_label }} · {{ $order->vehicle_year }}</div></div>
                    <div class="meta"><div class="metric-label">ECU</div><div class="meta-val mono">{{ $order->ecu_label }}</div></div>
                    <div class="meta"><div class="metric-label">Options</div><div class="meta-val">{{ $order->options_label }}</div></div>
                    <div class="meta"><div class="metric-label">Origin</div><div class="meta-val">{{ $order->origin }}</div></div>
                    <div class="meta"><div class="metric-label">Credits</div><div class="meta-val mono">{{ $order->credits_cost }}</div></div>
                    <div class="meta"><div class="metric-label">SLA</div><div class="meta-val mono">{{ $order->elapsedLabel() }} / {{ $order->sla }}</div></div>
                </div>

                <div class="drawer-grid">
                    {{-- LEFT: files + timeline --}}
                    <div>
                        <div class="card card-pad">
                            <div class="card-head">
                                <div class="metric-label">Files</div>
                                @if ($order->originalFile())
                                    <button class="ghost-btn ghost-btn-sm" type="button"><x-icon name="download" size="13" /> Original</button>
                                @endif
                            </div>
                            @forelse ($order->files as $f)
                                <div class="file-row">
                                    <div class="file-mark"><x-icon name="files" size="18" /></div>
                                    <div>
                                        <div class="mono">{{ $f->original_name ?? $f->kind.'_'.$order->reference.'.bin' }}</div>
                                        <div class="t-mute small mono">{{ $f->humanSize() }} · md5 {{ substr($f->md5 ?? '', 0, 8) }}… · {{ $f->kind }}</div>
                                    </div>
                                </div>
                            @empty
                                <div class="t-mute small">No files yet.</div>
                            @endforelse
                            <div class="checksum-row">
                                <span class="chip chip-sm chip-static">ECU id matches</span>
                                <span class="chip chip-sm chip-static">checksum ok</span>
                                <span class="chip chip-sm chip-static">no DTC mods</span>
                            </div>

                            @if ($canUpload && ! $order->tunedFile())
                                <form wire:submit="uploadTuned" style="margin-top:14px; padding-top:14px; border-top:1px dashed var(--border)">
                                    <div class="metric-label" style="margin-bottom:6px">Upload tuned file</div>
                                    <input type="file" wire:model="tunedUpload" style="width:100%; padding:6px; border:1px solid var(--border); border-radius:var(--r-sm); background:var(--surface)" />
                                    @error('tunedUpload') <div class="auth-hint" style="color:var(--danger); margin-top:4px">{{ $message }}</div> @enderror
                                    <button type="submit" class="primary-btn primary-btn-sm" style="margin-top:8px" wire:loading.attr="disabled">
                                        <span wire:loading.remove wire:target="uploadTuned"><x-icon name="check" size="13" /> Submit for review</span>
                                        <span wire:loading wire:target="uploadTuned">Uploading…</span>
                                    </button>
                                </form>
                            @endif
                        </div>

                        <div class="card card-pad" style="margin-top:14px">
                            <div class="card-head">
                                <div class="metric-label">Timeline</div>
                                <button class="ghost-btn ghost-btn-sm" type="button">All events</button>
                            </div>
                            <div class="timeline">
                                @php $events = $order->events; $count = $events->count(); @endphp
                                @foreach ($events as $i => $e)
                                    <div class="tl-row tl-row-{{ $e->state }}">
                                        <div class="tl-dot-col">
                                            <span class="tl-dot tl-dot-{{ $e->state }}"></span>
                                            @if ($i < $count - 1) <span class="tl-line"></span> @endif
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
                                </div>
                            @else
                                <div class="t-mute small">Unassigned.</div>
                            @endif
                        </div>

                        <div class="card card-pad" style="margin-top:14px">
                            <div class="card-head"><div class="metric-label">Customer notes</div></div>
                            @if ($order->customer_note)
                                <div class="quote">{{ $order->customer_note }}</div>
                                <div class="t-mute small">— {{ $c?->name }}, on upload</div>
                            @else
                                <div class="t-mute small">No notes from the customer.</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </aside>
</div>
