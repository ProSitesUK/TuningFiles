<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('partials.head')
    @if ($tenant->brand_color)
        <style>:root { --accent: {{ $tenant->brand_color }}; --accent-hover: {{ $tenant->brand_color }}dd; }</style>
    @endif
</head>
<body>
    <div class="auth">
        <aside class="auth-side">
            <a href="{{ route('tenant.login', $tenant) }}" class="mk-brand mk-brand-light" style="text-decoration:none">
                @if ($tenant->logo_url)
                    <img src="{{ $tenant->logo_url }}" alt="{{ $tenant->business_name }}" style="height:24px;width:auto;object-fit:contain">
                @else
                    <span class="mk-brand-mark">
                        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="9"/><path d="M12 3 V12 L18 15"/>
                        </svg>
                    </span>
                @endif
                <span>{{ $tenant->business_name }}</span>
            </a>

            <div class="auth-side-body">
                <div class="auth-quote">
                    <p>Welcome to the {{ $tenant->business_name }} tuning portal. Sign in to manage your orders and files.</p>
                </div>
            </div>

            <div class="auth-side-foot t-mute small mono">Powered by tuningfiles</div>
        </aside>

        <main class="auth-main">
            <div class="auth-card">
                <h1 class="auth-title">Welcome back</h1>
                <p class="auth-sub">Sign in to your {{ $tenant->business_name }} account.</p>

                @if ($errors->any())
                    <div class="mb-4 small" style="color:var(--danger)">
                        @foreach ($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <form method="POST" action="{{ route('tenant.login', $tenant) }}" class="auth-form">
                    @csrf

                    <label class="auth-field">
                        <span>Email</span>
                        <input type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" />
                    </label>

                    <label class="auth-field">
                        <span>Password</span>
                        <input type="password" name="password" required autocomplete="current-password" />
                    </label>

                    <label class="auth-check">
                        <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }} />
                        <span>Keep me signed in for 30 days</span>
                    </label>

                    <button type="submit" class="primary-btn primary-btn-lg auth-submit">Sign in &rarr;</button>

                    @if ($tenant->canAddCustomer())
                        <p class="auth-foot">
                            New here? <a href="{{ route('tenant.register', $tenant) }}">Create an account</a>
                        </p>
                    @endif
                </form>
            </div>
        </main>
    </div>
    @livewireScripts
</body>
</html>
