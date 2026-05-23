<button type="button"
        class="icon-btn"
        title="Toggle dark mode"
        x-data="{
            dark: document.documentElement.dataset.theme === 'dark',
            flip() {
                this.dark = !this.dark;
                document.documentElement.dataset.theme = this.dark ? 'dark' : 'light';
                try { localStorage.setItem('theme', this.dark ? 'dark' : 'light'); } catch (e) {}
            }
        }"
        @click="flip()">
    <svg x-show="!dark" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
        <circle cx="12" cy="12" r="4"/>
        <path d="M12 2 V4 M12 20 V22 M4 12 H2 M22 12 H20 M5.6 5.6 L4.2 4.2 M19.8 19.8 L18.4 18.4 M5.6 18.4 L4.2 19.8 M19.8 4.2 L18.4 5.6"/>
    </svg>
    <svg x-show="dark" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" x-cloak>
        <path d="M21 13 A9 9 0 1 1 11 3 a7 7 0 0 0 10 10 z"/>
    </svg>
</button>
