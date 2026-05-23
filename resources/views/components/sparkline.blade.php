@props(['data', 'color' => 'currentColor', 'width' => 320, 'height' => 36, 'fill' => false, 'bars' => false])

@php
    $arr = array_values($data);
    if (empty($arr)) { return; }
    $min = min($arr); $max = max($arr);
    $range = $max - $min ?: 1;
    $count = count($arr);
    $stepX = $width / ($count - ($bars ? 0 : 1));

    if ($bars) {
        $bw = $stepX * 0.62;
    } else {
        $pts = [];
        foreach ($arr as $i => $v) {
            $x = $i * $stepX;
            $y = $height - (($v - $min) / $range) * ($height - 4) - 2;
            $pts[] = round($x, 2).','.round($y, 2);
        }
        $pointsStr = implode(' ', $pts);
        $areaStr = '';
        if ($fill) {
            $first = explode(',', $pts[0])[0];
            $last  = explode(',', end($pts))[0];
            $areaStr = "M{$first},{$height} L".str_replace(' ', ' L', $pointsStr)." L{$last},{$height} Z";
        }
    }
@endphp

<svg viewBox="0 0 {{ $width }} {{ $height }}" class="spark" preserveAspectRatio="none">
    @if ($bars)
        @foreach ($arr as $i => $v)
            @php
                $h = (($v - $min) / $range) * ($height - 4) + 3;
                $x = round($i * $stepX + ($stepX - $bw) / 2, 2);
                $y = round($height - $h, 2);
            @endphp
            <rect x="{{ $x }}" y="{{ $y }}" width="{{ round($bw, 2) }}" height="{{ round($h, 2) }}" rx="1.2" fill="{{ $color }}" />
        @endforeach
    @else
        @if ($fill)
            <path d="{{ $areaStr }}" fill="{{ $color }}" opacity="0.12" />
        @endif
        <polyline points="{{ $pointsStr }}" fill="none" stroke="{{ $color }}" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" />
    @endif
</svg>
