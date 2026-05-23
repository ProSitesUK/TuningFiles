<?php

namespace App\Support;

class OrderTag
{
    public static function label(?string $optionsLabel): string
    {
        $s = (string) $optionsLabel;
        return match (true) {
            str_contains($s, 'Stage 2') => 'Stage 2',
            str_contains($s, 'Stage 1') => 'Stage 1',
            str_contains($s, 'DPF')     => 'DPF',
            str_contains($s, 'Custom')  => 'Custom',
            str_contains($s, 'Pops')    => 'Pops',
            default                     => 'Tune',
        };
    }
}
