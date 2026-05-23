<div class="page">
    <div class="page-head">
        <div>
            <h1 class="page-title">Disputes</h1>
            <p class="page-sub">Review and resolve customer disputes.</p>
        </div>
    </div>

    {{-- Filter chips --}}
    <div class="chips" style="margin-bottom: 14px">
        @foreach ([['open','Open'],['investigating','Investigating'],['resolved','Resolved'],['rejected','Rejected'],['all','All']] as [$id,$label])
            <button type="button" wire:click="$set('filter', '{{ $id }}')"
                    class="chip chip-sm {{ $filter === $id ? 'chip-active' : '' }}">{{ $label }}</button>
        @endforeach
    </div>

    {{-- Table --}}
    <div class="card card-table">
        <table class="t">
            <thead>
                <tr>
                    <th>Order</th>
                    <th>Customer</th>
                    <th>Reason</th>
                    <th>Status</th>
                    <th>Opened</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($disputes as $d)
                    <tr class="t-row {{ $selected === $d->id ? 't-row-active' : '' }}"
                        wire:click="selectDispute({{ $d->id }})" style="cursor: pointer">
                        <td class="mono">#{{ $d->order?->reference ?? '—' }}</td>
                        <td>{{ $d->order?->customer?->name ?? '—' }}</td>
                        <td>{{ Str::limit($d->reason, 60) }}</td>
                        <td>
                            @php
                                $statusBadge = match($d->status) {
                                    'open'          => 'badge-warning',
                                    'investigating' => 'badge-neutral',
                                    'resolved'      => 'badge-success',
                                    'rejected'      => 'badge-danger',
                                    default         => 'badge-neutral',
                                };
                            @endphp
                            <span class="badge {{ $statusBadge }}">{{ $d->status }}</span>
                        </td>
                        <td class="t-mute small">{{ $d->created_at->diffForHumans(short: true) }}</td>
                    </tr>

                    {{-- Inline detail panel --}}
                    @if ($selected === $d->id && $selDispute)
                        <tr>
                            <td colspan="5" style="background: var(--bg-offset); padding: 20px">
                                <div style="max-width: 700px">
                                    <div class="va-form-title" style="margin-bottom: 12px">Dispute #{{ $selDispute->id }} &mdash; Order #{{ $selDispute->order?->reference ?? '—' }}</div>

                                    <div class="metas" style="margin-bottom: 16px">
                                        <div class="meta">
                                            <div class="metric-label">Customer</div>
                                            <div class="meta-val">{{ $selDispute->order?->customer?->name ?? '—' }}</div>
                                        </div>
                                        <div class="meta">
                                            <div class="metric-label">Status</div>
                                            <div class="meta-val">
                                                @php
                                                    $sb = match($selDispute->status) {
                                                        'open'          => 'badge-warning',
                                                        'investigating' => 'badge-neutral',
                                                        'resolved'      => 'badge-success',
                                                        'rejected'      => 'badge-danger',
                                                        default         => 'badge-neutral',
                                                    };
                                                @endphp
                                                <span class="badge {{ $sb }}">{{ $selDispute->status }}</span>
                                            </div>
                                        </div>
                                        <div class="meta">
                                            <div class="metric-label">Opened</div>
                                            <div class="meta-val">{{ $selDispute->created_at->format('d M Y H:i') }}</div>
                                        </div>
                                        @if ($selDispute->resolved_at)
                                            <div class="meta">
                                                <div class="metric-label">Resolved</div>
                                                <div class="meta-val">{{ $selDispute->resolved_at->format('d M Y H:i') }}</div>
                                            </div>
                                        @endif
                                    </div>

                                    <div style="margin-bottom: 16px">
                                        <div class="metric-label">Full reason</div>
                                        <div style="margin-top: 4px; line-height: 1.5">{!! nl2br(e($selDispute->reason)) !!}</div>
                                    </div>

                                    @if (in_array($selDispute->status, ['open', 'investigating']))
                                        <div class="va-form">
                                            <label class="va-field">
                                                <span>Resolution notes</span>
                                                <textarea wire:model.defer="resolution" rows="3" placeholder="Describe the resolution or reason for rejection..."></textarea>
                                            </label>

                                            <div class="va-form-actions">
                                                @if ($selDispute->status === 'open')
                                                    <button type="button" wire:click="markInvestigating" class="ghost-btn ghost-btn-sm">Mark investigating</button>
                                                @endif
                                                <button type="button" wire:click="reject" class="ghost-btn ghost-btn-sm" style="color: var(--danger)">Reject</button>
                                                <button type="button" wire:click="resolve" class="primary-btn primary-btn-sm">Resolve</button>
                                            </div>
                                        </div>
                                    @else
                                        @if ($selDispute->resolution)
                                            <div>
                                                <div class="metric-label">Resolution</div>
                                                <div style="margin-top: 4px; line-height: 1.5">{!! nl2br(e($selDispute->resolution)) !!}</div>
                                            </div>
                                        @endif
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endif
                @empty
                    <tr><td colspan="5" class="empty-cell">No disputes found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
