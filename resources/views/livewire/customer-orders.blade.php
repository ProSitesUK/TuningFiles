<div>
    <div class="page-head" style="margin-bottom:18px">
        <div>
            <h1 class="page-title">Your orders</h1>
            <p class="page-sub">{{ $orders->total() }} in total</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('app.orders.new') }}" class="primary-btn" style="text-decoration:none"><x-icon name="plus" size="14" /> New tune</a>
        </div>
    </div>

    <div class="card card-table">
        <table class="t">
            <thead><tr>
                <th>Order</th>
                <th>Vehicle</th>
                <th>Options</th>
                <th>Status</th>
                <th class="num">Credits</th>
                <th class="num">Elapsed</th>
            </tr></thead>
            <tbody>
                @forelse ($orders as $o)
                    <tr class="t-row t-row-link" onclick="window.location='{{ route('app.orders.show', $o) }}'">
                        <td class="mono">#{{ $o->reference }}</td>
                        <td>{{ $o->vehicle_label }} <span class="t-mute mono small">· {{ $o->vehicle_year }}</span></td>
                        <td>{{ $o->options_label }}</td>
                        <td>@include('partials.status-badge', ['status' => $o->status])</td>
                        <td class="num mono">{{ $o->credits_cost }}</td>
                        <td class="num mono">{{ $o->elapsedLabel() }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="empty-cell">
                        No orders yet. <a href="{{ route('app.orders.new') }}" style="color:var(--accent)">Upload your first file →</a>
                    </td></tr>
                @endforelse
            </tbody>
        </table>
        @if ($orders->hasPages())
            <div class="table-foot">
                <span class="t-mute">{{ $orders->total() }} orders</span>
                <div class="pager">{!! $orders->links() !!}</div>
            </div>
        @endif
    </div>
</div>
