<?php

namespace App\Services;

class TuneEstimator
{
    /**
     * Best-effort estimate of tuned HP for stage 1 / 2 against stock.
     * Hardcoded multipliers tuned to mainstream petrol / diesel / hybrid figures.
     * Admin can override per variant once columns exist (out of scope for v1).
     */
    public static function estimate(?int $stockHp, ?string $fuel): array
    {
        if (! $stockHp) {
            return ['stage1' => null, 'stage2' => null, 'stage1Gain' => null, 'stage2Gain' => null];
        }

        $stage1Mul = 1.30;
        $stage2Mul = 1.50;

        if ($fuel === 'diesel') {
            $stage1Mul = 1.25;
            $stage2Mul = 1.40;
        }
        if ($fuel === 'hybrid' || $fuel === 'electric') {
            // Electric drivetrains: usually capped by hardware. Stage 1 only.
            $stage1Mul = 1.08;
            $stage2Mul = 1.08;
        }

        $stage1 = (int) round($stockHp * $stage1Mul / 5) * 5; // round to nearest 5
        $stage2 = (int) round($stockHp * $stage2Mul / 5) * 5;

        return [
            'stage1'     => $stage1,
            'stage2'     => $stage2,
            'stage1Gain' => $stage1 - $stockHp,
            'stage2Gain' => $stage2 - $stockHp,
        ];
    }
}
