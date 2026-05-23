@props([
    'stock'  => \App\Support\Charts::STOCK_CURVE,
    'tuned'  => \App\Support\Charts::TUNED_CURVE,
    'width'  => 480,
    'height' => 200,
    'compact'=> false,
])

@php
    $n = count($stock);
    $max = max(array_merge($stock, $tuned)) + 20;
    $stepX = ($width - 30) / ($n - 1);
    $toY = fn ($v) => $height - 16 - ($v / $max) * ($height - 32);

    $stockPts = [];
    $tunedPts = [];
    foreach ($stock as $i => $v) {
        $stockPts[] = round(15 + $i * $stepX, 2).','.round($toY($v), 2);
    }
    foreach ($tuned as $i => $v) {
        $tunedPts[] = round(15 + $i * $stepX, 2).','.round($toY($v), 2);
    }
    $stockStr = implode(' ', $stockPts);
    $tunedStr = implode(' ', $tunedPts);
    $first = explode(',', $tunedPts[0])[0];
    $last  = explode(',', end($tunedPts))[0];
    $area  = "M{$first},".($height - 16)." L".str_replace(' ', ' L', $tunedStr)." L{$last},".($height - 16)." Z";
@endphp

<svg viewBox="0 0 {{ $width }} {{ $height }}" class="tune-svg" preserveAspectRatio="none">
    @foreach ([0.25, 0.5, 0.75] as $f)
        <line x1="15" x2="{{ $width - 15 }}" y1="{{ 16 + ($height - 32) * $f }}" y2="{{ 16 + ($height - 32) * $f }}"
              stroke="var(--border)" stroke-dasharray="2 3" />
    @endforeach
    <path d="{{ $area }}" fill="var(--accent)" opacity="0.12" />
    <polyline points="{{ $stockStr }}" fill="none" stroke="var(--muted)" stroke-width="1.6" stroke-dasharray="3 3" stroke-linecap="round" />
    <polyline points="{{ $tunedStr }}" fill="none" stroke="var(--accent)" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" />
    @unless ($compact)
        <text x="{{ $width - 60 }}" y="{{ $toY($tuned[$n - 2]) - 6 }}" class="tune-label tune-label-accent">tuned</text>
        <text x="{{ $width - 60 }}" y="{{ $toY($stock[$n - 2]) + 14 }}" class="tune-label">stock</text>
    @endunless
</svg>
