<x-layouts.marketing>
    <div class="mk">
        @include('marketing.partials.nav')

        <section class="mk-section mk-section-narrow">
            <div class="mk-crumbs">
                <a href="{{ route('home') }}">Home</a>
                <span>·</span>
                <a href="{{ route('vehicles') }}">Vehicles</a>
                <span>·</span>
                <span class="crumb-active">{{ $make->name }}</span>
            </div>

            <header class="mk-pagehead">
                <div class="mk-pagehead-text">
                    <h1 class="mk-pagehead-title">{{ $make->name }} tuning files & remaps</h1>
                    <p class="mk-pagehead-sub">
                        {{ $models->count() }} model{{ $models->count() === 1 ? '' : 's' }} supported · stage 1, stage 2 & custom remaps · checksum-correct files in under 30 minutes.
                    </p>
                    <div class="mk-pagehead-actions">
                        <a href="{{ route('register') }}" class="primary-btn primary-btn-lg" style="text-decoration:none">Get your {{ $make->name }} tuned</a>
                        <a href="#models" class="ghost-btn ghost-btn-lg" style="text-decoration:none">Browse models</a>
                    </div>
                </div>
                @if ($make->logo_url)
                    <div class="mk-pagehead-mark">
                        <img src="{{ $make->logo_url }}" alt="{{ $make->name }} logo" />
                    </div>
                @endif
            </header>

            @if ($make->intro)
                <article class="mk-prose">
                    {!! Str::markdown($make->intro) !!}
                </article>
            @endif

            <h2 id="models" class="mk-h2">Models we tune</h2>
            <div class="vb-grid">
                @foreach ($models as $m)
                    @php $img = $m->image_url ?: $make->image_url; @endphp
                    <a href="{{ route('vehicles.model', [$make, $m]) }}" class="vb-card vb-card-link" style="text-decoration:none; color:inherit">
                        <div class="vb-card-media">
                            @if ($img)
                                <img src="{{ $img }}" alt="{{ $make->name }} {{ $m->name }}" loading="lazy" />
                            @else
                                <div class="vb-card-media-fallback">
                                    <span class="mono">{{ $make->name }}</span>
                                </div>
                            @endif
                            @if ($make->logo_url)
                                <img src="{{ $make->logo_url }}" alt="{{ $make->name }} logo" class="vb-card-logo" loading="lazy" />
                            @endif
                        </div>
                        <div class="vb-card-body">
                            <div class="vb-card-make small mono">{{ $make->name }}</div>
                            <h3 class="vb-card-model">{{ $m->name }}</h3>
                            <div class="vb-card-meta small t-mute">
                                {{ $m->variants_count }} variant{{ $m->variants_count === 1 ? '' : 's' }}
                                @if ($m->body_type) · {{ $m->body_type }} @endif
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </section>

        @include('marketing.partials.footer')
    </div>
</x-layouts.marketing>
