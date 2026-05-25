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
            <a href="{{ route('home') }}#how">How it works</a>
            <a href="{{ route('vehicles') }}">Supported</a>
            <a href="{{ route('results') }}">Results</a>
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
        <button class="mk-hamburger" @click.stop="mobileOpen = !mobileOpen" :aria-expanded="mobileOpen.toString()" aria-label="Toggle menu">
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
            <a href="{{ route('home') }}#how" @click="mobileOpen = false">How it works</a>
            <a href="{{ route('vehicles') }}" @click="mobileOpen = false">Supported</a>
            <a href="{{ route('results') }}" @click="mobileOpen = false">Results</a>
            <a href="{{ route('home') }}#pricing" @click="mobileOpen = false">Pricing</a>
            <a href="{{ route('blog.index') }}" @click="mobileOpen = false">Blog</a>
            <a href="{{ route('home') }}#tuners" @click="mobileOpen = false">For tuners</a>
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
