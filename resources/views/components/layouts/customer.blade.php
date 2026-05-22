<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('partials.head')
</head>
<body>
    <div class="mk">
        <header class="mk-nav">
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
                </nav>
                <div class="mk-nav-actions">
                    <span class="t-mute small">{{ auth()->user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}">@csrf
                        <button type="submit" class="ghost-btn ghost-btn-sm">Sign out</button>
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
