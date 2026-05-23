<x-layouts.marketing>
    <div class="mk">
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

        <section class="mk-hero">
            <div class="mk-hero-inner">
                <div class="mk-hero-eyebrow"><span class="dot dot-ok"></span> Foundation in place · Phase 2 brings the full landing</div>
                <h1 class="mk-hero-title">Professional ECU files.<br/><span class="mk-accent">Delivered in minutes,</span> not days.</h1>
                <p class="mk-hero-sub">Stage 1 to full custom remaps from a network of vetted tuners. Upload your read, get a tested file back — checksum-correct, dyno-validated, original retained.</p>
                <div class="mk-hero-actions">
                    <a href="{{ route('register') }}" class="primary-btn primary-btn-lg" style="text-decoration:none">Open a workshop account</a>
                    <a href="{{ route('login') }}" class="ghost-btn ghost-btn-lg" style="text-decoration:none">Sign in</a>
                </div>
            </div>
        </section>
    </div>
</x-layouts.marketing>
