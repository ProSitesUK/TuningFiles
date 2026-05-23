@props(['name', 'size' => 17, 'strokeWidth' => 1.6])

@php
    $paths = [
        'overview'  => '<path d="M3 12 L12 3 L21 12 M5 10 V20 H10 V14 H14 V20 H19 V10"/>',
        'queue'     => '<rect x="3" y="4" width="18" height="4" rx="1"/><rect x="3" y="10" width="18" height="4" rx="1"/><rect x="3" y="16" width="18" height="4" rx="1"/>',
        'live'      => '<circle cx="12" cy="12" r="9"/><path d="M12 7 V12 L15 14"/>',
        'files'     => '<path d="M14 3 H6 a2 2 0 0 0 -2 2 v14 a2 2 0 0 0 2 2 h12 a2 2 0 0 0 2 -2 V9 z"/><path d="M14 3 V9 H20"/>',
        'disputes'  => '<circle cx="12" cy="12" r="9"/><path d="M12 8 V13"/><circle cx="12" cy="16" r=".6" fill="currentColor"/>',
        'tickets'   => '<path d="M3 8 a2 2 0 0 1 2 -2 h14 a2 2 0 0 1 2 2 v2 a2 2 0 0 1 0 4 v2 a2 2 0 0 1 -2 2 H5 a2 2 0 0 1 -2 -2 v-2 a2 2 0 0 1 0 -4 z"/><path d="M10 6 V18" stroke-dasharray="2 2"/>',
        'customers' => '<circle cx="9" cy="9" r="3.5"/><path d="M3 19 c0 -3 3 -5.5 6 -5.5 s6 2.5 6 5.5"/><circle cx="16.5" cy="7.5" r="2.5"/><path d="M14.5 19 c0 -2.2 1.5 -4 4 -4.5"/>',
        'tuners'    => '<path d="M4 19 V11 M9 19 V7 M14 19 V13 M19 19 V9"/>',
        'vehicles'  => '<path d="M3 16 V13 l2 -5 h11 l3 5 v3"/><circle cx="7" cy="17" r="1.7"/><circle cx="16" cy="17" r="1.7"/>',
        'revenue'   => '<path d="M3 17 L9 11 L13 14 L21 6"/><path d="M15 6 H21 V12"/>',
        'credits'   => '<rect x="3" y="6" width="18" height="12" rx="2"/><path d="M3 10 H21"/>',
        'reports'   => '<rect x="4" y="3" width="16" height="18" rx="2"/><path d="M8 8 H16 M8 12 H16 M8 16 H13"/>',
        'search'    => '<circle cx="11" cy="11" r="6"/><path d="M16 16 L20 20"/>',
        'plus'      => '<path d="M12 5 V19 M5 12 H19"/>',
        'check'     => '<path d="M5 12 L10 17 L19 7"/>',
        'more'      => '<circle cx="6" cy="12" r="1.4" fill="currentColor"/><circle cx="12" cy="12" r="1.4" fill="currentColor"/><circle cx="18" cy="12" r="1.4" fill="currentColor"/>',
        'chevron'   => '<path d="M9 6 L15 12 L9 18"/>',
        'close'     => '<path d="M5 5 L19 19 M19 5 L5 19"/>',
        'download'  => '<path d="M12 4 V16 M7 11 L12 16 L17 11"/><path d="M5 20 H19"/>',
        'flag'      => '<path d="M5 3 V21"/><path d="M5 4 H17 L15 8 L17 12 H5"/>',
        'refund'    => '<path d="M21 12 a9 9 0 1 1 -3 -6.7"/><path d="M21 4 V10 H15"/>',
    ];
@endphp

<svg {{ $attributes }} width="{{ $size }}" height="{{ $size }}" viewBox="0 0 24 24" fill="none"
     stroke="currentColor" stroke-width="{{ $strokeWidth }}"
     stroke-linecap="round" stroke-linejoin="round">
    {!! $paths[$name] ?? '' !!}
</svg>
