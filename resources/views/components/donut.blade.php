@props(['slices', 'size' => 140, 'thickness' => 18])

@php
    $r = ($size - $thickness) / 2;
    $c = 2 * M_PI * $r;
    $total = array_sum(array_column($slices, 'value'));
    if ($total <= 0) $total = 1;
@endphp

<svg width="{{ $size }}" height="{{ $size }}" viewBox="0 0 {{ $size }} {{ $size }}" style="display:block">
    <circle cx="{{ $size / 2 }}" cy="{{ $size / 2 }}" r="{{ $r }}" fill="none" stroke="var(--border)" stroke-width="{{ $thickness }}" />
    @php $offset = 0; @endphp
    @foreach ($slices as $slice)
        @php
            $frac = $slice['value'] / $total;
            $dash = $frac * $c;
        @endphp
        <circle cx="{{ $size / 2 }}" cy="{{ $size / 2 }}" r="{{ $r }}" fill="none"
                stroke="{{ $slice['color'] }}" stroke-width="{{ $thickness }}"
                stroke-dasharray="{{ $dash }} {{ $c - $dash }}"
                stroke-dashoffset="{{ -$offset }}"
                transform="rotate(-90 {{ $size / 2 }} {{ $size / 2 }})" />
        @php $offset += $dash; @endphp
    @endforeach
</svg>
