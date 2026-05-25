<div class="page">
    <div class="page-head">
        <div>
            <h1 class="page-title">Payments</h1>
            <p class="page-sub">Approve or reject bank transfers and invoice payments from your customers.</p>
        </div>
    </div>

    @if ($flash)
        <div class="card card-pad" style="border-color: var(--success); background: var(--success-soft); margin-bottom: 16px">
            <span style="color: var(--success); font-weight: 500">{{ $flash }}</span>
        </div>
    @endif

    <div class="chips" style="margin-bottom:14px">
        @foreach ([['pending','Pending'],['completed','Approved'],['failed','Rejected'],['all','All']] as [$id,$label])
            <button type="button" wire:click="$set('filter', '{{ $id }}')"
                    class="chip {{ $filter === $id ? 'chip-active' : '' }}">
                {{ $label }}
                @if ($id === 'pending' && $pendingCount > 0)
                    <span class="chip-count">{{ $pendingCount }}</span>
                @endif
            </button>
        @endforeach
    </div>

    @if ($transactions->isEmpty())
        <div class="card card-pad" style="text-align:center; padding:40px">
            <div class="empty-title">{{ $filter === 'pending' ? 'No pending payments' : 'No payments found' }}</div>
            <div class="t-mute small">{{ $filter === 'pending' ? 'When a customer pays by bank transfer, it will appear here for approval.' : 'Try changing the filter.' }}</div>
        </div>
    @else
        <div class="card card-table" style="overflow-x:auto">
            <table class="t" style="width:100%">
                <thead><tr>
                    <th>Customer</th>
                    <th>Method</th>
                    <th class="num">Credits</th>
                    <th class="num">Amount</th>
                    <th>Reference</th>
                    <th>Date</th>
                    <th>Status</th>
                    @if ($filter === 'pending')
                        <th>Actions</th>
                    @endif
                </tr></thead>
                <tbody>
                    @foreach ($transactions as $tx)
                        <tr>
                            <td>
                                <div>{{ $tx->user?->name }}</div>
                                <div class="t-mute small">{{ $tx->user?->email }}</div>
                            </td>
                            <td>
                                <span class="badge {{ $tx->payment_method === 'bank' ? 'badge-blue' : 'badge-purple' }}">{{ $tx->payment_method }}</span>
                            </td>
                            <td class="num mono">{{ $tx->credits }}</td>
                            <td class="num mono">£{{ number_format(($tx->amount_pennies ?? 0) / 100, 2) }}</td>
                            <td class="mono small">{{ Str::limit($tx->note, 30) }}</td>
                            <td class="t-mute small">{{ $tx->created_at?->diffForHumans(short: true) }}</td>
                            <td>
                                @if ($tx->payment_status === 'pending')
                                    <span class="badge badge-warning">pending</span>
                                @elseif ($tx->payment_status === 'completed')
                                    <span class="badge badge-success">approved</span>
                                @else
                                    <span class="badge badge-neutral">{{ $tx->payment_status }}</span>
                                @endif
                            </td>
                            @if ($filter === 'pending')
                                <td style="white-space:nowrap">
                                    <button wire:click="approvePending({{ $tx->id }})" wire:confirm="Approve this payment and grant {{ $tx->credits }} credits?" class="primary-btn primary-btn-sm">Approve</button>
                                    <button wire:click="rejectPending({{ $tx->id }})" wire:confirm="Reject this payment?" class="ghost-btn ghost-btn-sm" style="color:var(--danger)">Reject</button>
                                </td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
