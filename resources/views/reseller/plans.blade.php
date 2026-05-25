<x-layouts.reseller>
    <div class="page">
        <div class="page-head">
            <div>
                <h1 class="page-title">Subscription plans</h1>
                <p class="page-sub">Choose a plan that fits your business.</p>
            </div>
        </div>

        @if (session('status'))
            <div class="card card-pad" style="margin-bottom:14px; border-color: var(--warning); background: var(--warning-soft);">
                <span style="color: var(--warning); font-weight: 500">{{ session('status') }}</span>
            </div>
        @endif

        <div style="display:grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap:18px; max-width:960px">
            @forelse ($plans as $plan)
                <div class="card card-pad" style="display:flex; flex-direction:column; justify-content:space-between">
                    <div>
                        <div style="font-weight:600; font-size:16px; margin-bottom:4px">{{ $plan->name }}</div>
                        <div class="mono" style="font-size:24px; font-weight:700; margin-bottom:12px">{{ $plan->priceFormatted() }}</div>

                        <ul style="list-style:none; padding:0; margin:0 0 18px">
                            <li class="t-mute small" style="padding:4px 0">
                                {{ $plan->isUnlimited() ? 'Unlimited' : $plan->max_customers }} customers
                            </li>
                            @if ($plan->features)
                                @foreach ($plan->features as $feature)
                                    <li class="t-mute small" style="padding:4px 0">{{ $feature }}</li>
                                @endforeach
                            @endif
                        </ul>
                    </div>

                    @if ($currentPlan === 'active')
                        @php
                            $currentMax = auth()->user()->resellerProfile?->max_customers;
                        @endphp
                        @if ($plan->max_customers === $currentMax)
                            <span class="badge badge-success" style="text-align:center; padding:10px">Current plan</span>
                        @else
                            <form method="POST" action="{{ route('reseller.subscribe', $plan->slug) }}">
                                @csrf
                                <button type="submit" class="ghost-btn" style="width:100%">Switch to {{ $plan->name }}</button>
                            </form>
                        @endif
                    @else
                        <form method="POST" action="{{ route('reseller.subscribe', $plan->slug) }}">
                            @csrf
                            <button type="submit" class="primary-btn" style="width:100%">Subscribe</button>
                        </form>
                    @endif
                </div>
            @empty
                <div class="card card-pad">
                    <p class="t-mute">No plans available yet. Please check back later.</p>
                </div>
            @endforelse
        </div>
    </div>
</x-layouts.reseller>
