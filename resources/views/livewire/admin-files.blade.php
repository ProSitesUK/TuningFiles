<div class="page">
    <div class="page-head">
        <div>
            <h1 class="page-title">Files</h1>
            <p class="page-sub">All uploaded order files across the platform.</p>
        </div>
    </div>

    {{-- KPI strip --}}
    <div class="kpi-row">
        <div class="card card-pad kpi">
            <div class="metric-label">Total files</div>
            <div class="kpi-value">{{ number_format($totalFiles) }}</div>
        </div>
        <div class="card card-pad kpi">
            <div class="metric-label">Total size</div>
            <div class="kpi-value">{{ $totalSize }}</div>
        </div>
        <div class="card card-pad kpi">
            <div class="metric-label">Originals</div>
            <div class="kpi-value">{{ number_format($originals) }}</div>
        </div>
        <div class="card card-pad kpi">
            <div class="metric-label">Tuned</div>
            <div class="kpi-value">{{ number_format($tuned) }}</div>
        </div>
    </div>

    {{-- Filter bar --}}
    <div class="filterbar">
        <div class="chips">
            @foreach ([['all','All'],['original','Original'],['tuned','Tuned'],['revision','Revision']] as [$id,$label])
                <button type="button" wire:click="$set('filter', '{{ $id }}')"
                        class="chip chip-sm {{ $filter === $id ? 'chip-active' : '' }}">{{ $label }}</button>
            @endforeach
        </div>
        <div class="filterbar-search">
            <input type="text" wire:model.live.debounce.250ms="search" placeholder="Search order ref or filename..." />
        </div>
    </div>

    {{-- Table --}}
    <div class="card card-table">
        <table class="t">
            <thead>
                <tr>
                    <th>Order</th>
                    <th>Filename</th>
                    <th>Kind</th>
                    <th>Size</th>
                    <th>Uploaded by</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($files as $f)
                    <tr class="t-row">
                        <td class="mono">#{{ $f->order?->reference ?? '—' }}</td>
                        <td>{{ $f->original_name }}</td>
                        <td>
                            @php
                                $kindBadge = match($f->kind) {
                                    'original' => 'badge-neutral',
                                    'tuned'    => 'badge-success',
                                    'revision' => 'badge-warning',
                                    'log'      => 'badge-danger',
                                    default    => 'badge-neutral',
                                };
                            @endphp
                            <span class="badge {{ $kindBadge }}">{{ $f->kind }}</span>
                        </td>
                        <td class="mono small">{{ $f->humanSize() }}</td>
                        <td>{{ $f->uploadedBy?->name ?? '—' }}</td>
                        <td class="t-mute small">{{ $f->created_at->diffForHumans(short: true) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="empty-cell">No files found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
