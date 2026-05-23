<footer class="mk-foot">
    <div class="mk-foot-inner">
        <div class="mk-foot-brand">
            <span class="mk-brand-mark">
                <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="9" /><path d="M12 3 V12 L18 15" />
                </svg>
            </span>
            <span>tuningfiles</span>
        </div>
        <div class="mk-foot-bottom">
            <span class="t-mute small">{{ \App\Models\SiteSetting::get('footer_company_line', '© '.date('Y').' tuningfiles ltd · Bristol, UK') }}</span>
            <span class="t-mute small mono">v 4.2.1 · all systems ok</span>
        </div>
    </div>
</footer>
