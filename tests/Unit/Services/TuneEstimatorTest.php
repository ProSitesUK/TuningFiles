<?php

namespace Tests\Unit\Services;

use App\Services\TuneEstimator;
use PHPUnit\Framework\TestCase;

class TuneEstimatorTest extends TestCase
{
    public function test_petrol_stage_1_adds_30_percent(): void
    {
        $result = TuneEstimator::estimate(300, 'petrol');

        $this->assertEquals(390, $result['stage1']);
    }

    public function test_petrol_stage_2_adds_50_percent(): void
    {
        $result = TuneEstimator::estimate(300, 'petrol');

        $this->assertEquals(450, $result['stage2']);
    }

    public function test_diesel_stage_1_adds_25_percent(): void
    {
        $result = TuneEstimator::estimate(200, 'diesel');

        $this->assertEquals(250, $result['stage1']);
    }

    public function test_diesel_stage_2_adds_40_percent(): void
    {
        $result = TuneEstimator::estimate(200, 'diesel');

        $this->assertEquals(280, $result['stage2']);
    }

    public function test_electric_minimal_gains(): void
    {
        $result = TuneEstimator::estimate(300, 'electric');

        $this->assertEquals(325, $result['stage1']);
    }

    public function test_null_stock_hp_returns_nulls(): void
    {
        $result = TuneEstimator::estimate(null, 'petrol');

        $this->assertNull($result['stage1']);
        $this->assertNull($result['stage2']);
        $this->assertNull($result['stage1Gain']);
        $this->assertNull($result['stage2Gain']);
    }

    public function test_rounds_to_nearest_5(): void
    {
        $result = TuneEstimator::estimate(192, 'petrol');

        // 192 * 1.30 = 249.6, round to nearest 5 = 250
        $this->assertEquals(250, $result['stage1']);
    }
}
