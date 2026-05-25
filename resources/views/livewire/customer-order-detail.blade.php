<div>
    <div class="crumbs-sm" style="margin-bottom:8px">
        <a href="{{ route('app.orders.index') }}" style="color:var(--muted); text-decoration:none">Orders</a>
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
        <div class="page-actions">
            @if ($order->originalFile())
                <button class="ghost-btn" type="button" wire:click="downloadOriginal"><x-icon name="download" size="13" /> Original</button>
            @endif
            @if ($order->tunedFile())
                <button class="primary-btn" type="button" wire:click="downloadTuned"><x-icon name="download" size="13" /> Tuned file</button>
            @endif
        </div>
    </div>

    <div class="meta-grid">
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
                    @if ($f->notes)
                        <div class="t-mute small" style="margin-top:4px; font-style:italic">{{ $f->notes }}</div>
                    @endif
                @empty
                    <div class="t-mute small">No files yet.</div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Guarantee section --}}
    @if (in_array($order->status, ['ready', 'delivered']))
        <div class="card card-pad" style="margin-top:18px">
            @if ($order->guarantee_claimed_at)
                <div style="display:flex; align-items:center; gap:8px">
                    <span class="chip chip-sm chip-static" style="background:var(--warning-soft); color:var(--warning)">Guarantee claimed</span>
                    <span class="t-mute small">on {{ $order->guarantee_claimed_at->format('j M Y') }}</span>
                </div>
            @elseif ($order->underGuarantee())
                @php $daysRemaining = (int) now()->diffInDays($order->guarantee_expires_at, false); @endphp
                <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:8px">
                    <div style="display:flex; align-items:center; gap:8px">
                        <span class="chip chip-sm chip-static" style="background:var(--success-soft); color:var(--success)">{{ \App\Models\SiteSetting::get('guarantee_days', '30') }}-day guarantee</span>
                        <span class="t-mute small">{{ $daysRemaining }} {{ $daysRemaining === 1 ? 'day' : 'days' }} remaining</span>
                    </div>
                    <button type="button" class="ghost-btn ghost-btn-sm" wire:click="$toggle('showGuaranteeForm')">Claim guarantee</button>
                </div>
                @if ($showGuaranteeForm)
                    <div style="margin-top:12px; padding-top:12px; border-top:1px dashed var(--border)">
                        <label class="va-field">
                            <span>Reason for claiming guarantee</span>
                            <textarea wire:model="guaranteeReason" rows="3" placeholder="Describe the issue with the tuned file..." style="width:100%; padding:8px 10px; border:1px solid var(--border); border-radius:var(--r-sm); background:var(--surface); font-size:13px"></textarea>
                        </label>
                        <div style="display:flex; gap:8px; margin-top:8px">
                            <button type="button" class="primary-btn primary-btn-sm" wire:click="claimGuarantee(guaranteeReason)" wire:confirm="This will refund {{ $order->credits_cost }} credits to your balance. Continue?">Submit claim</button>
                            <button type="button" class="ghost-btn ghost-btn-sm" wire:click="$set('showGuaranteeForm', false)">Cancel</button>
                        </div>
                    </div>
                @endif
            @elseif ($order->guarantee_expires_at && $order->guarantee_expires_at->isPast())
                <div style="display:flex; align-items:center; gap:8px">
                    <span class="chip chip-sm chip-static" style="background:var(--surface-2); color:var(--muted)">Guarantee expired</span>
                </div>
            @endif
        </div>
    @endif

    {{-- Revision window --}}
    @if ($order->status === 'delivered')
        @if ($order->underRevisionWindow())
            <div class="card card-pad" style="margin-top:16px; border-color:var(--accent)">
                <div class="metric-label">Revision window</div>
                <p class="t-mute small">{{ $order->revision_window_ends_at->diffForHumans() }} remaining · revision {{ $order->revision_count }} of {{ $order->max_revisions }}</p>
                <form wire:submit="requestRevision" style="margin-top:10px">
                    <textarea wire:model="revisionNotes" rows="3" placeholder="Describe what you'd like changed..." style="width:100%; padding:10px; border:1px solid var(--border); border-radius:var(--r-sm); background:var(--bg); color:var(--ink); font-family:inherit; font-size:13px"></textarea>
                    @error('revisionNotes') <em class="va-err">{{ $message }}</em> @enderror
                    @error('revision') <em class="va-err">{{ $message }}</em> @enderror
                    <button type="submit" class="primary-btn primary-btn-sm" style="margin-top:8px">Request revision</button>
                </form>
            </div>
        @elseif ($order->revision_window_ends_at)
            <div class="t-mute small" style="margin-top:12px">Revision window closed {{ $order->revision_window_ends_at->diffForHumans() }}.</div>
        @endif
    @endif

    {{-- Dyno results submission --}}
    @if ($order->status === 'delivered')
        <div class="card card-pad" style="margin-top:16px">
            <div class="metric-label">Share your dyno results</div>
            <p class="t-mute small">Upload your results and earn 5 bonus credits.</p>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px; margin-top:10px">
                <label class="va-field">
                    <span class="t-mute small">Stock HP</span>
                    <input type="number" wire:model="dynoStockHp" placeholder="e.g. 150" style="width:100%; padding:8px 10px; border:1px solid var(--border); border-radius:var(--r-sm); background:var(--bg); color:var(--ink); font-size:13px" />
                    @error('dynoStockHp') <em class="va-err">{{ $message }}</em> @enderror
                </label>
                <label class="va-field">
                    <span class="t-mute small">Tuned HP</span>
                    <input type="number" wire:model="dynoTunedHp" placeholder="e.g. 190" style="width:100%; padding:8px 10px; border:1px solid var(--border); border-radius:var(--r-sm); background:var(--bg); color:var(--ink); font-size:13px" />
                    @error('dynoTunedHp') <em class="va-err">{{ $message }}</em> @enderror
                </label>
            </div>
            <label class="va-field" style="margin-top:8px">
                <span class="t-mute small">Tune type</span>
                <select wire:model="dynoTuneType" style="width:100%; padding:8px 10px; border:1px solid var(--border); border-radius:var(--r-sm); background:var(--bg); color:var(--ink); font-size:13px">
                    <option value="">Select tune type...</option>
                    <option value="Stage 1">Stage 1</option>
                    <option value="Stage 2">Stage 2</option>
                    <option value="Stage 3">Stage 3</option>
                    <option value="Custom">Custom</option>
                    <option value="Eco">Eco</option>
                    <option value="DPF/EGR">DPF/EGR</option>
                </select>
                @error('dynoTuneType') <em class="va-err">{{ $message }}</em> @enderror
            </label>
            <label class="va-field" style="margin-top:8px">
                <span class="t-mute small">Notes (optional)</span>
                <textarea wire:model="dynoNotes" rows="2" placeholder="Any notes about your dyno run..." style="width:100%; padding:8px 10px; border:1px solid var(--border); border-radius:var(--r-sm); background:var(--bg); color:var(--ink); font-family:inherit; font-size:13px"></textarea>
                @error('dynoNotes') <em class="va-err">{{ $message }}</em> @enderror
            </label>
            <button type="button" wire:click="submitDynoResult" class="primary-btn primary-btn-sm" style="margin-top:8px">Submit results</button>
        </div>
    @endif

    <div style="margin-top:18px">
        <a href="{{ route('app.tickets.new', ['order' => $order->id]) }}" class="ghost-btn ghost-btn-sm" style="text-decoration:none">Need help with this order?</a>
    </div>
</div>
