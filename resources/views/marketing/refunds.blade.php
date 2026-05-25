<x-layouts.marketing>
    <div class="mk">
        @include('marketing.partials.nav')
        <section class="mk-section mk-section-narrow">
            <h1 class="mk-section-title">Refund Policy</h1>
            <article class="mk-prose">
                <p><em>Last updated: {{ date('j F Y') }}</em></p>

                <h2>30-day credit-back guarantee</h2>
                <p>Every tuning file delivered through TuningFiles is backed by our 30-day credit-back guarantee. If the file does not work correctly on the specified vehicle and cannot be resolved through our revision process, we will credit your account in full.</p>

                <h2>How to claim</h2>
                <p>Open a support ticket from your dashboard within 30 days of file delivery. Include a description of the issue and any relevant data logs or diagnostic screenshots. Our team will review the file and, if a revision cannot resolve the problem, restore your credits.</p>

                <h2>What's covered</h2>
                <ul>
                    <li>Files that do not function correctly on the specified vehicle and ECU</li>
                    <li>Files that cause check-engine lights or limp mode not present in the original</li>
                    <li>Files where the requested tuning options were not correctly applied</li>
                </ul>

                <h2>What's not covered</h2>
                <ul>
                    <li>Issues caused by incorrect ECU reads or hardware faults</li>
                    <li>Subjective dissatisfaction with power gains (within normal variance)</li>
                    <li>Files used on a vehicle or ECU other than the one specified in the order</li>
                    <li>Damage caused by third-party modifications or incorrect installation</li>
                </ul>

                <h2>Credit expiry</h2>
                <p>Credits restored under the guarantee do not expire and can be used on any future order. Credits are non-transferable between accounts.</p>

                <h2>Contact for disputes</h2>
                <p>If you believe a guarantee claim was incorrectly denied, email hello@tuningfiles.app with your order number and we will escalate the review.</p>
            </article>
        </section>
        @include('marketing.partials.footer')
    </div>
</x-layouts.marketing>
