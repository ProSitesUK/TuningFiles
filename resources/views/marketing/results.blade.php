<x-layouts.marketing>
    <div class="mk">
        @include('marketing.partials.nav')

        <section class="mk-section mk-section-narrow">
            <div class="mk-section-head">
                <span class="mk-kicker">Real results</span>
                <h1 class="mk-section-title">Dyno results gallery</h1>
                <p class="mk-section-sub">Verified before-and-after gains from our customers.</p>
            </div>

            @if (request('make'))
                <div style="margin-bottom:16px">
                    <span class="t-mute small">Filtered by:</span>
                    <span class="chip chip-sm chip-active">{{ request('make') }}</span>
                    <a href="{{ route('results') }}" class="ghost-btn ghost-btn-sm" style="margin-left:6px; text-decoration:none">Clear</a>
                </div>
            @endif

            @if ($results->isEmpty())
                <div class="card card-pad" style="text-align:center; padding:48px 24px">
                    <div class="t-mute">No results yet. Be the first to share your gains!</div>
                </div>
            @else
                <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(280px, 1fr)); gap:16px">
                    @foreach ($results as $r)
                        <div class="card card-pad">
                            <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:8px">
                                <div>
                                    <div style="font-weight:600; font-size:14px">{{ $r->vehicle_label }}</div>
                                    @if ($r->vehicle_year)
                                        <div class="t-mute small">{{ $r->vehicle_year }}</div>
                                    @endif
                                </div>
                                <span class="chip chip-sm chip-static" style="background:var(--accent-soft); color:var(--accent)">{{ $r->tune_type }}</span>
                            </div>
                            <div style="display:flex; gap:16px; margin-top:12px">
                                <div>
                                    <div class="t-mute small">Stock</div>
                                    <div class="mono" style="font-size:20px; font-weight:700">{{ $r->stock_hp }}<span class="t-mute small"> HP</span></div>
                                </div>
                                <div style="display:flex; align-items:center; color:var(--success); font-size:18px; font-weight:700">→</div>
                                <div>
                                    <div class="t-mute small">Tuned</div>
                                    <div class="mono" style="font-size:20px; font-weight:700">{{ $r->tuned_hp }}<span class="t-mute small"> HP</span></div>
                                </div>
                            </div>
                            <div style="margin-top:8px; display:flex; align-items:center; gap:6px">
                                <span class="chip chip-sm chip-static" style="background:var(--success-soft); color:var(--success)">+{{ $r->hpGain() }} HP</span>
                                @if ($r->notes)
                                    <span class="t-mute small">{{ \Illuminate\Support\Str::limit($r->notes, 60) }}</span>
                                @endif
                            </div>
                            <div class="t-mute small" style="margin-top:8px">
                                Submitted by {{ $r->user?->name ?? 'anonymous' }} · {{ $r->created_at->format('j M Y') }}
                            </div>
                        </div>
                    @endforeach
                </div>

                @if ($results->hasPages())
                    <div style="margin-top:24px">{{ $results->links() }}</div>
                @endif
            @endif
        </section>

        @include('marketing.partials.footer')
    </div>
</x-layouts.marketing>
