<x-layouts.marketing>
    <div class="mk">
        @include('marketing.partials.nav')
        <section class="mk-section mk-section-narrow">
            <div class="mk-section-head">
                <span class="mk-kicker">Contact</span>
                <h1 class="mk-section-title">Get in touch.</h1>
                <p class="mk-section-sub">Need help with an order? Log in and open a support ticket for the fastest response. For general enquiries, reach us below.</p>
            </div>
            <div class="card card-pad" style="max-width:600px">
                <div class="va-form-title">General enquiries</div>
                <p style="margin:8px 0 16px; font-size:15px">Email us at <a href="mailto:{{ \App\Models\SiteSetting::get('site_name', 'hello') }}@tuningfiles.app" style="color:var(--accent)">hello@tuningfiles.app</a></p>
                <div class="va-form-title" style="margin-top:18px">Existing customers</div>
                <p style="margin:8px 0 0; font-size:15px">
                    @auth
                        <a href="{{ route('app.tickets.new') }}" class="primary-btn primary-btn-sm" style="text-decoration:none">Open a support ticket</a>
                    @else
                        <a href="{{ route('login') }}" style="color:var(--accent)">Sign in</a> to open a support ticket for the fastest response.
                    @endauth
                </p>
            </div>
        </section>
        @include('marketing.partials.footer')
    </div>
</x-layouts.marketing>
