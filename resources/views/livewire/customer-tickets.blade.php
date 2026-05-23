<div class="page">
    <div class="page-head">
        <div>
            <h1 class="page-title">Support tickets</h1>
            <p class="page-sub">{{ $tickets->count() }} {{ $tickets->count() === 1 ? 'ticket' : 'tickets' }}</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('app.tickets.new') }}" class="primary-btn" style="text-decoration:none"><x-icon name="plus" size="14" /> New ticket</a>
        </div>
    </div>

    <div class="chips" style="margin-bottom:14px">
        <button wire:click="setFilter('all')" class="chip {{ $filter === 'all' ? 'chip-active' : '' }}">All</button>
        <button wire:click="setFilter('open')" class="chip {{ $filter === 'open' ? 'chip-active' : '' }}">Open</button>
        <button wire:click="setFilter('resolved')" class="chip {{ $filter === 'resolved' ? 'chip-active' : '' }}">Resolved</button>
    </div>

    @forelse ($tickets as $ticket)
        <a href="{{ route('app.tickets.show', $ticket) }}" class="card card-pad" style="display:block; text-decoration:none; color:inherit; margin-bottom:10px; transition: border-color 0.12s;" onmouseover="this.style.borderColor='var(--border-strong)'" onmouseout="this.style.borderColor='var(--border)'">
            <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:12px">
                <div style="flex:1; min-width:0">
                    <div style="display:flex; align-items:center; gap:8px; margin-bottom:4px">
                        <span style="font-weight:600; font-size:14px">{{ $ticket->subject }}</span>
                        @if ($ticket->status === 'open')
                            <span class="badge badge-green"><span class="badge-dot"></span> Open</span>
                        @elseif ($ticket->status === 'resolved')
                            <span class="badge badge-neutral">Resolved</span>
                        @else
                            <span class="badge badge-neutral">{{ ucfirst($ticket->status) }}</span>
                        @endif
                    </div>
                    <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap">
                        @if ($ticket->order)
                            <span class="mono small t-mute">Order #{{ $ticket->order->reference }}</span>
                            <span class="t-mute">&middot;</span>
                        @endif
                        @php $lastMsg = $ticket->messages->first(); @endphp
                        @if ($lastMsg)
                            <span class="small t-mute" style="overflow:hidden; text-overflow:ellipsis; white-space:nowrap; max-width:400px">{{ \Illuminate\Support\Str::limit($lastMsg->body, 80) }}</span>
                        @endif
                    </div>
                </div>
                <span class="small t-mute" style="white-space:nowrap">{{ $ticket->updated_at->diffForHumans() }}</span>
            </div>
        </a>
    @empty
        <div class="card card-pad" style="text-align:center; padding:40px 20px">
            <div class="t-mute" style="margin-bottom:8px">No tickets found.</div>
            <a href="{{ route('app.tickets.new') }}" style="color:var(--accent); text-decoration:none; font-weight:500">Open your first ticket &rarr;</a>
        </div>
    @endforelse
</div>
