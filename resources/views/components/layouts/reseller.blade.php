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
        ['id' => 'reseller.settings',  'label' => 'Settings',   'icon' => 'reports'],
    ];

    $crumbLabels = [
        'reseller.dashboard' => 'Dashboard',
        'reseller.customers' => 'Customers',
        'reseller.invite'    => 'Invite customer',
        'reseller.orders'    => 'Orders',
        'reseller.settings'  => 'Settings',
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
            <span class="avatar avatar-accent" style="width:32px;height:32px;font-size:12px">{{ auth()->user()->initials() }}</span>
            <div class="sidebar-user-text">
                <div class="sidebar-user-name">{{ auth()->user()->name }}</div>
                <div class="sidebar-user-role">
                    <livewire:staff-status-toggle />
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">@csrf
                <button type="submit" class="signout-btn" title="Sign out">
                    <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M15 17 L20 12 L15 7"/><path d="M20 12 H9"/><path d="M9 21 H5 a2 2 0 0 1 -2 -2 V5 a2 2 0 0 1 2 -2 h4"/>
                    </svg>
                </button>
            </form>
        </div>
    </aside>

    <div class="main">
        <header class="topbar">
            <button class="admin-hamburger" @click="sidebarOpen = !sidebarOpen" aria-label="Toggle sidebar">
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
            </div>
        </header>

        {{ $slot }}
    </div>
</div>
@livewireScripts
</body>
</html>
