<div>
    <section class="mk-section mk-section-narrow">
        <div class="mk-section-head">
            <span class="mk-kicker">Supported vehicles</span>
            <h1 class="mk-section-title">Find your vehicle.</h1>
            <p class="mk-section-sub">{{ $models->count() }} model{{ $models->count() === 1 ? '' : 's' }} across {{ $makes->count() }} makes — petrol, diesel, hybrid and electric.</p>
        </div>

        {{-- ============= FILTER BAR ============= --}}
        <div class="vb-filterbar">
            <div class="vb-search">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><path d="M21 21 l-4.3 -4.3"/></svg>
                <input wire:model.live.debounce.250ms="search" type="text" placeholder="Search make or model…" />
            </div>
            <select wire:model.live="makeSlug" class="vb-select">
                <option value="all">All makes</option>
                @foreach ($makes as $m)
                    <option value="{{ $m->slug }}">{{ $m->name }}</option>
                @endforeach
            </select>
            <div class="vb-chips">
                @foreach ([
                    ['all',      'All'],
                    ['petrol',   'Petrol'],
                    ['diesel',   'Diesel'],
                    ['hybrid',   'Hybrid'],
                    ['electric', 'Electric'],
                ] as [$id, $label])
                    <button type="button" wire:click="$set('fuel', '{{ $id }}')"
                            class="chip chip-sm {{ $fuel === $id ? 'chip-active' : '' }}">{{ $label }}</button>
                @endforeach
            </div>
            @if ($search !== '' || $makeSlug !== 'all' || $fuel !== 'all' || $body !== 'all')
                <button type="button" wire:click="resetFilters" class="ghost-btn ghost-btn-sm">Reset</button>
            @endif
        </div>

        {{-- ============= GRID ============= --}}
        <div class="vb-grid">
            @forelse ($models as $m)
                @php
                    $img = $m->image_url ?: $m->make->image_url;
                @endphp
                <article class="vb-card">
                    <div class="vb-card-media">
                        @if ($img)
                            <img src="{{ $img }}" alt="{{ $m->make->name }} {{ $m->name }}" loading="lazy" />
                        @else
                            <div class="vb-card-media-fallback">
                                <span class="mono">{{ $m->make->name }}</span>
                            </div>
                        @endif
                        @if ($m->make->logo_url)
                            <img src="{{ $m->make->logo_url }}" alt="{{ $m->make->name }} logo" class="vb-card-logo" loading="lazy" />
                        @endif
                    </div>
                    <div class="vb-card-body">
                        <div class="vb-card-make small mono">{{ $m->make->name }}</div>
                        <h3 class="vb-card-model">{{ $m->name }}</h3>
                        <div class="vb-card-meta small t-mute">
                            {{ $m->variants_count }} variant{{ $m->variants_count === 1 ? '' : 's' }}
                            @if ($m->body_type) · {{ $m->body_type }} @endif
                        </div>
                    </div>
                </article>
            @empty
                <div class="vb-empty">
                    <div class="empty-title">Nothing matched those filters</div>
                    <div class="t-mute small">Try widening the search or clearing the chips.</div>
                </div>
            @endforelse
        </div>
    </section>
</div>
