<?php

namespace Tests\Unit\Models;

use App\Models\Invoice;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvoiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RoleSeeder::class);
    }

    public function test_auto_generates_reference(): void
    {
        $invoice = Invoice::factory()->create(['reference' => null]);

        $this->assertStringStartsWith('INV-', $invoice->reference);
    }

    public function test_pending_scope_returns_draft_and_sent(): void
    {
        Invoice::factory()->create(['status' => 'draft']);
        Invoice::factory()->create(['status' => 'sent']);
        Invoice::factory()->create(['status' => 'paid']);

        $pending = Invoice::pending()->get();

        $this->assertCount(2, $pending);
    }

    public function test_overdue_scope(): void
    {
        $overdue = Invoice::factory()->create([
            'status' => 'sent',
            'due_at' => now()->subDay(),
        ]);

        Invoice::factory()->create([
            'status' => 'sent',
            'due_at' => now()->addDay(),
        ]);

        Invoice::factory()->create([
            'status' => 'paid',
            'due_at' => now()->subDay(),
        ]);

        $results = Invoice::overdue()->get();

        $this->assertCount(1, $results);
        $this->assertEquals($overdue->id, $results->first()->id);
    }
}
