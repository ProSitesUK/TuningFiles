<x-layouts.marketing>
    <div class="mk">
        @include('marketing.partials.nav')

        <section class="mk-section mk-section-narrow">
            <div class="mk-section-head">
                <span class="mk-kicker">Supported vehicles</span>
                <h1 class="mk-section-title">Pick your make.</h1>
                <p class="mk-section-sub">{{ $makes->count() }} OEMs covered — tap a brand for every model and variant we tune.</p>
            </div>

            <div class="vb-makes">
                @foreach ($makes as $m)
                    <a href="{{ route('vehicles.make', $m) }}" class="vb-make-tile" style="text-decoration:none; color:inherit">
                        <span class="vb-make-tile-logo">
                            @if ($m->logo_url)
                                <img src="{{ $m->logo_url }}" alt="{{ $m->name }}" loading="lazy" />
                            @else
                                <span class="va-logo-fallback mono">{{ strtoupper(substr($m->name, 0, 2)) }}</span>
                            @endif
                        </span>
                        <span class="vb-make-tile-name">{{ $m->name }}</span>
                        <span class="vb-make-tile-meta mono small">{{ $m->models_count }} model{{ $m->models_count === 1 ? '' : 's' }}</span>
                    </a>
                @endforeach
            </div>
        </section>

        @include('marketing.partials.footer')
    </div>
</x-layouts.marketing>
