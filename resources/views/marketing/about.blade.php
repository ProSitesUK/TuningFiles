<x-layouts.marketing>
    <div class="mk">
        @include('marketing.partials.nav')
        <section class="mk-section mk-section-narrow">
            <div class="mk-section-head">
                <span class="mk-kicker">About</span>
                <h1 class="mk-section-title">Built for workshops, by a workshop.</h1>
                <p class="mk-section-sub">TuningFiles is a UK-based ECU tuning platform connecting workshops with vetted tuners. Every file is checksum-correct, dyno-validated, and backed by a 30-day credit-back guarantee.</p>
            </div>
            <article class="mk-prose">
                <h2>What we do</h2>
                <p>We provide professional ECU tuning files — stage 1, stage 2, custom remaps, DPF, EGR, and AdBlue solutions — delivered in minutes, not days. Our network of vetted tuners covers 30+ vehicle makes and 200+ ECU types.</p>
                <h2>For workshops</h2>
                <p>Upload your ECU read, pick your tune, and get a tested file back with a 24-hour free revision window. Pay per file or buy credit packs for volume discounts. Trade and VIP accounts get priority queue access and dedicated tuner pools.</p>
                <h2>For tuners</h2>
                <p>Run your own branded tuning file service on our platform. Set your pricing, manage your customers, and handle your own files — we provide the infrastructure, you provide the expertise. Plans start from £49/month.</p>
                <h2>Our guarantee</h2>
                <p>Every file comes with a 30-day credit-back guarantee. If it doesn't work and can't be fixed via our free revision window, you get your credits back — no questions asked.</p>
            </article>
        </section>
        @include('marketing.partials.footer')
    </div>
</x-layouts.marketing>
