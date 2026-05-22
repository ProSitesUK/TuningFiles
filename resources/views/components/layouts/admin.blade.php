<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('partials.head')
</head>
<body>
    <div class="app" data-sb="full">
        <aside class="sidebar">
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

            <nav class="sidebar-nav">
                <div class="sidebar-group">
                    <div class="sidebar-group-label">Operations</div>
                    @php $r = request()->route()->getName(); @endphp
                    <a href="{{ route('admin.overview') }}"  class="sidebar-item {{ $r === 'admin.overview' ? 'sidebar-item-active' : '' }}"><span class="sidebar-item-label">Overview</span></a>
                    <a href="{{ route('admin.live') }}"      class="sidebar-item {{ $r === 'admin.live' ? 'sidebar-item-active' : '' }}"><span class="sidebar-item-label">Live queue</span></a>
                    <a href="{{ route('admin.queue') }}"     class="sidebar-item {{ $r === 'admin.queue' ? 'sidebar-item-active' : '' }}"><span class="sidebar-item-label">Queue</span></a>
                    <a href="{{ route('admin.files') }}"     class="sidebar-item {{ $r === 'admin.files' ? 'sidebar-item-active' : '' }}"><span class="sidebar-item-label">All files</span></a>
                </div>
                <div class="sidebar-group">
                    <div class="sidebar-group-label">Directory</div>
                    <a href="{{ route('admin.customers') }}" class="sidebar-item {{ $r === 'admin.customers' ? 'sidebar-item-active' : '' }}"><span class="sidebar-item-label">Customers</span></a>
                    <a href="{{ route('admin.tuners') }}"    class="sidebar-item {{ $r === 'admin.tuners' ? 'sidebar-item-active' : '' }}"><span class="sidebar-item-label">Tuners</span></a>
                </div>
                <div class="sidebar-group">
                    <div class="sidebar-group-label">Business</div>
                    <a href="{{ route('admin.revenue') }}"   class="sidebar-item {{ $r === 'admin.revenue' ? 'sidebar-item-active' : '' }}"><span class="sidebar-item-label">Revenue</span></a>
                    <a href="{{ route('admin.credits') }}"   class="sidebar-item {{ $r === 'admin.credits' ? 'sidebar-item-active' : '' }}"><span class="sidebar-item-label">Credits</span></a>
                </div>
            </nav>

            <div class="sidebar-user">
                <span class="avatar avatar-accent" style="width:32px;height:32px;font-size:12px">{{ auth()->user()->initials() }}</span>
                <div class="sidebar-user-text">
                    <div class="sidebar-user-name">{{ auth()->user()->name }}</div>
                    <div class="sidebar-user-role">{{ auth()->user()->isAdmin() ? 'Operations · live' : (auth()->user()->isTuner() ? 'Tuner · live' : 'User') }}</div>
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
                <div class="crumbs">
                    <span class="crumb">Admin</span>
                    <span class="crumb-sep">/</span>
                    <span class="crumb crumb-active">{{ $crumb ?? ucfirst(str_replace('admin.', '', $r ?? '')) }}</span>
                </div>
                <div class="topbar-right">
                    <span class="sys-pill"><span class="dot dot-ok"></span> all systems · ok</span>
                    <span class="sys-pill sys-pill-mute">env <b>{{ app()->environment() }}</b></span>
                </div>
            </header>

            {{ $slot }}
        </div>
    </div>
    @livewireScripts
</body>
</html>
