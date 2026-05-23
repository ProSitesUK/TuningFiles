<div>
    <section class="mk-section mk-section-narrow">

        {{-- ============= MAKES LANDING ============= --}}
        @if ($mode === 'makes')
            <div class="mk-section-head">
                <span class="mk-kicker">Supported vehicles</span>
                <h1 class="mk-section-title">Pick your make.</h1>
                <p class="mk-section-sub">{{ $makes->count() }} OEMs covered — tap a brand to see every model and variant we tune.</p>
            </div>

            <div class="vb-makes">
                @foreach ($makes as $m)
                    <button type="button" wire:click="selectMake('{{ $m->slug }}')" class="vb-make-tile">
                        <span class="vb-make-tile-logo">
                            @if ($m->logo_url)
                                <img src="{{ $m->logo_url }}" alt="{{ $m->name }}" loading="lazy" />
                            @else
                                <span class="va-logo-fallback mono">{{ strtoupper(substr($m->name, 0, 2)) }}</span>
                            @endif
                        </span>
                        <span class="vb-make-tile-name">{{ $m->name }}</span>
                        <span class="vb-make-tile-meta mono small">{{ $m->models_count }} model{{ $m->models_count === 1 ? '' : 's' }}</span>
                    </button>
                @endforeach
            </div>

        {{-- ============= MODELS / DRILL-DOWN ============= --}}
        @else
            <div class="mk-section-head">
                <span class="mk-kicker">
                    <button type="button" wire:click="clearMake" class="vb-crumb">← All makes</button>
                </span>
                <h1 class="mk-section-title">{{ $selMake?->name ?? 'All models' }}</h1>
                <p class="mk-section-sub">
                    {{ $models->count() }} model{{ $models->count() === 1 ? '' : 's' }}
                    @if ($selMake) from {{ $selMake->name }} @endif
                    — search, filter by fuel, then drill into a variant.
                </p>
            </div>

            {{-- Brand-icon strip --}}
            <div class="vb-makestrip">
                <button type="button" wire:click="clearMake"
                        class="vb-makestrip-cell {{ $makeSlug === 'all' ? 'vb-makestrip-cell-on' : '' }}"
                        title="All makes">
                    <span class="vb-makestrip-all">All</span>
                </button>
                @foreach ($makes as $m)
                    <button type="button" wire:click="selectMake('{{ $m->slug }}')"
                            class="vb-makestrip-cell {{ $makeSlug === $m->slug ? 'vb-makestrip-cell-on' : '' }}"
                            title="{{ $m->name }}">
                        @if ($m->logo_url)
                            <img src="{{ $m->logo_url }}" alt="{{ $m->name }}" loading="lazy" />
                        @else
                            <span class="vb-makestrip-fallback mono">{{ strtoupper(substr($m->name, 0, 2)) }}</span>
                        @endif
                        <span class="vb-makestrip-label">{{ $m->name }}</span>
                    </button>
                @endforeach
            </div>

            {{-- Secondary filters (search + fuel) --}}
            <div class="vb-filterbar">
                <div class="vb-search">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><path d="M21 21 l-4.3 -4.3"/></svg>
                    <input wire:model.live.debounce.250ms="search" type="text" placeholder="Search model name…" />
                </div>
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
                @if ($search !== '' || $fuel !== 'all' || $body !== 'all')
                    <button type="button" wire:click="resetFilters" class="ghost-btn ghost-btn-sm">Reset filters</button>
                @endif
            </div>

            {{-- Models grid --}}
            <div class="vb-grid">
                @forelse ($models as $m)
                    @php $img = $m->image_url ?: $m->make->image_url; @endphp
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
        @endif

    </section>
</div>
