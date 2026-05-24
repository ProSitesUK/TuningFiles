<div class="page">
    <div class="page-head">
        <div>
            <h1 class="page-title">Credits</h1>
            <p class="page-sub">Manage credit packs and apply manual adjustments.</p>
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

    {{-- Section A: Credit Pack management --}}
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
                <label class="va-field">
                    <span>Stripe Price ID <em class="t-mute small">(optional)</em></span>
                    <input type="text" wire:model.defer="form.stripe_price_id" placeholder="price_…" />
                    @error('form.stripe_price_id') <em class="va-err">{{ $message }}</em> @enderror
                </label>
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
                        <th>Stripe Price ID</th>
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
                            <td class="mono small t-mute">{{ $pack->stripe_price_id ?? '—' }}</td>
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
                            <td colspan="6" style="text-align:center; padding:32px">
                                <span class="t-mute">No credit packs yet. Click "+ New pack" to create one.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @endif

    {{-- Section B: Pending payments --}}
    @php
        $allPending = collect();

        // Add pending transactions (bank transfers)
        foreach ($pendingTransactions as $pt) {
            $allPending->push((object) [
                'id'       => $pt->id,
                'source'   => 'transaction',
                'name'     => $pt->user?->name ?? 'Unknown',
                'email'    => $pt->user?->email ?? '',
                'method'   => $pt->payment_method ?? 'bank',
                'credits'  => $pt->credits,
                'amount'   => $pt->amount_pennies ? '£' . number_format($pt->amount_pennies / 100, 2) : '—',
                'date'     => $pt->created_at,
                'status'   => $pt->payment_status,
            ]);
        }

        // Add pending invoices that don't already have a matching pending transaction
        foreach ($pendingInvoices as $pi) {
            $hasTx = $pendingTransactions->where('user_id', $pi->user_id)
                ->where('payment_method', 'invoice')
                ->where('credits', $pi->credits)
                ->isNotEmpty();
            if (! $hasTx) {
                $allPending->push((object) [
                    'id'       => $pi->id,
                    'source'   => 'invoice',
                    'name'     => $pi->user?->name ?? 'Unknown',
                    'email'    => $pi->user?->email ?? '',
                    'method'   => 'invoice',
                    'credits'  => $pi->credits,
                    'amount'   => '£' . number_format($pi->amount_pennies / 100, 2),
                    'date'     => $pi->created_at,
                    'status'   => $pi->status,
                ]);
            }
        }
    @endphp

    <div class="card card-table" style="margin-bottom: 24px">
        <div class="card-head card-pad-x">
            <div class="metric-label">Pending payments</div>
            <span class="t-mute small">{{ $allPending->count() }} awaiting action</span>
        </div>
        <table class="t">
            <thead>
                <tr>
                    <th>Customer</th>
                    <th>Method</th>
                    <th class="num">Credits</th>
                    <th class="num">Amount</th>
                    <th>Requested</th>
                    <th>Status</th>
                    <th style="width:160px">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($allPending as $p)
                    <tr class="t-row">
                        <td>
                            <div><strong>{{ $p->name }}</strong></div>
                            <div class="t-mute small">{{ $p->email }}</div>
                        </td>
                        <td>
                            @if ($p->method === 'bank')
                                <span class="badge badge-blue">bank transfer</span>
                            @elseif ($p->method === 'invoice')
                                <span class="badge badge-purple">invoice</span>
                            @else
                                <span class="badge badge-neutral">{{ $p->method }}</span>
                            @endif
                        </td>
                        <td class="num mono">{{ number_format($p->credits) }}</td>
                        <td class="num mono">{{ $p->amount }}</td>
                        <td class="mono small">{{ $p->date?->diffForHumans() }}</td>
                        <td>
                            @if ($p->status === 'pending' || $p->status === 'sent')
                                <span class="badge badge-warning">pending</span>
                            @elseif ($p->status === 'overdue')
                                <span class="badge badge-danger">overdue</span>
                            @else
                                <span class="badge badge-neutral">{{ $p->status }}</span>
                            @endif
                        </td>
                        <td>
                            @if ($p->source === 'transaction')
                                <div style="display:inline-flex; gap:4px">
                                    <button type="button" wire:click="approvePending({{ $p->id }})"
                                            wire:confirm="Approve this payment and grant {{ $p->credits }} credits?"
                                            class="primary-btn primary-btn-sm">Approve</button>
                                    <button type="button" wire:click="rejectPending({{ $p->id }})"
                                            wire:confirm="Reject this payment?"
                                            class="ghost-btn ghost-btn-sm" style="color:var(--danger)">Reject</button>
                                </div>
                            @else
                                <span class="t-mute small">Awaiting payment</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="empty-cell">No pending payments. All clear.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Section C: Manual credit adjustment --}}
    <div class="card card-pad" style="max-width: 640px">
        <div class="va-form-title">Manual credit adjustment</div>
        <p class="t-mute small" style="margin-bottom: 14px">Search for a customer, then apply a positive or negative credit adjustment. This creates an audit record.</p>

        <div class="va-form">
            {{-- User search --}}
            @if ($adjUserId)
                <div class="va-field">
                    <span>Customer</span>
                    <div style="display:flex; align-items:center; gap:8px">
                        <span style="flex:1; padding:6px 10px; border:1px solid var(--border); border-radius:6px; background:var(--bg); font-size:13px">{{ $adjUserName }}</span>
                        <button type="button" wire:click="clearUser" class="ghost-btn ghost-btn-sm">Change</button>
                    </div>
                </div>
            @else
                <label class="va-field">
                    <span>Search customer <em class="t-mute small">(name or email)</em></span>
                    <input type="text" wire:model.live.debounce.300ms="adjSearch" placeholder="Start typing…" autocomplete="off" />
                </label>
                @if ($searchResults->isNotEmpty())
                    <div style="border:1px solid var(--border); border-radius:6px; max-height:200px; overflow-y:auto; margin-top:-8px; margin-bottom:8px">
                        @foreach ($searchResults as $u)
                            <button type="button" wire:click="selectUser({{ $u->id }})"
                                    style="display:block; width:100%; text-align:left; padding:8px 12px; border:none; background:none; cursor:pointer; font-size:13px; border-bottom:1px solid var(--border)"
                                    onmouseover="this.style.background='var(--hover)'" onmouseout="this.style.background='none'">
                                <strong>{{ $u->name }}</strong> <span class="t-mute">{{ $u->email }}</span>
                            </button>
                        @endforeach
                    </div>
                @endif
                @error('adjUserId') <em class="va-err">{{ $message }}</em> @enderror
            @endif

            <div class="va-grid-2">
                <label class="va-field">
                    <span>Credits <em class="t-mute small">(+ to add, - to deduct)</em></span>
                    <input type="number" wire:model.defer="adjCredits" placeholder="e.g. 50 or -25" />
                    @error('adjCredits') <em class="va-err">{{ $message }}</em> @enderror
                </label>
                <label class="va-field">
                    <span>Note <em class="t-mute small">(required for audit)</em></span>
                    <input type="text" wire:model.defer="adjNote" placeholder="Reason for adjustment" />
                    @error('adjNote') <em class="va-err">{{ $message }}</em> @enderror
                </label>
            </div>

            <div class="va-form-actions" style="margin-top: 14px">
                <button type="button" wire:click="applyAdjustment" class="primary-btn primary-btn-sm" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="applyAdjustment">Apply adjustment</span>
                    <span wire:loading wire:target="applyAdjustment">Applying…</span>
                </button>
            </div>
        </div>
    </div>
</div>
