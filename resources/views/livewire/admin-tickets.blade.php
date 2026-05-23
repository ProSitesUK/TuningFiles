<div class="page page-flush">
    <div class="three-pane">
        {{-- LEFT: ticket list --}}
        <div class="pane pane-l">
            <div class="pane-head">
                <h2 class="pane-title">Tickets <span class="t-mute mono">{{ $tickets->count() }}</span></h2>
            </div>
            <div class="pane-search">
                <x-icon name="search" size="13" />
                <input wire:model.live.debounce.250ms="search" placeholder="Search subject or customer…" />
            </div>
            <div class="chips chips-tight">
                @foreach ([['open','Open'],['resolved','Resolved'],['all','All']] as [$id,$label])
                    <button type="button" wire:click="$set('filter', '{{ $id }}')"
                            class="chip chip-sm {{ $filter === $id ? 'chip-active' : '' }}">{{ $label }}</button>
                @endforeach
            </div>
            <div class="cust-list">
                @forelse ($tickets as $t)
                    <button type="button" wire:click="selectTicket({{ $t->id }})"
                            class="cust-row {{ $t->id === $selected ? 'cust-row-active' : '' }}">
                        <div class="cust-row-text">
                            <div class="cust-row-name">
                                {{ Str::limit($t->subject, 36) }}
                                @if ($t->status === 'open')
                                    <span class="badge badge-success">open</span>
                                @else
                                    <span class="badge badge-neutral">{{ $t->status }}</span>
                                @endif
                                @if ($t->priority === 'urgent')
                                    <span class="badge badge-danger">urgent</span>
                                @elseif ($t->priority === 'high')
                                    <span class="badge badge-warning">high</span>
                                @endif
                            </div>
                            <div class="cust-row-meta mono small">
                                {{ $t->customer?->name ?? '?' }} · {{ $t->messages_count }} msg · {{ $t->updated_at->diffForHumans(short: true) }}
                            </div>
                        </div>
                    </button>
                @empty
                    <div class="pane-empty">
                        <div class="empty-title">No tickets</div>
                        <div class="t-mute small">{{ $filter === 'open' ? 'No open tickets right now.' : 'Nothing found.' }}</div>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- MIDDLE: thread --}}
        <div class="pane pane-m">
            @if ($selTicket)
                <div class="pane-head">
                    <div class="crumbs-sm">
                        <span>Tickets</span>
                        <x-icon name="chevron" size="12" />
                        <span class="crumb-active">{{ Str::limit($selTicket->subject, 40) }}</span>
                    </div>
                    <div style="display:flex; gap:4px">
                        @if ($selTicket->status === 'open')
                            <button wire:click="resolveTicket" class="ghost-btn ghost-btn-sm">Resolve</button>
                        @else
                            <button wire:click="reopenTicket" class="ghost-btn ghost-btn-sm">Reopen</button>
                        @endif
                        @if (! $selTicket->assigned_to_id || $selTicket->assigned_to_id !== auth()->id())
                            <button wire:click="assignToMe" class="ghost-btn ghost-btn-sm">Assign to me</button>
                        @endif
                    </div>
                </div>

                <div class="pane-content">
                    {{-- Metadata --}}
                    <div class="metas" style="margin-bottom: 16px">
                        <div class="meta">
                            <div class="metric-label">Customer</div>
                            <div class="meta-val">{{ $selTicket->customer?->name ?? '—' }}</div>
                        </div>
                        <div class="meta">
                            <div class="metric-label">Order</div>
                            <div class="meta-val mono">{{ $selTicket->order ? '#'.$selTicket->order->reference : '—' }}</div>
                        </div>
                        <div class="meta">
                            <div class="metric-label">Assigned to</div>
                            <div class="meta-val">
                                @if ($selTicket->assignedTo)
                                    <span class="dot dot-{{ $selTicket->assignedTo->statusDot() }}"></span>
                                    {{ $selTicket->assignedTo->name }}
                                    <span class="t-mute small">({{ $selTicket->assignedTo->statusLabel() }})</span>
                                @else
                                    <span class="t-mute">Unassigned</span>
                                @endif
                            </div>
                        </div>
                        <div class="meta">
                            <div class="metric-label">Priority</div>
                            <div class="meta-val">
                                <select wire:change="setPriority($event.target.value)" style="font-size:13px; padding:2px 6px; border:1px solid var(--border); border-radius:4px; background:var(--bg); color:var(--ink)">
                                    @foreach (['low','normal','high','urgent'] as $p)
                                        <option value="{{ $p }}" {{ $selTicket->priority === $p ? 'selected' : '' }}>{{ ucfirst($p) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- Messages --}}
                    <div class="chat-thread">
                        @foreach ($messages as $msg)
                            @php
                                $isStaff = $msg->user_id !== $selTicket->customer_id;
                                $isInternal = $msg->is_internal;
                            @endphp
                            <div class="chat-msg {{ $isStaff ? 'chat-msg-staff' : 'chat-msg-mine' }} {{ $isInternal ? 'chat-msg-internal' : '' }}">
                                <div>{!! nl2br(e($msg->body)) !!}</div>
                                <div class="chat-msg-meta">
                                    @if ($isStaff)
                                        <span class="dot dot-{{ $msg->user->statusDot() }}"></span>
                                        <span>{{ $msg->user->name }}</span>
                                    @else
                                        <span>{{ $msg->user->name ?? 'Customer' }}</span>
                                    @endif
                                    · <span>{{ $msg->created_at->diffForHumans(short: true) }}</span>
                                    @if ($isInternal)
                                        · <span class="badge badge-neutral" style="font-size:9px">internal</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Reply --}}
                    <div class="chat-reply">
                        <textarea wire:model.defer="reply" placeholder="Type your reply…" rows="3"></textarea>
                        <div style="display:flex; flex-direction:column; gap:6px">
                            <button wire:click="sendReply" class="primary-btn primary-btn-sm" wire:loading.attr="disabled">Send</button>
                            <label class="va-check" style="font-size:10.5px">
                                <input type="checkbox" wire:model.defer="internal" />
                                Internal
                            </label>
                        </div>
                    </div>
                </div>
            @else
                <div class="pane-empty">
                    <div class="empty-title">Select a ticket</div>
                    <div class="t-mute small">Choose from the list on the left.</div>
                </div>
            @endif
        </div>

        {{-- RIGHT: empty for now, could add customer detail later --}}
        <div class="pane pane-r">
            <div class="pane-empty">
                <div class="t-mute small">Customer detail panel — coming soon.</div>
            </div>
        </div>
    </div>
</div>
