<?php

namespace Tests\Unit\Models;

use App\Models\CustomerProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RoleSeeder::class);
    }

    public function test_is_online_when_status_online(): void
    {
        $user = User::factory()->create(['status' => 'online']);

        $this->assertTrue($user->isOnline());
    }

    public function test_is_away_for_away_busy_holiday(): void
    {
        foreach (['away', 'busy', 'holiday'] as $status) {
            $user = User::factory()->create(['status' => $status]);
            $this->assertTrue($user->isAway(), "Expected isAway() for status '{$status}'");
        }
    }

    public function test_is_offline_for_off_and_null(): void
    {
        $userOff = User::factory()->create(['status' => 'off']);
        $this->assertTrue($userOff->isOffline());

        $userNull = User::factory()->create(['status' => null]);
        $this->assertTrue($userNull->isOffline());
    }

    public function test_status_dot_returns_correct_class(): void
    {
        $online = User::factory()->create(['status' => 'online']);
        $this->assertEquals('ok', $online->statusDot());

        $away = User::factory()->create(['status' => 'away']);
        $this->assertEquals('warn', $away->statusDot());

        $off = User::factory()->create(['status' => 'off']);
        $this->assertEquals('mute', $off->statusDot());
    }

    public function test_has_reseller_when_reseller_id_set(): void
    {
        $reseller = User::factory()->create();
        $user = User::factory()->create(['reseller_id' => $reseller->id]);

        $this->assertTrue($user->hasReseller());
    }

    public function test_has_reseller_false_when_null(): void
    {
        $user = User::factory()->create(['reseller_id' => null]);

        $this->assertFalse($user->hasReseller());
    }

    public function test_credit_balance_returns_profile_balance(): void
    {
        $user = User::factory()->create();
        CustomerProfile::factory()->create([
            'user_id'        => $user->id,
            'credit_balance' => 50,
        ]);

        $this->assertEquals(50, $user->creditBalance());
    }

    public function test_credit_balance_returns_zero_without_profile(): void
    {
        $user = User::factory()->create();

        $this->assertEquals(0, $user->creditBalance());
    }
}
