<div class="page">
    <div class="page-head">
        <div>
            <h1 class="page-title">Billing</h1>
            <p class="page-sub">Manage your subscription and billing details.</p>
        </div>
    </div>

    @if (session('status'))
        <div class="card card-pad" style="margin-bottom:14px; border-color: var(--success); background: var(--success-soft);">
            <span style="color: var(--success); font-weight: 500">{{ session('status') }}</span>
        </div>
    @endif

    <div class="card card-pad" style="max-width: 640px; margin-bottom: 24px">
        <div class="va-form-title">Current plan</div>

        @if ($profile && $plan)
            <div class="meta-grid" style="margin-bottom:18px">
                <div class="meta">
                    <div class="metric-label">Plan</div>
                    <div class="meta-val"><strong>{{ $plan->name }}</strong></div>
                </div>
                <div class="meta">
                    <div class="metric-label">Price</div>
                    <div class="meta-val mono">{{ $plan->priceFormatted() }}</div>
                </div>
                <div class="meta">
                    <div class="metric-label">Status</div>
                    <div class="meta-val">
                        @if ($profile->isSubscribed())
                            <span class="badge badge-success">{{ $profile->subscription_status }}</span>
                        @else
                            <span class="badge badge-warning">{{ $profile->subscription_status ?? 'none' }}</span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Usage bar --}}
            <div style="margin-bottom:18px">
                <div class="metric-label" style="margin-bottom:6px">Customer usage</div>
                @php
                    $max = $plan->isUnlimited() ? max($customerCount, 1) : $plan->max_customers;
                    $pct = $max > 0 ? min(100, round(($customerCount / $max) * 100)) : 0;
                @endphp
                <div style="background:var(--surface-2); border-radius:6px; height:8px; overflow:hidden">
                    <div style="background:var(--accent); height:100%; width:{{ $pct }}%; border-radius:6px; transition:width 0.3s"></div>
                </div>
                <div class="t-mute small" style="margin-top:4px">
                    {{ $customerCount }} / {{ $plan->isUnlimited() ? 'unlimited' : $plan->max_customers }} customers
                </div>
            </div>

            <div style="display:flex; gap:8px">
                <a href="{{ route('reseller.plans') }}" class="ghost-btn ghost-btn-sm" style="text-decoration:none">Change plan</a>
                @if ($profile->isSubscribed())
                    <form method="POST" action="{{ route('reseller.cancel') }}" onsubmit="return confirm('Are you sure you want to cancel your subscription?')">
                        @csrf
                        <button type="submit" class="ghost-btn ghost-btn-sm" style="color:var(--danger)">Cancel subscription</button>
                    </form>
                @endif
            </div>
        @else
            <p class="t-mute" style="margin-bottom:14px">You don't have an active subscription.</p>
            <a href="{{ route('reseller.plans') }}" class="primary-btn primary-btn-sm" style="text-decoration:none">View plans</a>
        @endif
    </div>
</div>
