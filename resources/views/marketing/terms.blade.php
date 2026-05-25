<x-layouts.marketing>
    <div class="mk">
        @include('marketing.partials.nav')
        <section class="mk-section mk-section-narrow">
            <h1 class="mk-section-title">Terms of Service</h1>
            <article class="mk-prose">
                <p><em>Last updated: {{ date('j F Y') }}</em></p>
                <h2>1. Service</h2>
                <p>TuningFiles provides ECU tuning file modification services via an online platform. By using our service, you agree to these terms.</p>
                <h2>2. Accounts</h2>
                <p>You are responsible for maintaining the security of your account credentials. You must be at least 18 years old to use this service.</p>
                <h2>3. Credits & payments</h2>
                <p>Credits are non-transferable and non-refundable except under our 30-day guarantee policy. Prices are displayed in GBP and may change with notice.</p>
                <h2>4. Files & liability</h2>
                <p>Tuned files are provided for off-road / motorsport use only. TuningFiles is not responsible for damage resulting from improper use of modified ECU files. Users are responsible for ensuring compliance with local laws and regulations.</p>
                <h2>5. Guarantee</h2>
                <p>All files are covered by a 30-day credit-back guarantee. If a file does not function correctly and cannot be resolved via our free revision process, credits will be restored to your account.</p>
                <h2>6. Intellectual property</h2>
                <p>All tuned files remain the intellectual property of TuningFiles and the tuner who created them. You receive a licence to use the file on the specified vehicle only.</p>
            </article>
        </section>
        @include('marketing.partials.footer')
    </div>
</x-layouts.marketing>
