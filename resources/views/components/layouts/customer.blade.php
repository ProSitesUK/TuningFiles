<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('partials.head')
</head>
<body>
    <div class="mk">
        <header class="mk-nav" x-data="{ mobileOpen: false }" @keydown.escape.window="mobileOpen = false">
            <div class="mk-nav-inner">
                <a href="{{ route('app.dashboard') }}" class="mk-brand" style="text-decoration:none">
                    <span class="mk-brand-mark">
                        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="9" /><path d="M12 3 V12 L18 15" />
                        </svg>
                    </span>
                    <span>tuningfiles</span>
                </a>
                <nav class="mk-nav-links">
                    <a href="{{ route('app.dashboard') }}">Dashboard</a>
                    <a href="{{ route('app.orders.index') }}">Orders</a>
                    <a href="{{ route('app.credits') }}">Credits</a>
                    <a href="{{ route('app.tickets.index') }}">Support</a>
                </nav>
                <div class="mk-nav-actions">
                    <livewire:customer-notifications />
                    <x-theme-toggle />
                    <div class="topbar-user" x-data="{ userMenu: false }" @click.outside="userMenu = false">
                        <button class="topbar-avatar" @click="userMenu = !userMenu" type="button">
                            <span class="avatar avatar-accent" style="width:28px;height:28px;font-size:10px">{{ auth()->user()->initials() }}</span>
                        </button>
                        <div class="topbar-user-menu" x-show="userMenu" x-transition x-cloak>
                            <div class="topbar-user-info">
                                <strong>{{ auth()->user()->name }}</strong>
                                <span class="t-mute small">{{ auth()->user()->email }}</span>
                            </div>
                            <a href="{{ route('profile') }}" class="topbar-user-item" @click="userMenu = false">
                                <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21 v-2 a4 4 0 0 0 -4 -4 H8 a4 4 0 0 0 -4 4 v2"/><circle cx="12" cy="7" r="4"/></svg>
                                Profile & password
                            </a>
                            <form method="POST" action="{{ route('logout') }}">@csrf
                                <button type="submit" class="topbar-user-item topbar-user-logout">
                                    <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M15 17 L20 12 L15 7"/><path d="M20 12 H9"/><path d="M9 21 H5 a2 2 0 0 1 -2 -2 V5 a2 2 0 0 1 2 -2 h4"/></svg>
                                    Sign out
                                </button>
                            </form>
                        </div>
                    </div>
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
                    <a href="{{ route('app.dashboard') }}" @click="mobileOpen = false">Dashboard</a>
                    <a href="{{ route('app.orders.index') }}" @click="mobileOpen = false">Orders</a>
                    <a href="{{ route('app.orders.new') }}" @click="mobileOpen = false">New tune</a>
                    <a href="{{ route('app.credits') }}" @click="mobileOpen = false">Credits</a>
                    <a href="{{ route('app.tickets.index') }}" @click="mobileOpen = false">Support</a>
                    <a href="{{ route('app.referrals') }}" @click="mobileOpen = false">Referrals</a>
                    <a href="{{ route('profile') }}" @click="mobileOpen = false">Profile & password</a>
                </nav>
                <div class="mk-mobile-actions">
                    <x-theme-toggle />
                    <form method="POST" action="{{ route('logout') }}" style="flex:1">@csrf
                        <button type="submit" class="ghost-btn ghost-btn-lg" style="width:100%; text-align:center">Sign out</button>
                    </form>
                </div>
            </div>
        </header>

        <main style="max-width:1200px;margin:0 auto;padding:32px 24px 80px">
            {{ $slot }}
        </main>
    </div>
    @livewireScripts
</body>
</html>
