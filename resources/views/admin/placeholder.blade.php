<x-layouts.admin>
    @php $name = ucfirst(str_replace('admin.', '', request()->route()->getName() ?? '')); @endphp
    <div class="page">
        <div class="page-head">
            <div>
                <h1 class="page-title">{{ $name }}</h1>
                <p class="page-sub">Coming later in the build.</p>
            </div>
        </div>
        <div class="card card-pad placeholder">
            <div class="placeholder-mark">
                <svg viewBox="0 0 24 24" width="28" height="28" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14 3 H6 a2 2 0 0 0 -2 2 v14 a2 2 0 0 0 2 2 h12 a2 2 0 0 0 2 -2 V9 z"/><path d="M14 3 V9 H20"/>
                </svg>
            </div>
            <div class="placeholder-title">{{ $name }} screen not built yet</div>
            <div class="t-mute small">The IA includes this route — it will be wired up in a later phase.</div>
        </div>
    </div>
</x-layouts.admin>
