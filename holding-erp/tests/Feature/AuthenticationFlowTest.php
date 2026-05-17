<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_login_and_login_is_logged(): void
    {
        $this->seed(DatabaseSeeder::class);

        $response = $this->post(route('login'), [
            'email' => 'owner@holding.test',
            'password' => 'password123456',
        ]);

        $response->assertRedirect(route('holding.dashboard'));
        $this->assertAuthenticated();
        $this->assertDatabaseHas('login_logs', ['event' => 'login']);
        $this->assertDatabaseHas('activity_logs', ['event' => 'auth.login']);
        $this->assertNotNull(User::query()->where('email', 'owner@holding.test')->firstOrFail()->last_login_at);
    }
}
