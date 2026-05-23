<x-layouts.marketing>
    <div class="mk">
        @include('marketing.partials.nav')

        <section class="mk-section mk-section-narrow">
            <div class="mk-crumbs">
                <a href="{{ route('home') }}">Home</a>
                <span>·</span>
                <a href="{{ route('vehicles') }}">Vehicles</a>
                <span>·</span>
                <a href="{{ route('vehicles.make', $make) }}">{{ $make->name }}</a>
                <span>·</span>
                <span class="crumb-active">{{ $model->name }}</span>
            </div>

            <header class="mk-pagehead">
                <div class="mk-pagehead-text">
                    <h1 class="mk-pagehead-title">{{ $make->name }} {{ $model->name }} — tuning files & remaps</h1>
                    <p class="mk-pagehead-sub">
                        {{ $variants->count() }} variant{{ $variants->count() === 1 ? '' : 's' }} supported
                        @if ($model->body_type) · {{ $model->body_type }} @endif
                        · stage 1, stage 2 & custom maps · 30-minute SLA.
                    </p>
                    <div class="mk-pagehead-actions">
                        <a href="{{ route('register') }}" class="primary-btn primary-btn-lg" style="text-decoration:none">Upload your read</a>
                        <a href="{{ route('vehicles.make', $make) }}" class="ghost-btn ghost-btn-lg" style="text-decoration:none">← All {{ $make->name }}</a>
                    </div>
                </div>
                @php $hero = $model->image_url ?: $make->image_url; @endphp
                @if ($hero)
                    <div class="mk-pagehead-photo">
                        <img src="{{ $hero }}" alt="{{ $make->name }} {{ $model->name }}" />
                    </div>
                @endif
            </header>

            @if ($model->intro)
                <article class="mk-prose">
                    {!! Str::markdown($model->intro) !!}
                </article>
            @endif

            {{-- =============== VARIANTS TABLE =============== --}}
            <h2 class="mk-h2">Variants & expected gains</h2>
            <div class="card card-pad" style="overflow-x:auto; padding:0">
                <table class="t" style="width:100%">
                    <thead>
                        <tr>
                            <th>Gen</th>
                            <th>Years</th>
                            <th>Engine</th>
                            <th>Stock</th>
                            <th>Stage 1*</th>
                            <th>Stage 2*</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($variants as $v)
                            @php $est = \App\Services\TuneEstimator::estimate($v->stock_hp, $v->fuel); @endphp
                            <tr>
                                <td class="mono">{{ $v->generation ?: '—' }}</td>
                                <td class="mono">{{ $v->yearRange() }}</td>
                                <td>{{ $v->displacement ?? '—' }} <span class="t-mute small">· {{ $v->fuel }}</span></td>
                                <td class="mono">{{ $v->stock_hp ?? '—' }}{{ $v->stock_hp ? ' hp' : '' }}</td>
                                <td class="mono">{{ $est['stage1'] ? $est['stage1'].' hp (+'.$est['stage1Gain'].')' : '—' }}</td>
                                <td class="mono">{{ $est['stage2'] ? $est['stage2'].' hp (+'.$est['stage2Gain'].')' : '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <p class="t-mute small" style="margin-top:8px">* Stage figures are typical estimates. Final results depend on vehicle condition, fuel quality and ambient conditions. Every map is dyno-validated before delivery.</p>

            {{-- =============== ECUs =============== --}}
            @php
                $ecus = $variants->flatMap->ecus->unique('id');
            @endphp
            @if ($ecus->isNotEmpty())
                <h2 class="mk-h2">Supported ECUs</h2>
                <div class="vb-pill-row">
                    @foreach ($ecus as $ecu)
                        <span class="vb-pill mono">{{ $ecu->identifier }}</span>
                    @endforeach
                </div>
            @endif

            {{-- =============== TUNE OPTIONS =============== --}}
            <h2 class="mk-h2">Available tune options</h2>
            <div class="mk-tunes-grid">
                @foreach ($tunes as $tune)
                    <div class="mk-tune">
                        <div class="mk-tune-head">
                            <h3 class="mk-tune-name">{{ $tune->label }}</h3>
                            <span class="mk-tune-cost mono">{{ $tune->credit_cost }} cr</span>
                        </div>
                        @if ($tune->description)
                            <p class="mk-tune-desc">{{ $tune->description }}</p>
                        @endif
                    </div>
                @endforeach
            </div>

            {{-- =============== RELATED =============== --}}
            @if ($related->isNotEmpty())
                <h2 class="mk-h2">Other {{ $make->name }} models we tune</h2>
                <div class="vb-grid">
                    @foreach ($related as $r)
                        @php $img = $r->image_url ?: $make->image_url; @endphp
                        <a href="{{ route('vehicles.model', [$make, $r]) }}" class="vb-card vb-card-link" style="text-decoration:none; color:inherit">
                            <div class="vb-card-media">
                                @if ($img)
                                    <img src="{{ $img }}" alt="{{ $make->name }} {{ $r->name }}" loading="lazy" />
                                @else
                                    <div class="vb-card-media-fallback"><span class="mono">{{ $make->name }}</span></div>
                                @endif
                            </div>
                            <div class="vb-card-body">
                                <div class="vb-card-make small mono">{{ $make->name }}</div>
                                <h3 class="vb-card-model">{{ $r->name }}</h3>
                                <div class="vb-card-meta small t-mute">{{ $r->body_type ?: 'see variants' }}</div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif

            {{-- =============== CTA =============== --}}
            <section class="mk-cta" style="margin-top:48px; border-radius:14px">
                <div class="mk-cta-inner">
                    <div>
                        <h2 class="mk-cta-title">Ready to tune your {{ $model->name }}?</h2>
                        <p class="mk-cta-sub">Upload your ECU read, pick your map, and get a tested file in under 30 minutes.</p>
                    </div>
                    <a href="{{ route('register') }}" class="primary-btn primary-btn-lg" style="text-decoration:none">Start now →</a>
                </div>
            </section>
        </section>

        @include('marketing.partials.footer')
    </div>
</x-layouts.marketing>
