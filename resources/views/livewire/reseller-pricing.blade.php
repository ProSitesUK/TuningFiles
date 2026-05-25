<div class="page">
    <div class="page-head">
        <div>
            <h1 class="page-title">Pricing</h1>
            <p class="page-sub">Manage credit packs available to your customers.</p>
        </div>
        <div class="page-actions">
            @if ($mode === 'list')
                <button type="button" wire:click="newPack" class="primary-btn primary-btn-sm">+ New pack</button>
            @else
                <button type="button" wire:click="cancelPack" class="ghost-btn ghost-btn-sm">← Back to list</button>
            @endif
        </div>
    </div>

    @if ($flash)
        <div class="card card-pad" style="border-color: var(--success); background: var(--success-soft); margin-bottom: 16px">
            <span style="color: var(--success); font-weight: 500">{{ $flash }}</span>
        </div>
    @endif

    @if ($mode === 'form')
        <div class="card card-pad" style="max-width: 640px; margin-bottom: 24px">
            <div class="va-form-title">{{ $editingId ? 'Edit credit pack' : 'New credit pack' }}</div>
            <div class="va-form">
                <label class="va-field">
                    <span>Name</span>
                    <input type="text" wire:model.defer="form.name" placeholder="e.g. Starter Pack" />
                    @error('form.name') <em class="va-err">{{ $message }}</em> @enderror
                </label>
                <div class="va-grid-2">
                    <label class="va-field">
                        <span>Credits</span>
                        <input type="number" wire:model.defer="form.credits" min="1" />
                        @error('form.credits') <em class="va-err">{{ $message }}</em> @enderror
                    </label>
                    <label class="va-field">
                        <span>Price (pennies) <em class="t-mute small">e.g. 2500 = &pound;25</em></span>
                        <input type="number" wire:model.defer="form.price_pennies" min="0" />
                        @error('form.price_pennies') <em class="va-err">{{ $message }}</em> @enderror
                    </label>
                </div>
                <label class="va-check">
                    <input type="checkbox" wire:model.defer="form.is_active" />
                    <span>Active (visible to customers)</span>
                </label>
                <div class="va-form-actions" style="margin-top: 14px">
                    <button type="button" wire:click="cancelPack" class="ghost-btn ghost-btn-sm">Cancel</button>
                    <button type="button" wire:click="savePack" class="primary-btn primary-btn-sm" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="savePack">Save</span>
                        <span wire:loading wire:target="savePack">Saving…</span>
                    </button>
                </div>
            </div>
        </div>
    @else
        <div class="card card-table" style="margin-bottom: 24px">
            <table class="t">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th class="num">Credits</th>
                        <th class="num">Price</th>
                        <th>Status</th>
                        <th style="width:140px">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($packs as $pack)
                        <tr class="t-row">
                            <td><strong>{{ $pack->name }}</strong></td>
                            <td class="num mono">{{ number_format($pack->credits) }}</td>
                            <td class="num mono">&pound;{{ number_format($pack->price_pennies / 100, 2) }}</td>
                            <td>
                                @if ($pack->is_active)
                                    <span class="badge badge-success">active</span>
                                @else
                                    <span class="badge badge-neutral">archived</span>
                                @endif
                            </td>
                            <td>
                                <div style="display:inline-flex; gap:4px">
                                    <button type="button" wire:click="editPack({{ $pack->id }})" class="ghost-btn ghost-btn-sm">Edit</button>
                                    <button type="button" wire:click="toggleActive({{ $pack->id }})" class="ghost-btn ghost-btn-sm">
                                        {{ $pack->is_active ? 'Archive' : 'Activate' }}
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align:center; padding:32px">
                                <span class="t-mute">No credit packs yet. Click "+ New pack" to create one.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="card card-pad" style="max-width: 640px">
            <p class="t-mute small">Your customers will see these packs along with the platform default packs when purchasing credits. Tenant packs are shown first.</p>
        </div>
    @endif
</div>
