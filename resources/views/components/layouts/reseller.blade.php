<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('partials.head')
</head>
<body>
@php
    $route = request()->route()?->getName();
    $isActive = fn (string $name) => $route === $name;

    $rProfile = auth()->user()->resellerProfile;
    $brandName = $rProfile?->business_name ?? 'Reseller';

    $subCount   = auth()->user()->subCustomers()->count();
    $orderCount = \App\Models\Order::where('reseller_id', auth()->id())->count();

    $nav = [
        ['id' => 'reseller.dashboard', 'label' => 'Dashboard',  'icon' => 'overview'],
        ['id' => 'reseller.customers', 'label' => 'Customers',  'icon' => 'customers', 'count' => $subCount],
        ['id' => 'reseller.orders',    'label' => 'Orders',     'icon' => 'queue',     'count' => $orderCount],
        ['id' => 'reseller.pricing',   'label' => 'Pricing',    'icon' => 'credits'],
        ['id' => 'reseller.billing',   'label' => 'Billing',    'icon' => 'revenue'],
        ['id' => 'reseller.settings',  'label' => 'Settings',   'icon' => 'reports'],
    ];

    $crumbLabels = [
        'reseller.dashboard'    => 'Dashboard',
        'reseller.customers'    => 'Customers',
        'reseller.invite'       => 'Invite customer',
        'reseller.orders'       => 'Orders',
        'reseller.orders.show'  => 'Order detail',
        'reseller.pricing'      => 'Pricing',
        'reseller.billing'      => 'Billing',
        'reseller.plans'        => 'Plans',
        'reseller.settings'     => 'Settings',
    ];
    $crumbLeaf = $crumbLabels[$route] ?? '';
@endphp

<div class="app" data-sb="full" x-data="{ sidebarOpen: false }" @keydown.escape.window="sidebarOpen = false">
    <div class="sidebar-scrim" :class="sidebarOpen && 'sidebar-scrim-on'" @click="sidebarOpen = false"></div>
    <aside class="sidebar" :class="sidebarOpen && 'sidebar-open'">
        <div class="sidebar-brand">
            <div class="brand-mark">
                <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="9" /><path d="M12 3 V12 L18 15" />
                </svg>
            </div>
            <div class="brand-text">
                <div class="brand-name">{{ $brandName }}</div>
                <div class="brand-env">reseller portal</div>
            </div>
        </div>

        <nav class="sidebar-nav">
            <div class="sidebar-group">
                <div class="sidebar-group-label">Portal</div>
                @foreach ($nav as $it)
                    <a href="{{ route($it['id']) }}"
                       class="sidebar-item {{ $isActive($it['id']) ? 'sidebar-item-active' : '' }}"
                       style="text-decoration:none">
                        <x-icon :name="$it['icon']" />
                        <span class="sidebar-item-label">{{ $it['label'] }}</span>
                        @isset($it['count'])
                            <span class="sidebar-count">{{ $it['count'] }}</span>
                        @endisset
                    </a>
                @endforeach
            </div>
        </nav>

        <div class="sidebar-user">
            <a href="{{ route('profile') }}" class="sidebar-user-link" style="text-decoration:none; color:inherit">
                <span class="avatar avatar-accent" style="width:32px;height:32px;font-size:12px">{{ auth()->user()->initials() }}</span>
                <div class="sidebar-user-text">
                    <div class="sidebar-user-name">{{ auth()->user()->name }}</div>
                    <div class="sidebar-user-role">
                        <livewire:staff-status-toggle />
                    </div>
                </div>
            </a>
            <div class="sidebar-user-actions">
                <a href="{{ route('profile') }}" class="sidebar-action-btn" title="Profile" style="text-decoration:none">
                    <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 21 v-2 a4 4 0 0 0 -4 -4 H8 a4 4 0 0 0 -4 4 v2"/><circle cx="12" cy="7" r="4"/>
                    </svg>
                </a>
                <form method="POST" action="{{ route('logout') }}">@csrf
                    <button type="submit" class="sidebar-action-btn sidebar-action-logout" title="Sign out">
                        <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M15 17 L20 12 L15 7"/><path d="M20 12 H9"/><path d="M9 21 H5 a2 2 0 0 1 -2 -2 V5 a2 2 0 0 1 2 -2 h4"/>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <div class="main">
        <header class="topbar">
            <button class="admin-hamburger" @click.stop="sidebarOpen = !sidebarOpen" aria-label="Toggle sidebar">
                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/>
                </svg>
            </button>
            <div class="crumbs">
                <span class="crumb">Reseller</span>
                @if ($crumbLeaf)
                    <span class="crumb-sep">/</span>
                    <span class="crumb crumb-active">{{ $crumbLeaf }}</span>
                @endif
            </div>
            <div class="topbar-right">
                <x-theme-toggle />
                <livewire:admin-notifications />
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
        </header>

        {{ $slot }}
    </div>
</div>
@livewireScripts
</body>
</html>
