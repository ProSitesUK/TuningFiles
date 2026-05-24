<?php

namespace Tests\Feature\Orders;

use App\Livewire\NewOrderWizard;
use App\Models\CustomerProfile;
use App\Models\Ecu;
use App\Models\Order;
use App\Models\Tune;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleMake;
use App\Models\VehicleModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class OrderCreationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RoleSeeder::class);
        Storage::fake('local');
    }

    private function makeCustomer(int $balance = 100): User
    {
        $user = User::factory()->create();
        $user->syncRoles(['customer']);
        CustomerProfile::create([
            'user_id'        => $user->id,
            'plan'           => 'Pro',
            'credit_balance' => $balance,
        ]);
        return $user;
    }

    private function createVehicleAndEcu(): array
    {
        $make = VehicleMake::create([
            'name'      => 'Volkswagen',
            'slug'      => 'volkswagen',
            'is_active' => true,
        ]);

        $model = VehicleModel::create([
            'make_id'   => $make->id,
            'name'      => 'Golf R',
            'slug'      => 'golf-r',
            'is_active' => true,
        ]);

        $vehicle = Vehicle::create([
            'model_id'   => $model->id,
            'generation' => 'MK7',
            'year_start' => 2014,
            'year_end'   => 2020,
            'fuel'       => 'Petrol',
            'is_active'  => true,
        ]);

        $ecu = Ecu::create([
            'vendor'     => 'Bosch',
            'family'     => 'MED17',
            'variant'    => '1.62',
            'identifier' => 'Bosch MED17.1.62',
            'is_active'  => true,
        ]);

        // Attach ECU to vehicle via pivot
        $vehicle->ecus()->attach($ecu->id);

        // Create the tune
        $tune = Tune::create([
            'slug'        => 'stage_1',
            'label'       => 'Stage 1',
            'credit_cost' => 32,
            'is_active'   => true,
        ]);

        return compact('make', 'model', 'vehicle', 'ecu', 'tune');
    }

    public function test_order_created_with_sufficient_credits(): void
    {
        $user = $this->makeCustomer(100);
        $data = $this->createVehicleAndEcu();

        Livewire::actingAs($user)
            ->test(NewOrderWizard::class)
            ->set('makeId', $data['make']->id)
            ->set('modelId', $data['model']->id)
            ->set('vehicleId', $data['vehicle']->id)
            ->set('ecuId', $data['ecu']->id)
            ->set('tuneSlugs', ['stage_1'])
            ->set('upload', UploadedFile::fake()->create('test.bin', 1024))
            ->call('submit');

        $this->assertDatabaseHas('orders', [
            'customer_id'  => $user->id,
            'status'       => 'queued',
            'credits_cost' => 32,
        ]);

        $user->refresh();
        $this->assertEquals(68, $user->customerProfile->credit_balance);
    }

    public function test_order_rejected_with_insufficient_credits(): void
    {
        \App\Models\SiteSetting::put('pay_per_file_enabled', 'false');

        $user = $this->makeCustomer(10);
        $data = $this->createVehicleAndEcu();

        Livewire::actingAs($user)
            ->test(NewOrderWizard::class)
            ->set('makeId', $data['make']->id)
            ->set('modelId', $data['model']->id)
            ->set('vehicleId', $data['vehicle']->id)
            ->set('ecuId', $data['ecu']->id)
            ->set('tuneSlugs', ['stage_1'])
            ->set('paymentMethod', 'credits')
            ->set('upload', UploadedFile::fake()->create('test.bin', 1024))
            ->call('submit')
            ->assertHasErrors('upload');

        $this->assertEquals(0, Order::count());
    }

    public function test_reseller_id_auto_set(): void
    {
        $reseller = User::factory()->create();
        $reseller->syncRoles(['reseller']);

        $user = User::factory()->create(['reseller_id' => $reseller->id]);
        $user->syncRoles(['customer']);
        CustomerProfile::create([
            'user_id'        => $user->id,
            'plan'           => 'Pro',
            'credit_balance' => 200,
        ]);

        $data = $this->createVehicleAndEcu();

        Livewire::actingAs($user)
            ->test(NewOrderWizard::class)
            ->set('makeId', $data['make']->id)
            ->set('modelId', $data['model']->id)
            ->set('vehicleId', $data['vehicle']->id)
            ->set('ecuId', $data['ecu']->id)
            ->set('tuneSlugs', ['stage_1'])
            ->set('upload', UploadedFile::fake()->create('test.bin', 1024))
            ->call('submit');

        $order = Order::where('customer_id', $user->id)->first();
        $this->assertNotNull($order);
        $this->assertEquals($reseller->id, $order->reseller_id);
    }

    public function test_order_reference_auto_increments(): void
    {
        $user = $this->makeCustomer(500);
        $data = $this->createVehicleAndEcu();

        // First order
        Livewire::actingAs($user)
            ->test(NewOrderWizard::class)
            ->set('makeId', $data['make']->id)
            ->set('modelId', $data['model']->id)
            ->set('vehicleId', $data['vehicle']->id)
            ->set('ecuId', $data['ecu']->id)
            ->set('tuneSlugs', ['stage_1'])
            ->set('upload', UploadedFile::fake()->create('test1.bin', 1024))
            ->call('submit');

        // Second order
        Livewire::actingAs($user)
            ->test(NewOrderWizard::class)
            ->set('makeId', $data['make']->id)
            ->set('modelId', $data['model']->id)
            ->set('vehicleId', $data['vehicle']->id)
            ->set('ecuId', $data['ecu']->id)
            ->set('tuneSlugs', ['stage_1'])
            ->set('upload', UploadedFile::fake()->create('test2.bin', 1024))
            ->call('submit');

        $orders = Order::orderBy('id')->get();
        $this->assertCount(2, $orders);
        $this->assertNotEquals($orders[0]->reference, $orders[1]->reference);
        $this->assertEquals((int) $orders[0]->reference + 1, (int) $orders[1]->reference);
    }
}
