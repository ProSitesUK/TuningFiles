<div class="page">
    <div class="page-head">
        <div>
            <h1 class="page-title">Dyno results</h1>
            <p class="page-sub">Moderate customer-submitted dyno results before they appear on the public gallery.</p>
        </div>
    </div>

    <div class="filter-chips" style="margin-bottom:16px; display:flex; gap:6px">
        <button type="button" class="chip {{ $filter === 'pending' ? 'chip-active' : '' }}" wire:click="$set('filter', 'pending')">Pending</button>
        <button type="button" class="chip {{ $filter === 'approved' ? 'chip-active' : '' }}" wire:click="$set('filter', 'approved')">Approved</button>
        <button type="button" class="chip {{ $filter === 'all' ? 'chip-active' : '' }}" wire:click="$set('filter', 'all')">All</button>
    </div>

    <div class="card">
        <table class="tbl">
            <thead>
                <tr>
                    <th>Vehicle</th>
                    <th>Stock HP</th>
                    <th>Tuned HP</th>
                    <th>Gain</th>
                    <th>Tune type</th>
                    <th>Submitted by</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($results as $r)
                    <tr>
                        <td>{{ $r->vehicle_label }} {{ $r->vehicle_year }}</td>
                        <td class="mono">{{ $r->stock_hp }}</td>
                        <td class="mono">{{ $r->tuned_hp }}</td>
                        <td class="mono" style="color:var(--success)">+{{ $r->hpGain() }}</td>
                        <td>{{ $r->tune_type }}</td>
                        <td>{{ $r->user?->name ?? '—' }}</td>
                        <td class="t-mute small">{{ $r->created_at->format('j M Y') }}</td>
                        <td>
                            @if ($r->is_approved)
                                <span class="chip chip-sm chip-static" style="background:var(--success-soft); color:var(--success)">Approved</span>
                            @elseif ($r->is_public)
                                <span class="chip chip-sm chip-static" style="background:var(--warning-soft); color:var(--warning)">Pending</span>
                            @else
                                <span class="chip chip-sm chip-static" style="background:var(--surface-2); color:var(--muted)">Rejected</span>
                            @endif
                        </td>
                        <td style="text-align:right">
                            @if (!$r->is_approved)
                                <button type="button" class="ghost-btn ghost-btn-sm" wire:click="approve({{ $r->id }})" wire:confirm="Approve this result?">Approve</button>
                            @endif
                            @if ($r->is_public)
                                <button type="button" class="ghost-btn ghost-btn-sm" wire:click="reject({{ $r->id }})" style="color:var(--danger)">Reject</button>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="9" class="t-mute" style="text-align:center; padding:24px">No results found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($results->hasPages())
        <div style="margin-top:16px">{{ $results->links() }}</div>
    @endif
</div>
