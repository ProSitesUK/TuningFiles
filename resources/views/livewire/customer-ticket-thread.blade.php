<div>
    <div class="crumbs-sm" style="margin-bottom:8px">
        <a href="{{ route('app.tickets.index') }}" style="color:var(--muted); text-decoration:none">Tickets</a>
        <x-icon name="chevron" size="12" />
        <span class="crumb-active">{{ \Illuminate\Support\Str::limit($ticket->subject, 40) }}</span>
    </div>

    <div class="page-head" style="margin-bottom:18px">
        <div>
            <h1 class="page-title">{{ $ticket->subject }}</h1>
            <div class="cust-meta">
                @if ($ticket->status === 'open')
                    <span class="badge badge-green"><span class="badge-dot"></span> Open</span>
                @elseif ($ticket->status === 'resolved')
                    <span class="badge badge-neutral">Resolved</span>
                @else
                    <span class="badge badge-neutral">{{ ucfirst($ticket->status) }}</span>
                @endif

                @if ($ticket->order)
                    <span class="t-mute">&middot;</span>
                    <a href="{{ route('app.orders.show', $ticket->order) }}" class="mono small" style="color:var(--accent); text-decoration:none">Order #{{ $ticket->order->reference }}</a>
                @endif

                @if ($assignee)
                    <span class="t-mute">&middot;</span>
                    <span class="small" style="display:inline-flex; align-items:center; gap:4px">
                        <span class="dot dot-{{ $assignee->statusDot() }}"></span>
                        {{ $assignee->name }} <span class="t-mute">({{ $assignee->statusLabel() }})</span>
                    </span>
                @endif
            </div>
        </div>
    </div>

    <div class="card card-pad">
        <div class="chat-thread">
            @foreach ($messages as $msg)
                @php $isMine = $msg->user_id === auth()->id(); @endphp
                <div class="chat-msg {{ $isMine ? 'chat-msg-mine' : 'chat-msg-staff' }}">
                    @unless ($isMine)
                        <div class="chat-msg-meta" style="margin-top:0; margin-bottom:4px">
                            <span class="dot dot-{{ $msg->user->statusDot() }}" style="width:6px; height:6px"></span>
                            <strong style="font-size:12px; color:var(--ink)">{{ $msg->user->name }}</strong>
                        </div>
                    @endunless
                    <div>{!! nl2br(e($msg->body)) !!}</div>
                    <div class="chat-msg-meta">
                        <span>{{ $msg->created_at->diffForHumans() }}</span>
                    </div>
                </div>
            @endforeach
        </div>

        @if ($ticket->status !== 'resolved')
            <form wire:submit="sendReply" class="chat-reply">
                <textarea wire:model="reply" placeholder="Type your reply..." rows="2"></textarea>
                <button type="submit" class="primary-btn" style="align-self:flex-end">Send</button>
            </form>
            @error('reply') <em class="va-err" style="margin-top:4px">{{ $message }}</em> @enderror
        @else
            <div class="t-mute small" style="text-align:center; padding:12px 0">This ticket has been resolved. <a href="{{ route('app.tickets.new') }}" style="color:var(--accent); text-decoration:none">Open a new ticket</a> if you need further help.</div>
        @endif
    </div>
</div>
