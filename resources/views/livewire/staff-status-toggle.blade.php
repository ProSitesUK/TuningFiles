<div class="staff-status" x-data="{ open: false }" @click.outside="open = false">
    <button type="button" @click="open = !open" class="staff-status-btn">
        <span class="dot dot-{{ auth()->user()->statusDot() }}"></span>
        <span>{{ auth()->user()->statusLabel() }}</span>
        <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9 l6 6 6 -6"/></svg>
    </button>
    <div class="staff-status-menu" x-show="open" x-transition x-cloak>
        @foreach ([
            ['online',  'ok',   'Online'],
            ['away',    'warn', 'Away'],
            ['busy',    'warn', 'Busy'],
            ['holiday', 'warn', 'Holiday'],
            ['off',     'mute', 'Offline'],
        ] as [$val, $dot, $label])
            <button type="button" wire:click="setStatus('{{ $val }}')" @click="open = false"
                    class="staff-status-opt {{ $status === $val ? 'staff-status-opt-on' : '' }}">
                <span class="dot dot-{{ $dot }}"></span>
                <span>{{ $label }}</span>
            </button>
        @endforeach
    </div>
</div>
