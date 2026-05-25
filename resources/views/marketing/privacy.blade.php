<x-layouts.marketing>
    <div class="mk">
        @include('marketing.partials.nav')
        <section class="mk-section mk-section-narrow">
            <h1 class="mk-section-title">Privacy Policy</h1>
            <article class="mk-prose">
                <p><em>Last updated: {{ date('j F Y') }}</em></p>

                <h2>1. Data we collect</h2>
                <p>When you create an account we collect your name, email address, and company name. When you place an order we store the ECU file you upload, vehicle details, and payment information processed securely via Stripe. We also collect usage data such as IP addresses, browser type, and pages visited.</p>

                <h2>2. How we use it</h2>
                <p>Your data is used to provide and improve our tuning file service, process payments, communicate order updates, and send occasional product announcements (which you can opt out of at any time). We never sell your personal data to third parties.</p>

                <h2>3. Data retention</h2>
                <p>ECU files and order records are retained for the duration of your account plus 12 months. You can request deletion of your account and associated data at any time by contacting support.</p>

                <h2>4. Your rights</h2>
                <p>Under UK GDPR and the Data Protection Act 2018, you have the right to access, rectify, or erase your personal data. You may also request data portability or object to processing. To exercise these rights, email us at hello@tuningfiles.app.</p>

                <h2>5. Cookies</h2>
                <p>We use essential cookies for authentication and session management. We use analytics cookies (anonymised) to understand how the site is used. You can disable non-essential cookies in your browser settings.</p>

                <h2>6. Contact us</h2>
                <p>For privacy-related enquiries, contact us at hello@tuningfiles.app. Our data controller is TuningFiles Ltd, registered in England and Wales.</p>
            </article>
        </section>
        @include('marketing.partials.footer')
    </div>
</x-layouts.marketing>
