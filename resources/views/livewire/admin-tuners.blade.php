<div class="page">
    <div class="page-head">
        <div>
            <h1 class="page-title">Tuners</h1>
            <p class="page-sub">Manage tuner profiles, capacity and specialties.</p>
        </div>
    </div>

    @if ($flash)
        <div class="card card-pad" style="border-color: var(--success); background: var(--success-soft); margin-bottom: 16px">
            <span style="color: var(--success); font-weight: 500">{{ $flash }}</span>
        </div>
    @endif

    <div class="card card-table">
        <table class="t">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Status</th>
                    <th>Workload</th>
                    <th>Active orders</th>
                    <th>Specialties</th>
                    <th>Last active</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($tuners as $tp)
                    <tr class="t-row {{ $editing === $tp->id ? 't-row-active' : '' }}" wire:click="edit({{ $tp->id }})" style="cursor: pointer">
                        <td>{{ $tp->user?->name ?? '—' }}</td>
                        <td>
                            @php
                                $dotColor = match($tp->status) {
                                    'live' => 'ok',
                                    'busy' => 'warn',
                                    'away' => 'mute',
                                    'off'  => 'err',
                                    default => 'mute',
                                };
                            @endphp
                            <span class="dot dot-{{ $dotColor }}"></span>
                            {{ ucfirst($tp->status) }}
                        </td>
                        <td>
                            @php
                                $pct = $tp->capacity > 0 ? min(100, round(($tp->active_count / $tp->capacity) * 100)) : 0;
                                $barColor = $pct >= 90 ? 'var(--danger)' : ($pct >= 70 ? 'var(--warning)' : 'var(--accent)');
                            @endphp
                            <div style="display:flex; align-items:center; gap:8px">
                                <div style="flex:1; height:6px; background:var(--border); border-radius:3px; min-width:60px">
                                    <div style="width:{{ $pct }}%; height:100%; background:{{ $barColor }}; border-radius:3px"></div>
                                </div>
                                <span class="mono small t-mute">{{ $tp->active_count }}/{{ $tp->capacity }}</span>
                            </div>
                        </td>
                        <td class="mono">{{ $tp->active_count }}</td>
                        <td>
                            @if (is_array($tp->specialties) && count($tp->specialties))
                                @foreach ($tp->specialties as $spec)
                                    <span class="badge badge-neutral">{{ $spec }}</span>
                                @endforeach
                            @else
                                <span class="t-mute">—</span>
                            @endif
                        </td>
                        <td class="t-mute small">{{ $tp->last_active_at?->diffForHumans(short: true) ?? '—' }}</td>
                        <td>
                            <button type="button" wire:click.stop="edit({{ $tp->id }})" class="ghost-btn ghost-btn-sm">Edit</button>
                        </td>
                    </tr>

                    {{-- Inline edit form --}}
                    @if ($editing === $tp->id)
                        <tr>
                            <td colspan="7" style="background: var(--bg-offset); padding: 16px 20px">
                                <div class="va-form" style="max-width: 600px">
                                    <div class="va-form-title">Edit {{ $tp->user?->name ?? 'Tuner' }}</div>
                                    <div class="va-grid-2">
                                        <label class="va-field">
                                            <span>Status</span>
                                            <select wire:model.defer="form.status">
                                                @foreach (['live','busy','away','off'] as $s)
                                                    <option value="{{ $s }}">{{ ucfirst($s) }}</option>
                                                @endforeach
                                            </select>
                                        </label>
                                        <label class="va-field">
                                            <span>Capacity</span>
                                            <input type="number" wire:model.defer="form.capacity" min="0" max="50" />
                                            @error('form.capacity') <em class="va-err">{{ $message }}</em> @enderror
                                        </label>
                                    </div>
                                    <label class="va-field">
                                        <span>Specialties <em class="t-mute small">(comma-separated)</em></span>
                                        <input type="text" wire:model.defer="form.specialties" placeholder="e.g. BMW, Stage 2, DPF delete" />
                                    </label>
                                    <div class="va-form-actions">
                                        <button type="button" wire:click="cancel" class="ghost-btn ghost-btn-sm">Cancel</button>
                                        <button type="button" wire:click="save" class="primary-btn primary-btn-sm">Save</button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endif
                @empty
                    <tr><td colspan="7" class="empty-cell">No tuner profiles found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
