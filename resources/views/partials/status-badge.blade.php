<?php
$map = [
    'queued'      => ['neutral', 'queued'],
    'in_progress' => ['amber',   'in progress'],
    'review'      => ['red',     'review'],
    'ready'       => ['green',   'ready'],
    'delivered'   => ['green',   'delivered'],
    'refunded'    => ['neutral', 'refunded'],
    'dispute'     => ['red',     'dispute'],
    'failed'      => ['red',     'failed'],
];
[$kind, $label] = $map[$status] ?? ['neutral', $status];
?>
<span class="badge badge-{{ $kind }}"><span class="badge-dot"></span>{{ $label }}</span>
