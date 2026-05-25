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
                    <p>Create your account with {{ $tenant->business_name }} to start ordering tuning files.</p>
                </div>
            </div>

            <div class="auth-side-foot t-mute small mono">Powered by tuningfiles</div>
        </aside>

        <main class="auth-main">
            <div class="auth-card">
                <h1 class="auth-title">Create account</h1>
                <p class="auth-sub">Join {{ $tenant->business_name }} to get started.</p>

                @if ($errors->any())
                    <div class="mb-4 small" style="color:var(--danger)">
                        @foreach ($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <form method="POST" action="{{ route('tenant.register', $tenant) }}" class="auth-form">
                    @csrf

                    <label class="auth-field">
                        <span>Full name</span>
                        <input type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" />
                        @error('name')<span class="auth-hint" style="color:var(--danger)">{{ $message }}</span>@enderror
                    </label>

                    <label class="auth-field">
                        <span>Email</span>
                        <input type="email" name="email" value="{{ old('email') }}" required autocomplete="username" />
                        @error('email')<span class="auth-hint" style="color:var(--danger)">{{ $message }}</span>@enderror
                    </label>

                    <label class="auth-field">
                        <span>Password</span>
                        <input type="password" name="password" required autocomplete="new-password" />
                        <span class="auth-hint t-mute">Minimum 10 characters</span>
                        @error('password')<span class="auth-hint" style="color:var(--danger)">{{ $message }}</span>@enderror
                    </label>

                    <button type="submit" class="primary-btn primary-btn-lg auth-submit">Create account &rarr;</button>

                    <p class="auth-foot">
                        Already have an account? <a href="{{ route('tenant.login', $tenant) }}">Sign in</a>
                    </p>
                </form>
            </div>
        </main>
    </div>
    @livewireScripts
</body>
</html>
