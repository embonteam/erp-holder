<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Modules\Audit\Models\Role;
use Modules\Holding\Models\Holding;
use Tests\TestCase;

class PermissionMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_without_purchase_permission_is_forbidden_from_purchase_screen(): void
    {
        $this->seed(DatabaseSeeder::class);

        $user = User::query()->create([
            'role_id' => Role::query()->where('code', 'cashier')->firstOrFail()->id,
            'holding_id' => Holding::query()->firstOrFail()->id,
            'name' => 'Cashier User',
            'email' => 'cashier@example.test',
            'password' => Hash::make('password123456'),
            'is_active' => true,
        ]);

        $this->actingAs($user)
            ->get(route('purchasing.purchases.index'))
            ->assertForbidden();
    }
}
