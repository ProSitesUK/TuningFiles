<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('partials.head')
</head>
<body>
    @php
        $isLogin    = request()->routeIs('login');
        $isRegister = request()->routeIs('register');
    @endphp
    <div class="auth">
        <aside class="auth-side">
            <a href="{{ route('home') }}" class="mk-brand mk-brand-light" style="text-decoration:none">
                <span class="mk-brand-mark">
                    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="9"/><path d="M12 3 V12 L18 15"/>
                    </svg>
                </span>
                <span>tuningfiles</span>
            </a>

            <div class="auth-side-body">
                <div class="auth-quote">
                    <p>
                        “We switched off our in-house tuner and moved everything to tuningfiles
                        in a weekend. Files come back in 12 minutes, never had a comeback.”
                    </p>
                    <div class="auth-quote-by">
                        <span class="avatar avatar-accent" style="width:32px;height:32px;font-size:12px">HK</span>
                        <div>
                            <div class="auth-quote-name">Hae-Jin Kim</div>
                            <div class="t-mute small">Tunehouse Seoul · Trade plan · 62 files / mo</div>
                        </div>
                    </div>
                </div>

                <div class="auth-side-stats">
                    <div><b>14m</b><span>avg turnaround</span></div>
                    <div><b>98.2%</b><span>SLA hit</span></div>
                    <div><b>1,412</b><span>workshops</span></div>
                </div>
            </div>

            <div class="auth-side-foot t-mute small mono">© {{ date('Y') }} tuningfiles · all systems ok</div>
        </aside>

        <main class="auth-main">
            <a href="{{ route('home') }}" class="auth-back" style="text-decoration:none">← Back to home</a>
            <div class="auth-card">
                {{ $slot }}
            </div>
        </main>
    </div>
    @livewireScripts
</body>
</html>
