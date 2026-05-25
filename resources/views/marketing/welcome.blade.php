<x-layouts.marketing>
    <div class="mk">
        {{-- =================== NAV =================== --}}
        <header class="mk-nav" x-data="{ mobileOpen: false }" @keydown.escape.window="mobileOpen = false">
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
                    <a href="{{ route('vehicles') }}">Supported</a>
                    <a href="{{ route('results') }}">Results</a>
                    <a href="#pricing">Pricing</a>
                    <a href="{{ route('blog.index') }}">Blog</a>
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
                <button class="mk-hamburger" @click="mobileOpen = !mobileOpen" :aria-expanded="mobileOpen.toString()" aria-label="Toggle menu">
                    <svg x-show="!mobileOpen" viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/>
                    </svg>
                    <svg x-show="mobileOpen" viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" x-cloak>
                        <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                    </svg>
                </button>
            </div>

            <div class="mk-mobile-drawer"
                 x-show="mobileOpen"
                 x-transition:enter="mk-drawer-enter"
                 x-transition:enter-start="mk-drawer-enter-start"
                 x-transition:enter-end="mk-drawer-enter-end"
                 x-transition:leave="mk-drawer-leave"
                 x-transition:leave-start="mk-drawer-leave-start"
                 x-transition:leave-end="mk-drawer-leave-end"
                 @click.outside="mobileOpen = false"
                 x-cloak>
                <nav class="mk-mobile-links">
                    <a href="#how" @click="mobileOpen = false">How it works</a>
                    <a href="{{ route('vehicles') }}" @click="mobileOpen = false">Supported</a>
                    <a href="{{ route('results') }}" @click="mobileOpen = false">Results</a>
                    <a href="#pricing" @click="mobileOpen = false">Pricing</a>
                    <a href="{{ route('blog.index') }}" @click="mobileOpen = false">Blog</a>
                    <a href="#tuners" @click="mobileOpen = false">For tuners</a>
                </nav>
                <div class="mk-mobile-actions">
                    <x-theme-toggle />
                    @auth
                        <a href="{{ route('dashboard') }}" class="primary-btn primary-btn-lg" style="text-decoration:none" @click="mobileOpen = false">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="ghost-btn ghost-btn-lg" style="text-decoration:none" @click="mobileOpen = false">Sign in</a>
                        <a href="{{ route('register') }}" class="primary-btn primary-btn-lg" style="text-decoration:none" @click="mobileOpen = false">Get started</a>
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
                        <span class="mk-trust-sep"></span>
                        <em>30-day</em> guarantee
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

        {{-- =================== FEATURED MAKES CAROUSEL =================== --}}
        @php
            $featuredMakes = \App\Models\VehicleMake::where('is_active', true)
                ->whereNotNull('logo_url')
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get();
        @endphp
        @if ($featuredMakes->isNotEmpty())
            <section class="mk-carousel">
                <div class="mk-carousel-head">
                    <span class="mk-kicker">Vehicles we cover</span>
                    <h2 class="mk-carousel-title">{{ $featuredMakes->count() }} OEMs · 100+ models · every ECU we can read.</h2>
                    <a href="{{ route('vehicles') }}" class="ghost-btn ghost-btn-sm" style="text-decoration:none">Browse all →</a>
                </div>
                <div class="mk-marquee" aria-hidden="true">
                    <div class="mk-marquee-track">
                        @foreach ($featuredMakes->concat($featuredMakes) as $m)
                            <div class="mk-marquee-cell">
                                <img src="{{ $m->logo_url }}" alt="{{ $m->name }}" loading="lazy" />
                                <span>{{ $m->name }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

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
                    ['03', 'Get the tuned file', 'Delivered with a 24-hour free revision window and a 30-day credit-back guarantee. Original file retained. Not happy? Credits back, no questions asked.'],
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

        {{-- =================== RESULTS =================== --}}
        @php
            $latestResults = \App\Models\DynoResult::approved()
                ->with('user:id,name')
                ->orderByDesc('created_at')
                ->limit(4)
                ->get();
        @endphp
        <section class="mk-section">
            <div class="mk-section-head" style="display:flex; align-items:end; justify-content:space-between; gap:16px; flex-wrap:wrap">
                <div>
                    <span class="mk-kicker">Real results</span>
                    <h2 class="mk-section-title" style="margin-top:8px">Verified gains from real customers.</h2>
                </div>
                <a href="{{ route('results') }}" class="ghost-btn ghost-btn-sm" style="text-decoration:none">View all →</a>
            </div>
            @if ($latestResults->isNotEmpty())
                <div class="mk-showcase">
                    @foreach ($latestResults as $r)
                        <figure class="mk-shot">
                            @if ($r->image_url)
                                <img src="{{ $r->image_url }}" alt="{{ $r->vehicle_make }} {{ $r->vehicle_model }}" loading="lazy" />
                            @else
                                <div style="width:100%;height:100%;background:linear-gradient(135deg,var(--surface-2),var(--surface));display:grid;place-items:center">
                                    <span class="mono" style="font-size:18px;color:var(--muted)">{{ $r->vehicle_make }}</span>
                                </div>
                            @endif
                            <figcaption class="mk-shot-caption">
                                <span class="mk-shot-veh">{{ $r->vehicle_make }} {{ $r->vehicle_model }}</span>
                                <span class="mk-shot-tag">{{ $r->stock_hp }} → {{ $r->tuned_hp }} hp (+{{ $r->hpGain() }})</span>
                            </figcaption>
                        </figure>
                    @endforeach
                </div>
            @else
                {{-- Fallback to static showcase if no results yet --}}
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
            @endif
        </section>

        {{-- =================== LATEST BLOG =================== --}}
        @php
            $latestPosts = \App\Models\Post::published()->orderByDesc('published_at')->limit(3)->get();
        @endphp
        @if ($latestPosts->isNotEmpty())
            <section class="mk-section">
                <div class="mk-section-head" style="display:flex; align-items:end; justify-content:space-between; gap:16px; flex-wrap:wrap">
                    <div>
                        <span class="mk-kicker">From the blog</span>
                        <h2 class="mk-section-title" style="margin-top:8px">Latest reads.</h2>
                    </div>
                    <a href="{{ route('blog.index') }}" class="ghost-btn ghost-btn-sm" style="text-decoration:none">All posts →</a>
                </div>
                <div class="vb-grid">
                    @foreach ($latestPosts as $post)
                        <a href="{{ route('blog.show', $post) }}" class="vb-card vb-card-link" style="text-decoration:none; color:inherit">
                            <div class="vb-card-media">
                                @if ($post->cover_image)
                                    <img src="{{ $post->cover_image }}" alt="{{ $post->title }}" loading="lazy" />
                                @else
                                    <div class="vb-card-media-fallback"><span class="mono">Blog</span></div>
                                @endif
                            </div>
                            <div class="vb-card-body">
                                <div class="vb-card-make small mono">{{ optional($post->published_at)->format('j M Y') }}</div>
                                <h3 class="vb-card-model" style="font-size:17px">{{ $post->title }}</h3>
                                @if ($post->excerpt)
                                    <p class="vb-card-meta small t-mute" style="margin-top:4px; line-height:1.5">{{ Str::limit($post->excerpt, 110) }}</p>
                                @endif
                            </div>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif

        {{-- =================== REFERRAL =================== --}}
        <section class="mk-cta" style="margin:0 24px; border-radius:14px">
            <div class="mk-cta-inner">
                <div>
                    <h2 class="mk-cta-title">Refer a workshop, earn credits.</h2>
                    <p class="mk-cta-sub">Share your referral link — when they place their first order, you both get {{ \App\Models\SiteSetting::get('referral_credits_referrer', '10') }} free credits. No limits.</p>
                </div>
                <a href="{{ route('register') }}" class="primary-btn primary-btn-lg" style="text-decoration:none">Get your referral link →</a>
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
                        ['Pro',   'From £32', '/ file · pay-as-you-go',  'For independent shops and enthusiasts. No credit pack required.',  ['Stage 1 & 2 maps', 'DPF / EGR / AdBlue', '24-hour revision window', '30-day guarantee', 'Pay by card or bank transfer'],                                       'Start free', false],
                        ['Trade', '£24',     '/ file · 50-pack',        'For workshops doing 30+ files a month.',          ['Everything in Pro', 'Volume discount pricing', 'Invoice payments available', 'Priority queue · 30-min SLA', 'Dedicated tuner pool'], 'Open workshop account', true],
                        ['VIP',   'custom',  'white-label portal',        'For tuning businesses and dealer networks.',    ['Everything in Trade', 'Your own branded portal', 'Custom pricing for your customers', 'Subscription management', 'Custom domain support'],                       'Talk to sales', false],
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
            <div style="text-align:center; margin-top:24px">
                <p class="t-mute" style="font-size:14px; max-width:560px; margin:0 auto">No credit pack required — pay per file from your first tune. <b>Card, bank transfer, or invoice</b> accepted. <a href="{{ route('register') }}" style="color:var(--accent)">Try it now →</a></p>
            </div>
        </section>

        {{-- =================== GUARANTEE =================== --}}
        <section class="mk-section" style="text-align:center; max-width:800px; margin:0 auto; padding:60px 24px">
            <div style="display:inline-flex; align-items:center; gap:10px; padding:10px 18px; background:var(--success-soft); border:1px solid var(--success); border-radius:999px; margin-bottom:18px">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--success)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22 s8-4 8-10 V5 l-8-3-8 3 v7 c0 6 8 10 8 10z"/></svg>
                <span style="font-weight:600; color:var(--success); font-size:14px">30-day credit-back guarantee</span>
            </div>
            <h2 class="mk-section-title">Every file is guaranteed.</h2>
            <p class="mk-section-sub" style="max-width:600px; margin:0 auto">If your tune doesn't work and can't be fixed via our free 24-hour revision window, you get your credits back within 30 days — no questions asked. We also retain your original file so you can always roll back.</p>
        </section>

        {{-- =================== FOR TUNERS / SAAS =================== --}}
        <section id="tuners" class="mk-section">
            <div class="mk-section-head" style="text-align:center">
                <span class="mk-kicker">For tuners</span>
                <h2 class="mk-section-title">Start your own tuning business.</h2>
                <p class="mk-section-sub" style="max-width:600px; margin:0 auto">Run your own branded file service on our platform. Set your pricing, manage your customers, we handle the infrastructure. No setup fees, cancel anytime.</p>
            </div>
            @php $plans = \App\Models\SubscriptionPlan::active()->orderBy('sort_order')->get(); @endphp
            @if ($plans->isNotEmpty())
                <div class="mk-tiers">
                    @foreach ($plans as $plan)
                        <div class="mk-tier {{ $plan->slug === 'professional' ? 'mk-tier-featured' : '' }}">
                            @if ($plan->slug === 'professional') <div class="mk-tier-flag">Most popular</div> @endif
                            <div class="mk-tier-head">
                                <div class="mk-tier-plan">{{ $plan->name }}</div>
                                <div class="mk-tier-price">
                                    <span class="mk-tier-num">{{ $plan->price_pennies > 0 ? '£'.number_format($plan->price_pennies / 100) : 'Custom' }}</span>
                                    <span class="mk-tier-unit">{{ $plan->price_pennies > 0 ? '/ month' : 'contact us' }}</span>
                                </div>
                                <p class="mk-tier-blurb">{{ $plan->description }}</p>
                            </div>
                            <ul class="mk-tier-features">
                                @foreach ($plan->features ?? [] as $f)
                                    <li>
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12 L10 17 L19 7"/></svg>
                                        <span>{{ $f }}</span>
                                    </li>
                                @endforeach
                            </ul>
                            <a href="{{ route('register') }}" class="{{ $plan->slug === 'professional' ? 'primary-btn primary-btn-lg' : 'ghost-btn ghost-btn-lg' }}" style="text-decoration:none">Get started</a>
                        </div>
                    @endforeach
                </div>
            @endif
        </section>

        {{-- =================== FOOTER =================== --}}
        @include('marketing.partials.footer')
    </div>
</x-layouts.marketing>
