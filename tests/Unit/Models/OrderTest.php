<?php

namespace Tests\Unit\Models;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RoleSeeder::class);
    }

    public function test_is_overdue_when_sla_past_and_not_terminal(): void
    {
        $order = Order::factory()->create([
            'sla_due_at' => now()->subHour(),
            'status'     => 'in_progress',
        ]);

        $this->assertTrue($order->isOverdue());
    }

    public function test_is_not_overdue_when_delivered(): void
    {
        $order = Order::factory()->create([
            'sla_due_at' => now()->subHour(),
            'status'     => 'delivered',
        ]);

        $this->assertFalse($order->isOverdue());
    }

    public function test_is_not_overdue_when_sla_null(): void
    {
        $order = Order::factory()->create([
            'sla_due_at' => null,
            'status'     => 'in_progress',
        ]);

        $this->assertFalse($order->isOverdue());
    }

    public function test_elapsed_label_shows_minutes(): void
    {
        $order = Order::factory()->create([
            'queued_at' => now()->subMinutes(14),
        ]);

        $this->assertStringContainsString('14m', $order->elapsedLabel());
    }

    public function test_elapsed_label_shows_hours(): void
    {
        $order = Order::factory()->create([
            'queued_at' => now()->subHours(3),
        ]);

        $this->assertStringContainsString('3h', $order->elapsedLabel());
    }

    public function test_status_constants_complete(): void
    {
        $expected = ['queued', 'in_progress', 'review', 'ready', 'delivered', 'refunded', 'dispute', 'failed'];

        foreach ($expected as $status) {
            $this->assertContains($status, Order::STATUSES);
        }
    }
}
