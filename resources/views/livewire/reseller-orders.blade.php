<div>
    <div class="page">
        <div class="page-head">
            <div>
                <h1 class="page-title">Orders</h1>
                <p class="page-sub">All orders placed by your customers.</p>
            </div>
        </div>

        {{-- Filters --}}
        <div class="filterbar">
            <div class="chips">
                <button wire:click="$set('status', '')" class="chip {{ $status === '' ? 'chip-active' : '' }}">All</button>
                @foreach (\App\Models\Order::STATUSES as $s)
                    <button wire:click="$set('status', '{{ $s }}')" class="chip {{ $status === $s ? 'chip-active' : '' }}">{{ ucfirst(str_replace('_', ' ', $s)) }}</button>
                @endforeach
            </div>
            <div>
                <select wire:model.live="customerId"
                        style="padding:7px 10px; border:1px solid var(--border); border-radius:var(--r-sm); background:var(--surface); color:var(--ink); font-size:13px; font-family:inherit; outline:0;">
                    <option value="">All customers</option>
                    @foreach ($subCustomers as $sc)
                        <option value="{{ $sc->id }}">{{ $sc->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- Table --}}
        <div class="card card-table">
            <table class="t">
                <thead><tr>
                    <th>Ref</th>
                    <th>Customer</th>
                    <th>Vehicle</th>
                    <th>Options</th>
                    <th>Status</th>
                    <th class="num">Credits</th>
                    <th class="num">Elapsed</th>
                    <th class="num">Date</th>
                </tr></thead>
                <tbody>
                    @forelse ($orders as $o)
                        <tr class="t-row" style="cursor:pointer" onclick="window.location='{{ route('reseller.orders.show', $o) }}'">
                            <td class="mono">#{{ $o->reference }}</td>
                            <td>{{ $o->customer?->name ?? '—' }}</td>
                            <td>{{ $o->vehicle_label }}</td>
                            <td>{{ $o->options_label }}</td>
                            <td>@include('partials.status-badge', ['status' => $o->status])</td>
                            <td class="num mono">{{ $o->credits_cost }}</td>
                            <td class="num mono">{{ $o->elapsedLabel() }}</td>
                            <td class="num">{{ $o->created_at->format('d M Y') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="empty-cell">
                            No orders match the current filters.
                        </td></tr>
                    @endforelse
                </tbody>
            </table>
            @if ($orders->hasPages())
                <div class="table-foot">
                    {{ $orders->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
