<div class="notif-wrap" x-data="{ open: false }" @click.outside="open = false">
    <button class="icon-btn" type="button" title="Notifications" @click="open = !open">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M6 16 V11 a6 6 0 0 1 12 0 v5 l2 2 H4 z"/><path d="M10 20 a2 2 0 0 0 4 0"/></svg>
        @if ($count > 0)
            <span class="bell-dot"></span>
        @endif
    </button>

    <div class="notif-dropdown" x-show="open" x-transition x-cloak>
        <div class="notif-head">
            <span>Notifications</span>
            <span class="t-mute mono">{{ $count }}</span>
        </div>

        @forelse ($items as $item)
            <a href="{{ $item['url'] }}" class="notif-item" @click="open = false">
                <span class="notif-icon"><x-icon :name="$item['icon']" size="14" /></span>
                <span class="notif-text">
                    <span>{{ $item['text'] }}</span>
                    <div class="notif-time">{{ $item['time']->diffForHumans(short: true) }}</div>
                </span>
            </a>
        @empty
            <div class="notif-empty">All clear — nothing to action.</div>
        @endforelse
    </div>
</div>
