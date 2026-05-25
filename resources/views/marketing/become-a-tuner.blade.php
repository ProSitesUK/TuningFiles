<x-layouts.marketing>
    <div class="mk">
        {{-- =================== NAV =================== --}}
        @include('marketing.partials.nav')

        {{-- =================== HERO =================== --}}
        <section class="mk-hero" style="padding-bottom:40px">
            <div class="mk-hero-inner" style="max-width:720px">
                <div class="mk-hero-eyebrow">
                    <span class="dot dot-ok"></span> No setup fees · Cancel anytime · 14-day free trial
                </div>
                <h1 class="mk-hero-title">
                    Start your tuning business.<br/>
                    <span class="mk-accent">We handle the platform.</span>
                </h1>
                <p class="mk-hero-sub">
                    Run your own branded file service on tuningfiles. Set your pricing, manage your customers, keep your brand front and centre. Infrastructure, payments, and file delivery are on us.
                </p>
                <div class="mk-hero-actions">
                    <a href="#signup" class="primary-btn primary-btn-lg" style="text-decoration:none">Create your account</a>
                </div>
            </div>
        </section>

        {{-- =================== BENEFITS =================== --}}
        <section class="mk-section">
            <div class="mk-section-head" style="text-align:center">
                <span class="mk-kicker">Why tuningfiles</span>
                <h2 class="mk-section-title">Everything you need to run a tuning business.</h2>
            </div>
            <div class="mk-steps">
                <div class="mk-step">
                    <div class="mk-step-n mono">
                        <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="3" width="18" height="18" rx="3" /><path d="M9 12 h6 M12 9 v6" />
                        </svg>
                    </div>
                    <h3 class="mk-step-t">Your own branded portal</h3>
                    <p class="mk-step-body">Your customers see your brand, your logo, your domain. They sign up at your URL, place orders through your interface, and never see ours.</p>
                </div>
                <div class="mk-step">
                    <div class="mk-step-n mono">
                        <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="9" /><path d="M12 7 v5 l3 3" />
                        </svg>
                    </div>
                    <h3 class="mk-step-t">Set your own pricing</h3>
                    <p class="mk-step-body">Choose your own credit pricing and markups. You control what your customers pay. Buy credits wholesale, sell at your rate, keep the margin.</p>
                </div>
                <div class="mk-step">
                    <div class="mk-step-n mono">
                        <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 22 s8-4 8-10 V5 l-8-3-8 3 v7 c0 6 8 10 8 10z" />
                        </svg>
                    </div>
                    <h3 class="mk-step-t">We handle the infrastructure</h3>
                    <p class="mk-step-body">File delivery, SLA monitoring, payment processing, customer support tooling, and QA pipelines. You focus on tuning, we handle the rest.</p>
                </div>
            </div>
        </section>

        {{-- =================== PLANS =================== --}}
        @if ($plans->isNotEmpty())
            <section class="mk-section mk-section-alt">
                <div class="mk-section-head" style="text-align:center">
                    <span class="mk-kicker">Plans</span>
                    <h2 class="mk-section-title">Pick the plan that fits your business.</h2>
                    <p class="mk-section-sub" style="max-width:560px; margin:0 auto">Start with a 14-day free trial on any plan. No credit card required to sign up.</p>
                </div>
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
                            <a href="#signup" class="{{ $plan->slug === 'professional' ? 'primary-btn primary-btn-lg' : 'ghost-btn ghost-btn-lg' }}" style="text-decoration:none">Get started</a>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

        {{-- =================== SIGNUP FORM =================== --}}
        <section id="signup" class="mk-section" style="max-width:520px; margin:0 auto; padding:60px 24px">
            <div style="text-align:center; margin-bottom:32px">
                <h2 class="mk-section-title">Create your tuner account</h2>
                <p class="mk-section-sub">Takes about 30 seconds. Pick a plan after signup.</p>
            </div>

            @if ($errors->any())
                <div style="padding:12px 16px; background:var(--danger-soft, rgba(220,38,38,.08)); border:1px solid var(--danger); border-radius:8px; margin-bottom:20px; font-size:13px; color:var(--danger)">
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('tuner.signup') }}" class="auth-form">
                @csrf

                <label class="auth-field">
                    <span>Business name</span>
                    <input type="text" name="business_name" value="{{ old('business_name') }}" placeholder="Bristol Performance Tuning" required autofocus />
                    <span class="auth-hint t-mute">This will be your portal name. You can change it later.</span>
                </label>

                <label class="auth-field">
                    <span>Your name</span>
                    <input type="text" name="name" value="{{ old('name') }}" placeholder="Sam Okafor" required />
                </label>

                <label class="auth-field">
                    <span>Email</span>
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="you@yourbusiness.com" required />
                </label>

                <label class="auth-field">
                    <span>Password</span>
                    <input type="password" name="password" placeholder="At least 10 characters" required minlength="10" />
                    <span class="auth-hint t-mute">Use a strong passphrase you don't use elsewhere.</span>
                </label>

                <label class="auth-field">
                    <span>Website <span class="t-mute">(optional)</span></span>
                    <input type="url" name="website" value="{{ old('website') }}" placeholder="https://yourbusiness.com" />
                </label>

                <button type="submit" class="primary-btn primary-btn-lg auth-submit" style="width:100%">Create account & pick a plan</button>

                <div style="text-align:center; margin-top:16px">
                    <p class="t-mute small" style="margin:0">No setup fees · Cancel anytime · 14-day free trial</p>
                </div>

                <p class="auth-foot" style="text-align:center">
                    Already have an account? <a href="{{ route('login') }}">Sign in</a>
                </p>
            </form>
        </section>

        {{-- =================== FOOTER =================== --}}
        @include('marketing.partials.footer')
    </div>
</x-layouts.marketing>
