<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('partials.head')
</head>
<body>
@php
    $route = request()->route()?->getName();
    $isActive = fn (string $name) => $route === $name;

    // dynamic sidebar counts
    $liveCount     = \App\Models\Order::whereIn('status', ['queued','in_progress','review','ready'])->count();
    $queueTotal    = \App\Models\Order::count();
    $filesCount    = \App\Models\OrderFile::count();
    $disputesCount = \App\Models\Dispute::where('status', 'open')->count();
    $customersK    = \App\Models\User::role('customer')->count();
    $tunersCount   = \App\Models\User::role('tuner')->count();

    $nav = [
        ['heading' => 'Operations', 'items' => [
            ['id' => 'admin.overview', 'label' => 'Overview',   'icon' => 'overview'],
            ['id' => 'admin.live',     'label' => 'Live queue', 'icon' => 'live',    'count' => $liveCount],
            ['id' => 'admin.queue',    'label' => 'Queue',      'icon' => 'queue',   'count' => $queueTotal],
            ['id' => 'admin.files',    'label' => 'All files',  'icon' => 'files',   'count' => $filesCount],
            ['id' => 'admin.disputes', 'label' => 'Disputes',   'icon' => 'disputes','count' => $disputesCount, 'dot' => $disputesCount > 0 ? 'err' : null],
            ['id' => 'admin.tickets',  'label' => 'Tickets',    'icon' => 'tickets'],
        ]],
        ['heading' => 'Directory', 'items' => [
            ['id' => 'admin.customers','label' => 'Customers',   'icon' => 'customers','count' => $customersK >= 1000 ? round($customersK / 1000, 1).'k' : $customersK],
            ['id' => 'admin.tuners',   'label' => 'Tuners',      'icon' => 'tuners',   'count' => $tunersCount],
            ['id' => 'admin.vehicles', 'label' => 'Vehicles DB', 'icon' => 'vehicles'],
        ]],
        ['heading' => 'Business', 'items' => [
            ['id' => 'admin.revenue',  'label' => 'Revenue', 'icon' => 'revenue'],
            ['id' => 'admin.credits',  'label' => 'Credits', 'icon' => 'credits'],
            ['id' => 'admin.reports',  'label' => 'Reports', 'icon' => 'reports'],
        ]],
        ['heading' => 'Content', 'items' => [
            ['id' => 'admin.blog', 'label' => 'Blog', 'icon' => 'reports'],
        ]],
        ['heading' => 'System', 'items' => [
            ['id' => 'admin.seo',      'label' => 'SEO',      'icon' => 'reports'],
            ['id' => 'admin.settings', 'label' => 'Settings', 'icon' => 'reports'],
        ]],
    ];

    $crumbLabels = [
        'admin.overview'  => ['Operations', 'Overview'],
        'admin.live'      => ['Operations', 'Live queue'],
        'admin.queue'     => ['Operations', 'Queue'],
        'admin.files'     => ['Operations', 'All files'],
        'admin.disputes'  => ['Operations', 'Disputes'],
        'admin.tickets'   => ['Operations', 'Tickets'],
        'admin.customers' => ['Directory', 'Customers'],
        'admin.tuners'    => ['Directory', 'Tuners'],
        'admin.vehicles'  => ['Directory', 'Vehicles DB'],
        'admin.revenue'   => ['Business', 'Revenue'],
        'admin.credits'   => ['Business', 'Credits'],
        'admin.reports'   => ['Business', 'Reports'],
        'admin.blog'      => ['Content',  'Blog'],
        'admin.seo'       => ['System',   'SEO'],
        'admin.settings'  => ['System',   'Settings'],
    ];
    [$crumbGroup, $crumbLeaf] = $crumbLabels[$route] ?? ['', ''];
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
                <div class="brand-name">tuningfiles</div>
                <div class="brand-env">admin · {{ app()->environment() }}</div>
            </div>
        </div>

        <button class="sidebar-search" type="button">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="6"/><path d="M16 16 L20 20"/></svg>
            <span>Search admin…</span>
            <kbd>⌘K</kbd>
        </button>

        <nav class="sidebar-nav">
            @foreach ($nav as $group)
                <div class="sidebar-group">
                    <div class="sidebar-group-label">{{ $group['heading'] }}</div>
                    @foreach ($group['items'] as $it)
                        <a href="{{ route($it['id']) }}"
                           class="sidebar-item {{ $isActive($it['id']) ? 'sidebar-item-active' : '' }}"
                           style="text-decoration:none">
                            <x-icon :name="$it['icon']" />
                            <span class="sidebar-item-label">{{ $it['label'] }}</span>
                            @isset($it['count'])
                                <span class="sidebar-count {{ ($it['dot'] ?? null) === 'err' ? 'sidebar-count-err' : '' }}">{{ $it['count'] }}</span>
                            @endisset
                        </a>
                    @endforeach
                </div>
            @endforeach
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
                <span class="crumb">Admin</span>
                @if ($crumbGroup)
                    <span class="crumb-sep">/</span>
                    <span class="crumb">{{ $crumbGroup }}</span>
                    <span class="crumb-sep">/</span>
                    <span class="crumb crumb-active">{{ $crumbLeaf }}</span>
                @endif
            </div>
            <div class="topbar-right">
                <span class="sys-pill"><span class="dot dot-ok"></span> all systems · ok</span>
                <span class="sys-pill sys-pill-mute">queue depth <b>{{ $queueTotal }}</b></span>
                <span class="sys-pill sys-pill-mute">env <b>{{ app()->environment() }}</b></span>
                <x-theme-toggle />
                <livewire:admin-notifications />
            </div>
        </header>

        {{ $slot }}
    </div>

    <livewire:order-drawer />
</div>
@livewireScripts
</body>
</html>
