<div>
    <div class="page">
        <div class="page-head">
            <div>
                <h1 class="page-title">Customers</h1>
                <p class="page-sub">Manage the customers linked to your reseller account.</p>
            </div>
            <div class="page-actions">
                <a href="{{ route('reseller.invite') }}" class="primary-btn" style="text-decoration:none"><x-icon name="plus" size="14" /> Invite customer</a>
            </div>
        </div>

        @if (session()->has('message'))
            <div class="card card-pad" style="margin-bottom:14px; border-color: var(--success); background: var(--success-soft);">
                {{ session('message') }}
            </div>
        @endif

        {{-- Search --}}
        <div class="filterbar">
            <div style="display:flex; align-items:center; gap:8px; flex:1; max-width:360px">
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search customers..."
                       style="flex:1; padding:8px 12px; border:1px solid var(--border); border-radius:var(--r-sm); background:var(--surface); color:var(--ink); font-size:13px; outline:0; font-family:inherit;" />
            </div>
        </div>

        {{-- Table --}}
        <div class="card card-table">
            <table class="t">
                <thead><tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Plan</th>
                    <th class="num">Credit balance</th>
                    <th class="num">Orders</th>
                    <th>Joined</th>
                    <th>Actions</th>
                </tr></thead>
                <tbody>
                    @forelse ($customers as $c)
                        <tr class="t-row">
                            <td style="font-weight:600">{{ $c->name }}</td>
                            <td>{{ $c->email }}</td>
                            <td>{{ $c->customerProfile?->plan ?? '—' }}</td>
                            <td class="num mono">{{ $c->customerProfile?->credit_balance ?? 0 }}</td>
                            <td class="num mono">{{ $c->orders_count }}</td>
                            <td>{{ $c->created_at->format('d M Y') }}</td>
                            <td>
                                <div style="display:flex; gap:4px;">
                                    <a href="{{ route('reseller.orders', ['customerId' => $c->id]) }}" class="ghost-btn ghost-btn-sm" style="text-decoration:none">View orders</a>
                                    <button wire:click="removeCustomer({{ $c->id }})" wire:confirm="Remove {{ $c->name }} from your customers? They will keep their account but no longer be linked to you." class="ghost-btn ghost-btn-sm" style="color:var(--danger)">Remove</button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="empty-cell">
                            No customers yet. <a href="{{ route('reseller.invite') }}" style="color:var(--accent)">Invite your first customer &rarr;</a>
                        </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
