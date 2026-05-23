<x-layouts.marketing>
    <div class="mk">
        {{-- =================== NAV =================== --}}
        <header class="mk-nav">
            <div class="mk-nav-inner">
                <a href="{{ route('home') }}" class="mk-brand" style="text-decoration:none">
                    <span class="mk-brand-mark">
                        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="9" /><path d="M12 3 V12 L18 15" />
                        </svg>
                    </span>
                    <span>tuningfiles</span>
                </a>
                <nav class="mk-nav-links">
                    <a href="#how">How it works</a>
                    <a href="#vehicles">Supported</a>
                    <a href="#pricing">Pricing</a>
                    <a href="#tuners">For tuners</a>
                </nav>
                <div class="mk-nav-actions">
                    <x-theme-toggle />
                    @auth
                        <a href="{{ route('dashboard') }}" class="primary-btn primary-btn-sm" style="text-decoration:none">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="ghost-btn ghost-btn-sm" style="text-decoration:none">Sign in</a>
                        <a href="{{ route('register') }}" class="primary-btn primary-btn-sm" style="text-decoration:none">Get started</a>
                    @endauth
                </div>
            </div>
        </header>

        {{-- =================== HERO =================== --}}
        <section class="mk-hero">
            <div class="mk-hero-inner">
                <div class="mk-hero-eyebrow">
                    <span class="dot dot-ok"></span> 412 files tuned today · avg 14 min
                </div>
                <h1 class="mk-hero-title">
                    Professional ECU files.<br/>
                    <span class="mk-accent">Delivered in minutes,</span> not days.
                </h1>
                <p class="mk-hero-sub">
                    Stage 1 to full custom remaps from a network of vetted tuners.
                    Upload your read, get a tested file back — checksum-correct, dyno-validated, original retained.
                </p>
                <div class="mk-hero-actions">
                    <a href="{{ route('register') }}" class="primary-btn primary-btn-lg" style="text-decoration:none">Open a workshop account</a>
                    <a href="{{ route('login') }}" class="ghost-btn ghost-btn-lg" style="text-decoration:none">Sign in</a>
                </div>
                <div class="mk-hero-trust">
                    <span>Trusted by</span>
                    <span class="mk-trust-row">
                        <em>1,412</em> workshops
                        <span class="mk-trust-sep"></span>
                        <em>62</em> countries
                        <span class="mk-trust-sep"></span>
                        <em>£3.4M</em> processed / mo
                    </span>
                </div>
            </div>

            {{-- Hero visual: browser-frame queue preview --}}
            <div class="mk-hero-visual">
                <div class="mk-preview">
                    <div class="mk-preview-bar">
                        <span class="mk-preview-dot"></span><span class="mk-preview-dot"></span><span class="mk-preview-dot"></span>
                        <span class="mk-preview-url mono">tuningfiles.app / live</span>
                    </div>
                    <div class="mk-preview-body">
                        <div class="mk-preview-kpis">
                            <div class="mk-mini-kpi"><span>Intake / hr</span><b>24</b></div>
                            <div class="mk-mini-kpi"><span>In progress</span><b>8</b></div>
                            <div class="mk-mini-kpi mk-mini-kpi-accent"><span>SLA today</span><b>98.2%</b></div>
                        </div>
                        <div class="mk-preview-cards">
                            <div class="mk-pcard">
                                <div class="mk-pcard-row"><span class="mono small t-mute">#4471</span><span class="mk-pcard-tag mk-pcard-tag-warn">14m</span></div>
                                <div class="mk-pcard-veh">Golf R MK7</div>
                                <div class="mk-pcard-opt">Stage 1 + EGR</div>
                            </div>
                            <div class="mk-pcard">
                                <div class="mk-pcard-row"><span class="mono small t-mute">#4470</span><span class="mk-pcard-tag mk-pcard-tag-warn">22m</span></div>
                                <div class="mk-pcard-veh">BMW 335i</div>
                                <div class="mk-pcard-opt">Stage 2</div>
                                <div class="mk-pcard-prog"><span style="width:50%"></span></div>
                            </div>
                            <div class="mk-pcard">
                                <div class="mk-pcard-row"><span class="mono small t-mute">#4468</span><span class="mk-pcard-tag mk-pcard-tag-ok">ready</span></div>
                                <div class="mk-pcard-veh">Audi RS3 8V</div>
                                <div class="mk-pcard-opt">Stage 1</div>
                            </div>
                            <div class="mk-pcard">
                                <div class="mk-pcard-row"><span class="mono small t-mute">#4465</span><span class="mk-pcard-tag mk-pcard-tag-ok">delivered</span></div>
                                <div class="mk-pcard-veh">VW Polo GTI</div>
                                <div class="mk-pcard-opt">Stage 1</div>
                            </div>
                        </div>
                        <div class="mk-preview-curve">
                            <svg viewBox="0 0 320 70" preserveAspectRatio="none">
                                <path d="M0,55 Q40,52 60,46 T120,30 T200,16 T320,8" fill="none" stroke="var(--accent)" stroke-width="2" stroke-linecap="round" />
                                <path d="M0,60 Q40,58 60,54 T120,46 T200,38 T320,32" fill="none" stroke="var(--muted)" stroke-width="1.5" stroke-dasharray="3 3" stroke-linecap="round" />
                            </svg>
                            <span class="mk-preview-pill mono">tuned · +52 hp</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- =================== STAT STRIP =================== --}}
        <section class="mk-strip">
            <div class="mk-strip-inner">
                @foreach ([
                    ['42k+', 'files / month'],
                    ['14m', 'avg turnaround'],
                    ['98.2%', 'SLA hit rate'],
                    ['0.8%', 'refund rate'],
                    ['62', 'countries'],
                ] as [$n, $l])
                    <div class="mk-stat"><div class="mk-stat-n">{{ $n }}</div><div class="mk-stat-l">{{ $l }}</div></div>
                @endforeach
            </div>
        </section>

        {{-- =================== HOW IT WORKS =================== --}}
        <section id="how" class="mk-section">
            <div class="mk-section-head">
                <span class="mk-kicker">How it works</span>
                <h2 class="mk-section-title">From read to road in three steps.</h2>
            </div>
            <div class="mk-steps">
                @foreach ([
                    ['01', 'Upload your read', 'Drop in your ECU read from KESS, Autotuner, MPPS or PCM. We validate the ECU id, checksum and DTCs before it leaves your browser.'],
                    ['02', 'A tuner picks it up', 'Auto-assigned by vehicle expertise and current load. You can watch the timeline live — every map change, every QA pass.'],
                    ['03', 'Get the tuned file', 'Delivered with a 24-hour revision window, a torque/HP preview chart, and the original file retained side-by-side.'],
                ] as [$n, $t, $body])
                    <div class="mk-step">
                        <div class="mk-step-n mono">{{ $n }}</div>
                        <h3 class="mk-step-t">{{ $t }}</h3>
                        <p class="mk-step-body">{{ $body }}</p>
                    </div>
                @endforeach
            </div>
        </section>

        {{-- =================== VENDORS =================== --}}
        <section id="vehicles" class="mk-section mk-section-alt">
            <div class="mk-section-head">
                <span class="mk-kicker">Coverage</span>
                <h2 class="mk-section-title">Any ECU you can read, we can tune.</h2>
                <p class="mk-section-sub">Bosch, Siemens, Continental, Delphi. Petrol, diesel, hybrid. From a 2009 Polo to a 2025 GR Yaris.</p>
            </div>
            <div class="mk-vendors">
                @foreach ([
                    ['Bosch MED17', 'petrol DI · VAG, BMW'],
                    ['Bosch EDC17', 'diesel · VAG, BMW, Ford'],
                    ['Siemens MEVD17', 'BMW N20/N55/B58'],
                    ['Continental SID', 'Ford EcoBoost, PSA'],
                    ['Delphi DCM', 'Renault, Dacia diesel'],
                    ['MED40 / MED41', 'VAG MQB-Evo, Audi RS'],
                    ['Bosch MG1', 'Euro 6 diesel, gen-4 petrol'],
                    ['Hitachi / Denso', 'JDM, Nissan, Toyota'],
                ] as [$v, $n])
                    <div class="mk-vendor">
                        <div class="mk-vendor-mark">
                            <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="6" width="18" height="12" rx="2" />
                                <path d="M7 6 V3 M17 6 V3 M7 21 V18 M17 21 V18 M3 10 H1 M3 14 H1 M23 10 H21 M23 14 H21" />
                                <circle cx="12" cy="12" r="2" fill="currentColor" />
                            </svg>
                        </div>
                        <div>
                            <div class="mk-vendor-name mono">{{ $v }}</div>
                            <div class="mk-vendor-sub small t-mute">{{ $n }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

        {{-- =================== SHOWCASE =================== --}}
        <section class="mk-section">
            <div class="mk-section-head">
                <span class="mk-kicker">Recent work</span>
                <h2 class="mk-section-title">Files that left the queue this week.</h2>
                <p class="mk-section-sub">A small selection from the 412 tuned today — every one checksum-correct, dyno-validated, original retained.</p>
            </div>
            <div class="mk-showcase">
                @foreach ([
                    ['https://images.unsplash.com/photo-1503376780353-7e6692767b70?w=900&q=80', 'Porsche 911 GT3', 'Stage 1 · +38 hp'],
                    ['https://images.unsplash.com/photo-1568605117036-5fe5e7bab0b7?w=900&q=80', 'Audi R8 V10', 'Stage 2 · +62 hp'],
                    ['https://images.unsplash.com/photo-1494976388531-d1058494cdd8?w=900&q=80', 'Chevrolet Camaro', 'Custom remap'],
                    ['https://images.unsplash.com/photo-1580273916550-e323be2ae537?w=900&q=80', 'Mercedes-AMG C63', 'Stage 1 + DPF'],
                ] as [$src, $veh, $tag])
                    <figure class="mk-shot">
                        <img src="{{ $src }}" alt="{{ $veh }}" loading="lazy" />
                        <figcaption class="mk-shot-caption">
                            <span class="mk-shot-veh">{{ $veh }}</span>
                            <span class="mk-shot-tag">{{ $tag }}</span>
                        </figcaption>
                    </figure>
                @endforeach
            </div>
        </section>

        {{-- =================== PRICING =================== --}}
        <section id="pricing" class="mk-section">
            <div class="mk-section-head">
                <span class="mk-kicker">Pricing</span>
                <h2 class="mk-section-title">Credits, not commitments.</h2>
                <p class="mk-section-sub">Buy a pack, spend it as you go. Volume discount kicks in automatically.</p>
            </div>
            <div class="mk-tiers">
                @php
                    $tiers = [
                        ['Pro',   '£32',     '/ file · pay-as-you-go',  'For independent shops and serious enthusiasts.',  ['Stage 1 & 2 maps', 'DPF / EGR / AdBlue', '24-hour revision window', 'File retention 12 months'],                                       'Start free', false],
                        ['Trade', '£24',     '/ file · 50-pack',        'For workshops doing 30+ files a month.',          ['Everything in Pro', 'Trade-portal API + webhooks', 'Round-robin auto-assign', 'Priority queue · 30-min SLA', 'Dedicated tuner pool'], 'Open workshop account', true],
                        ['VIP',   'custom',  'dedicated tuners',        'For groups, dealer networks and white-label.',    ['Everything in Trade', 'Custom remaps & race maps', 'Named dyno engineer', 'On-site setup support', 'SLA 99.5%'],                       'Talk to sales', false],
                    ];
                @endphp
                @foreach ($tiers as [$plan, $price, $unit, $blurb, $features, $cta, $featured])
                    <div class="mk-tier {{ $featured ? 'mk-tier-featured' : '' }}">
                        @if ($featured) <div class="mk-tier-flag">Most popular</div> @endif
                        <div class="mk-tier-head">
                            <div class="mk-tier-plan">{{ $plan }}</div>
                            <div class="mk-tier-price"><span class="mk-tier-num">{{ $price }}</span><span class="mk-tier-unit">{{ $unit }}</span></div>
                            <p class="mk-tier-blurb">{{ $blurb }}</p>
                        </div>
                        <ul class="mk-tier-features">
                            @foreach ($features as $f)
                                <li>
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12 L10 17 L19 7"/></svg>
                                    <span>{{ $f }}</span>
                                </li>
                            @endforeach
                        </ul>
                        <a href="{{ route('register') }}" class="{{ $featured ? 'primary-btn primary-btn-lg' : 'ghost-btn ghost-btn-lg' }}" style="text-decoration:none">{{ $cta }}</a>
                    </div>
                @endforeach
            </div>
        </section>

        {{-- =================== CTA BAND =================== --}}
        <section id="tuners" class="mk-cta">
            <div class="mk-cta-inner">
                <div>
                    <h2 class="mk-cta-title">Tune for us.</h2>
                    <p class="mk-cta-sub">Looking for senior tuners with proven Bosch MED / EDC and modern Continental experience. Remote, paid per file, no on-call.</p>
                </div>
                <a href="{{ route('register') }}" class="primary-btn primary-btn-lg" style="text-decoration:none">Apply to the tuner network →</a>
            </div>
        </section>

        {{-- =================== FOOTER =================== --}}
        <footer class="mk-foot">
            <div class="mk-foot-inner">
                <div class="mk-foot-brand">
                    <span class="mk-brand-mark">
                        <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="9" /><path d="M12 3 V12 L18 15" />
                        </svg>
                    </span>
                    <span>tuningfiles</span>
                </div>
                <div class="mk-foot-cols">
                    <div><b>Product</b><a href="#how">How it works</a><a href="#pricing">Pricing</a><a href="#vehicles">Coverage</a></div>
                    <div><b>Tuners</b><a href="#tuners">Apply</a><a href="#">Tuner agreement</a><a href="#">Payouts</a></div>
                    <div><b>Company</b><a href="#">About</a><a href="#">Status · ok</a><a href="#">Contact</a></div>
                    <div><b>Legal</b><a href="#">Terms</a><a href="#">Privacy</a><a href="#">Refund policy</a></div>
                </div>
                <div class="mk-foot-bottom">
                    <span class="t-mute small">© {{ date('Y') }} tuningfiles ltd · Bristol, UK</span>
                    <span class="t-mute small mono">v 4.2.1 · all systems ok</span>
                </div>
            </div>
        </footer>
    </div>
</x-layouts.marketing>
