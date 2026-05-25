@props(['tenant'])
<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('partials.head')
    @if ($tenant->brand_color)
        <style>:root { --accent: {{ $tenant->brand_color }}; --accent-hover: {{ $tenant->brand_color }}dd; }</style>
    @endif
    <style>
        .mk-hamburger { display:none; background:none; border:none; cursor:pointer; padding:6px; color:var(--fg) }
        .mk-hamburger svg { display:block }
        @media (max-width: 768px) {
            .mk-nav-links, .mk-nav-actions { display:none !important }
            .mk-nav-links.open, .mk-nav-actions.open {
                display:flex !important;
                flex-direction:column;
                position:absolute;
                top:100%;
                left:0;
                right:0;
                background:var(--bg-card);
                border-bottom:1px solid var(--border);
                padding:12px 20px;
                gap:8px;
                z-index:100;
            }
            .mk-nav-actions.open { border-top:1px solid var(--border); }
            .mk-hamburger { display:block }
        }
    </style>
</head>
<body>
    <div class="mk">
        <header class="mk-nav" x-data="{ mobileOpen: false }">
            <div class="mk-nav-inner" style="position:relative">
                <a href="{{ route('tenant.dashboard', $tenant) }}" class="mk-brand" style="text-decoration:none">
                    @if ($tenant->logo_url)
                        <img src="{{ $tenant->logo_url }}" alt="{{ $tenant->business_name }}" style="height:24px;width:auto;object-fit:contain">
                    @else
                        <span class="mk-brand-mark">
                            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="9" /><path d="M12 3 V12 L18 15" />
                            </svg>
                        </span>
                    @endif
                    <span>{{ $tenant->business_name }}</span>
                </a>

                <button class="mk-hamburger" @click.stop="mobileOpen = !mobileOpen" aria-label="Toggle menu">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                        <template x-if="!mobileOpen"><g><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></g></template>
                        <template x-if="mobileOpen"><g><line x1="6" y1="6" x2="18" y2="18"/><line x1="6" y1="18" x2="18" y2="6"/></g></template>
                    </svg>
                </button>

                <nav class="mk-nav-links" :class="{ 'open': mobileOpen }">
                    <a href="{{ route('tenant.dashboard', $tenant) }}">Dashboard</a>
                    <a href="{{ route('tenant.orders', $tenant) }}">Orders</a>
                    <a href="{{ route('tenant.credits', $tenant) }}">Credits</a>
                    <a href="{{ route('tenant.tickets', $tenant) }}">Tickets</a>
                </nav>
                <div class="mk-nav-actions" :class="{ 'open': mobileOpen }">
                    <span class="t-mute small">{{ auth()->user()->name }}</span>
                    <x-theme-toggle />
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
