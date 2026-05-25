<div>
    <div class="crumbs-sm" style="margin-bottom:8px">
        <a href="{{ route('reseller.orders') }}" style="color:var(--muted); text-decoration:none">Orders</a>
        <x-icon name="chevron" size="12" />
        <span class="crumb-active mono">#{{ $order->reference }}</span>
    </div>

    <div class="page-head" style="margin-bottom:18px">
        <div>
            <h1 class="page-title">Order #{{ $order->reference }}</h1>
            <div class="cust-meta">
                @include('partials.status-badge', ['status' => $order->status])
                <span class="mono small t-mute">{{ $order->elapsedLabel() }} elapsed</span>
            </div>
        </div>
        <div class="page-actions" style="display:flex; gap:6px; align-items:center; flex-wrap:wrap">
            <select wire:change="changeStatus($event.target.value)" style="padding:5px 8px; border:1px solid var(--border); border-radius:var(--r-sm); background:var(--surface); font-size:12px; color:var(--ink)">
                @foreach (\App\Models\Order::STATUSES as $s)
                    <option value="{{ $s }}" {{ $order->status === $s ? 'selected' : '' }}>{{ ucfirst(str_replace('_',' ',$s)) }}</option>
                @endforeach
            </select>
            <button class="ghost-btn ghost-btn-sm" type="button" wire:click="markReady" wire:confirm="Mark this order ready for delivery?">Mark ready</button>
            @if ($order->originalFile())
                <button class="ghost-btn ghost-btn-sm" type="button" wire:click="downloadOriginal"><x-icon name="download" size="13" /> Original</button>
            @endif
            @if ($order->tunedFile())
                <button class="primary-btn primary-btn-sm" type="button" wire:click="downloadTuned"><x-icon name="download" size="13" /> Tuned</button>
            @endif
        </div>
    </div>

    <div class="meta-grid">
        <div class="meta"><div class="metric-label">Customer</div><div class="meta-val">{{ $order->customer?->name ?? '—' }}</div></div>
        <div class="meta"><div class="metric-label">Vehicle</div><div class="meta-val">{{ $order->vehicle_label }} · {{ $order->vehicle_year }}</div></div>
        <div class="meta"><div class="metric-label">ECU</div><div class="meta-val mono">{{ $order->ecu_label }}</div></div>
        <div class="meta"><div class="metric-label">Options</div><div class="meta-val">{{ $order->options_label }}</div></div>
        <div class="meta"><div class="metric-label">Tuner</div><div class="meta-val">{{ $order->assignedTuner?->name ?? 'auto-assigning…' }}</div></div>
        <div class="meta"><div class="metric-label">Credits</div><div class="meta-val mono">{{ $order->credits_cost }}</div></div>
        <div class="meta"><div class="metric-label">SLA</div><div class="meta-val mono">{{ $order->elapsedLabel() }} / {{ $order->sla }}</div></div>
    </div>

    <div class="drawer-grid" style="margin-top:18px">
        <div>
            <div class="card card-pad">
                <div class="metric-label" style="margin-bottom:12px">Timeline</div>
                <div class="timeline">
                    @foreach ($order->events as $i => $e)
                        <div class="tl-row tl-row-{{ $e->state }}">
                            <div class="tl-dot-col">
                                <span class="tl-dot tl-dot-{{ $e->state }}"></span>
                                @if ($i < $order->events->count() - 1) <span class="tl-line"></span> @endif
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

        <div>
            <div class="card card-pad">
                <div class="metric-label" style="margin-bottom:12px">Files</div>
                @forelse ($order->files as $f)
                    <div class="file-row">
                        <div class="file-mark"><x-icon name="files" size="18" /></div>
                        <div>
                            <div class="mono">{{ $f->original_name ?? $f->kind.'_'.$order->reference.'.bin' }}</div>
                            <div class="t-mute small mono">{{ $f->humanSize() }} · md5 {{ substr($f->md5 ?? '', 0, 8) }}… · {{ $f->kind }}</div>
                        </div>
                    </div>
                    <div style="margin-top:6px">
                        <textarea wire:change="saveFileNote({{ $f->id }}, $event.target.value)"
                                  rows="2"
                                  placeholder="Add a note about this file…"
                                  style="width:100%; padding:6px 8px; border:1px solid var(--border); border-radius:var(--r-sm); background:var(--surface-2); color:var(--ink); font-size:12px; font-family:inherit; resize:vertical"
                        >{{ $f->notes }}</textarea>
                    </div>
                @empty
                    <div class="t-mute small">No files yet.</div>
                @endforelse

                @if ($canUpload)
                    <form wire:submit="uploadTuned" style="margin-top:14px; padding-top:14px; border-top:1px dashed var(--border)">
                        <div class="metric-label" style="margin-bottom:6px">Upload tuned file</div>
                        <input type="file" wire:model="tunedUpload" style="width:100%; padding:6px; border:1px solid var(--border); border-radius:var(--r-sm); background:var(--surface)" />
                        @error('tunedUpload') <div style="color:var(--danger); margin-top:4px; font-size:12px">{{ $message }}</div> @enderror
                        <button type="submit" class="primary-btn primary-btn-sm" style="margin-top:8px" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="uploadTuned">Upload tuned file</span>
                            <span wire:loading wire:target="uploadTuned">Uploading…</span>
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    @if ($order->customer_note)
        <div class="card card-pad" style="margin-top:18px">
            <div class="metric-label" style="margin-bottom:8px">Customer note</div>
            <p class="t-mute small">{{ $order->customer_note }}</p>
        </div>
    @endif
</div>
