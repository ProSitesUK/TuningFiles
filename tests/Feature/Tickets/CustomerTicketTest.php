<?php

namespace Tests\Feature\Tickets;

use App\Livewire\CustomerNewTicket;
use App\Livewire\CustomerTicketThread;
use App\Models\CustomerProfile;
use App\Models\Ticket;
use App\Models\TicketMessage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CustomerTicketTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RoleSeeder::class);
    }

    private function makeCustomer(): User
    {
        $user = User::factory()->create();
        $user->syncRoles(['customer']);
        CustomerProfile::create([
            'user_id'        => $user->id,
            'plan'           => 'Pro',
            'credit_balance' => 50,
        ]);
        return $user;
    }

    private function makeAdmin(): User
    {
        $admin = User::factory()->create();
        $admin->syncRoles(['admin']);
        return $admin;
    }

    public function test_customer_can_create_ticket(): void
    {
        $customer = $this->makeCustomer();

        Livewire::actingAs($customer)
            ->test(CustomerNewTicket::class)
            ->set('subject', 'Need help with tuning file')
            ->set('body', 'My car is not responding to the new tune properly.')
            ->call('submit');

        $this->assertDatabaseHas('tickets', [
            'customer_id' => $customer->id,
            'subject'     => 'Need help with tuning file',
            'status'      => 'open',
            'priority'    => 'normal',
        ]);

        $this->assertDatabaseHas('ticket_messages', [
            'user_id'     => $customer->id,
            'body'        => 'My car is not responding to the new tune properly.',
            'is_internal' => false,
        ]);
    }

    public function test_customer_can_reply(): void
    {
        $customer = $this->makeCustomer();

        $ticket = Ticket::create([
            'customer_id' => $customer->id,
            'subject'     => 'Test Ticket',
            'status'      => 'open',
            'priority'    => 'normal',
        ]);

        TicketMessage::create([
            'ticket_id'   => $ticket->id,
            'user_id'     => $customer->id,
            'body'        => 'Initial message',
            'is_internal' => false,
        ]);

        Livewire::actingAs($customer)
            ->test(CustomerTicketThread::class, ['ticketId' => $ticket->id])
            ->set('reply', 'Thank you, here is more information.')
            ->call('sendReply');

        $this->assertEquals(2, TicketMessage::where('ticket_id', $ticket->id)->count());
        $this->assertDatabaseHas('ticket_messages', [
            'ticket_id'   => $ticket->id,
            'user_id'     => $customer->id,
            'body'        => 'Thank you, here is more information.',
            'is_internal' => false,
        ]);
    }

    public function test_customer_cannot_see_internal_messages(): void
    {
        $customer = $this->makeCustomer();
        $admin = $this->makeAdmin();

        $ticket = Ticket::create([
            'customer_id' => $customer->id,
            'subject'     => 'Internal Test',
            'status'      => 'open',
            'priority'    => 'normal',
        ]);

        // Customer's visible message
        TicketMessage::create([
            'ticket_id'   => $ticket->id,
            'user_id'     => $customer->id,
            'body'        => 'Customer visible message',
            'is_internal' => false,
        ]);

        // Admin's internal note
        TicketMessage::create([
            'ticket_id'   => $ticket->id,
            'user_id'     => $admin->id,
            'body'        => 'This is a secret internal note',
            'is_internal' => true,
        ]);

        $component = Livewire::actingAs($customer)
            ->test(CustomerTicketThread::class, ['ticketId' => $ticket->id]);

        // The customer thread filters out internal messages
        $messages = $component->viewData('messages');
        $this->assertCount(1, $messages);
        $this->assertEquals('Customer visible message', $messages->first()->body);
    }
}
