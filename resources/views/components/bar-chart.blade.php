@props(['data', 'accentLast' => 2])

@php
    $arr = array_values($data);
    $max = max($arr) ?: 1;
    $n   = count($arr);
@endphp

<div class="bar-chart">
    @foreach ($arr as $i => $v)
        @php
            $h         = ($v / $max) * 100;
            $isAccent  = $i >= $n - $accentLast;
        @endphp
        <div class="bar-col">
            <div class="bar {{ $isAccent ? 'bar-accent' : '' }}" style="height: {{ $h }}%">
                <span class="bar-tip">{{ $v }}</span>
            </div>
        </div>
    @endforeach
</div>
