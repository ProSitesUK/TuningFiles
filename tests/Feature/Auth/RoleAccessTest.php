<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RoleSeeder::class);
    }

    public function test_customer_cannot_access_admin(): void
    {
        $user = User::factory()->create();
        $user->syncRoles(['customer']);

        $response = $this->actingAs($user)->get('/admin/overview');
        $response->assertForbidden();
    }

    public function test_customer_cannot_access_reseller(): void
    {
        $user = User::factory()->create();
        $user->syncRoles(['customer']);

        $response = $this->actingAs($user)->get('/reseller');
        $response->assertForbidden();
    }

    public function test_unauthenticated_redirected_to_login(): void
    {
        $response = $this->get('/app');
        $response->assertRedirect('/login');
    }

    public function test_admin_can_access_admin(): void
    {
        $admin = User::factory()->create();
        $admin->syncRoles(['admin']);

        $response = $this->actingAs($admin)->get('/admin/overview');
        $response->assertOk();
    }

    public function test_reseller_can_access_reseller_area(): void
    {
        $reseller = User::factory()->create();
        $reseller->syncRoles(['reseller']);

        $response = $this->actingAs($reseller)->get('/reseller');
        $response->assertOk();
    }
}
