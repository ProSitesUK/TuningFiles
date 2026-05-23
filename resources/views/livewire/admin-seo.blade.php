<div class="page">
    <div class="page-head">
        <div>
            <h1 class="page-title">SEO</h1>
            <p class="page-sub">Per-page meta overrides. Blank fields fall back to the defaults in <a href="{{ route('admin.settings') }}">Settings</a>.</p>
        </div>
    </div>

    @if ($flash)
        <div class="card card-pad" style="border-color: var(--success); background: var(--success-soft); margin-bottom: 16px">
            <span style="color: var(--success); font-weight: 500">✓ {{ $flash }}</span>
        </div>
    @endif

    <div style="display: grid; gap: 12px; max-width: 920px">
        @foreach ($subjects as $s)
            @php $row = $overrides[$s['type'].':'.$s['key']] ?? null; @endphp
            <div class="card card-pad">
                <div style="display:flex; justify-content:space-between; align-items:start; gap: 14px">
                    <div style="min-width: 0">
                        <div class="va-form-title">{{ $s['label'] }}</div>
                        <div class="t-mute small mono">{{ $s['path'] }}</div>
                        <div class="t-mute small" style="margin-top: 4px">{{ $s['hint'] }}</div>
                        @if ($row)
                            <div style="margin-top: 10px">
                                <div style="font-size: 13px; font-weight: 600">{{ $row->title ?: '— title not set —' }}</div>
                                <div class="t-mute small" style="margin-top: 2px">{{ $row->description ?: 'description: (default)' }}</div>
                                <div class="t-mute small mono" style="margin-top: 4px">robots: {{ $row->robots ?? 'default' }}</div>
                            </div>
                        @else
                            <div class="t-mute small" style="margin-top: 10px">No override — using site defaults.</div>
                        @endif
                    </div>
                    <button type="button" wire:click="edit('{{ $s['type'] }}', '{{ $s['key'] }}')" class="ghost-btn ghost-btn-sm">
                        {{ $row ? 'Edit' : 'Add override' }}
                    </button>
                </div>

                @if ($editing !== null && $editingType === $s['type'] && $editingKey === $s['key'])
                    <div class="va-form" style="margin: 14px 0 0">
                        <label class="va-field">
                            <span>Title <em class="t-mute small">(blank uses default)</em></span>
                            <input type="text" wire:model.defer="form.title" />
                            @error('form.title') <em class="va-err">{{ $message }}</em> @enderror
                        </label>
                        <label class="va-field">
                            <span>Description <em class="t-mute small">(50–160 chars ideal)</em></span>
                            <textarea wire:model.defer="form.description" rows="3"></textarea>
                            @error('form.description') <em class="va-err">{{ $message }}</em> @enderror
                        </label>
                        <div class="va-grid-2">
                            <label class="va-field">
                                <span>OG image URL</span>
                                <input type="url" wire:model.defer="form.og_image" placeholder="https://…" />
                                @error('form.og_image') <em class="va-err">{{ $message }}</em> @enderror
                            </label>
                            <label class="va-field">
                                <span>Canonical URL override</span>
                                <input type="url" wire:model.defer="form.canonical" placeholder="(blank = auto)" />
                                @error('form.canonical') <em class="va-err">{{ $message }}</em> @enderror
                            </label>
                        </div>
                        <label class="va-field">
                            <span>Robots <em class="t-mute small">(blank uses site default)</em></span>
                            <select wire:model.defer="form.robots">
                                <option value="">(use site default)</option>
                                <option value="index,follow">index, follow</option>
                                <option value="index,nofollow">index, nofollow</option>
                                <option value="noindex,follow">noindex, follow</option>
                                <option value="noindex,nofollow">noindex, nofollow</option>
                            </select>
                        </label>
                        <div class="va-form-actions">
                            <button type="button" wire:click="cancel" class="ghost-btn ghost-btn-sm">Cancel</button>
                            <button type="button" wire:click="save" class="primary-btn primary-btn-sm">Save</button>
                        </div>
                    </div>
                @endif
            </div>
        @endforeach
    </div>
</div>
