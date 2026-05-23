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
            <a href="{{ route('home') }}#how">How it works</a>
            <a href="{{ route('vehicles') }}">Supported</a>
            <a href="{{ route('home') }}#pricing">Pricing</a>
            <a href="{{ route('blog.index') }}">Blog</a>
            <a href="{{ route('home') }}#tuners">For tuners</a>
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
