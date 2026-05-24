<div>
    <div class="page-head" style="margin-bottom:18px">
        <div>
            <h1 class="page-title">Credits</h1>
            <p class="page-sub">Buy a pack — spend it as you go.</p>
        </div>
    </div>

    @if (session('status'))
        <div class="card card-pad" style="background:var(--success-soft); border-color:transparent; margin-bottom:18px; color:var(--success)">
            {{ session('status') }}
        </div>
    @endif

    <div class="card card-pad" style="margin-bottom:18px">
        <div class="metric-label">Current balance</div>
        <div class="metric-big">{{ $balance }} <span class="t-mute small" style="font-size:14px; font-family:var(--font-sans)">credits</span></div>
    </div>

    {{-- Confirmation messages --}}
    @if ($confirmation === 'bank')
        <div class="card card-pad" style="background:var(--info-soft); border-color:var(--info); margin-bottom:18px">
            <div style="font-weight:600; margin-bottom:6px; color:var(--info)">Bank transfer request submitted</div>
            <p class="small" style="margin:0">Your credit purchase is pending. Please transfer the amount to our bank account using reference <strong class="mono">{{ $bankReference }}</strong>. Credits will be added once payment is confirmed by our team.</p>
            <button type="button" wire:click="cancelSelection" class="ghost-btn ghost-btn-sm" style="margin-top:10px">Dismiss</button>
        </div>
    @endif

    @if ($confirmation === 'invoice')
        <div class="card card-pad" style="background:var(--info-soft); border-color:var(--info); margin-bottom:18px">
            <div style="font-weight:600; margin-bottom:6px; color:var(--info)">Invoice requested</div>
            <p class="small" style="margin:0">Your invoice has been created and sent. Credits will be added once payment is received. Check your email for the invoice details.</p>
            <button type="button" wire:click="cancelSelection" class="ghost-btn ghost-btn-sm" style="margin-top:10px">Dismiss</button>
        </div>
    @endif

    <div class="metric-label" style="margin-bottom:10px">Credit packs</div>
    <div class="mk-tiers" style="margin-bottom:24px">
        @foreach ($packs as $p)
            <div class="mk-tier {{ $p->slug === 'trade' ? 'mk-tier-featured' : '' }}">
                @if ($p->slug === 'trade') <div class="mk-tier-flag">Most popular</div> @endif
                <div class="mk-tier-head">
                    <div class="mk-tier-plan">{{ $p->name }}</div>
                    <div class="mk-tier-price"><span class="mk-tier-num">{{ $p->priceFormatted() }}</span></div>
                    <p class="mk-tier-blurb">{{ $p->credits }} credits · roughly {{ floor($p->credits / 32) }} Stage 1 tunes</p>
                </div>

                @if ($selectedPack === $p->id && ! $confirmation)
                    {{-- Payment method selector --}}
                    <div style="border-top:1px solid var(--border); padding-top:12px; margin-top:8px">
                        <div class="metric-label" style="margin-bottom:8px">Choose payment method</div>

                        @if ($selectedMethod === null)
                            <div style="display:flex; flex-direction:column; gap:6px">
                                @if ($stripeEnabled)
                                    <button type="button" wire:click="selectMethod('stripe')"
                                            class="primary-btn primary-btn-sm" style="width:100%; justify-content:center">
                                        Pay with card
                                    </button>
                                @endif
                                @if ($bankEnabled)
                                    <button type="button" wire:click="selectMethod('bank')"
                                            class="ghost-btn ghost-btn-sm" style="width:100%; justify-content:center">
                                        Pay by bank transfer
                                    </button>
                                @endif
                                @if ($invoiceEnabled)
                                    <button type="button" wire:click="selectMethod('invoice')"
                                            class="ghost-btn ghost-btn-sm" style="width:100%; justify-content:center">
                                        Request invoice
                                    </button>
                                @endif
                            </div>
                            <button type="button" wire:click="cancelSelection" class="ghost-btn ghost-btn-sm" style="width:100%; justify-content:center; margin-top:6px; color:var(--muted)">
                                Cancel
                            </button>
                        @endif

                        {{-- Bank transfer details --}}
                        @if ($selectedMethod === 'bank')
                            <div style="background:var(--bg); border:1px solid var(--border); border-radius:6px; padding:12px; margin-bottom:8px">
                                <div style="font-weight:600; margin-bottom:6px">Bank transfer details</div>
                                <pre style="margin:0; font-size:12px; white-space:pre-wrap; font-family:var(--font-mono)">{{ $bankDetails }}</pre>
                                <div style="margin-top:8px; padding:8px; background:var(--warning-soft); border-radius:4px">
                                    <div class="small"><strong>Your reference:</strong> <span class="mono">{{ $bankReference }}</span></div>
                                    <div class="small t-mute">Please use this reference when making your transfer.</div>
                                </div>
                            </div>
                            <div style="display:flex; gap:6px">
                                <button type="button" wire:click="processBank"
                                        class="primary-btn primary-btn-sm" style="flex:1; justify-content:center"
                                        wire:loading.attr="disabled">
                                    <span wire:loading.remove wire:target="processBank">I've sent the payment</span>
                                    <span wire:loading wire:target="processBank">Processing…</span>
                                </button>
                                <button type="button" wire:click="cancelSelection" class="ghost-btn ghost-btn-sm">Cancel</button>
                            </div>
                        @endif

                        {{-- Invoice confirmation --}}
                        @if ($selectedMethod === 'invoice')
                            <div style="background:var(--bg); border:1px solid var(--border); border-radius:6px; padding:12px; margin-bottom:8px">
                                <div style="font-weight:600; margin-bottom:4px">Request invoice</div>
                                <p class="small t-mute" style="margin:0">An invoice for <strong>{{ $p->priceFormatted() }}</strong> ({{ $p->credits }} credits) will be generated and sent to your email. Payment terms apply as per your agreement.</p>
                            </div>
                            <div style="display:flex; gap:6px">
                                <button type="button" wire:click="processInvoice"
                                        class="primary-btn primary-btn-sm" style="flex:1; justify-content:center"
                                        wire:loading.attr="disabled">
                                    <span wire:loading.remove wire:target="processInvoice">Confirm invoice request</span>
                                    <span wire:loading wire:target="processInvoice">Processing…</span>
                                </button>
                                <button type="button" wire:click="cancelSelection" class="ghost-btn ghost-btn-sm">Cancel</button>
                            </div>
                        @endif
                    </div>
                @else
                    {{-- Default buy button --}}
                    <button type="button" wire:click="selectPack({{ $p->id }})"
                            class="{{ $p->slug === 'trade' ? 'primary-btn primary-btn-lg' : 'ghost-btn ghost-btn-lg' }}"
                            style="width:100%; justify-content:center">
                        Buy {{ $p->name }}
                    </button>
                @endif
            </div>
        @endforeach
    </div>

    <div class="card card-table">
        <div class="card-head card-pad-x">
            <div class="metric-label">Recent transactions</div>
            <div class="card-head-r t-mute small">last 15</div>
        </div>
        <table class="t">
            <thead><tr>
                <th>When</th>
                <th>Type</th>
                <th>Note</th>
                <th class="num">Credits</th>
                <th class="num">Balance after</th>
            </tr></thead>
            <tbody>
                @forelse ($tx as $t)
                    <tr class="t-row">
                        <td class="mono small">{{ $t->created_at?->diffForHumans() }}</td>
                        <td>
                            {{ $t->type }}
                            @if ($t->payment_status === 'pending')
                                <span class="badge badge-warning" style="margin-left:4px">pending</span>
                            @elseif ($t->payment_status === 'failed')
                                <span class="badge badge-danger" style="margin-left:4px">rejected</span>
                            @endif
                        </td>
                        <td class="t-mute small">{{ $t->note }}</td>
                        <td class="num mono">{{ $t->credits >= 0 ? '+' : '' }}{{ $t->credits }}</td>
                        <td class="num mono">{{ $t->balance_after }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="empty-cell">No credit transactions yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
