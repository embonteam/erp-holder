<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Modules\Audit\Models\Role;
use Modules\Holding\Models\Brand;
use Modules\Holding\Models\Branch;
use Modules\Holding\Models\City;
use Modules\Holding\Models\Holding;
use Modules\Holding\Models\Warehouse;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_create_update_and_deactivate_scoped_user(): void
    {
        $this->seed(DatabaseSeeder::class);

        $owner = User::query()->where('email', 'owner@holding.test')->firstOrFail();
        $role = Role::query()->where('code', 'purchasing')->firstOrFail();
        $holding = Holding::query()->firstOrFail();
        $brand = Brand::query()->where('code', 'ICONMART')->firstOrFail();
        $city = City::query()->where('code', 'SMD')->firstOrFail();
        $branch = Branch::query()->withoutGlobalScopes()->where('brand_id', $brand->id)->firstOrFail();
        $warehouse = Warehouse::query()->withoutGlobalScopes()->where('branch_id', $branch->id)->firstOrFail();

        $response = $this->actingAs($owner)->post(route('it.users.store'), [
            'name' => 'Purchasing Scoped User',
            'email' => 'purchasing-scoped@example.test',
            'password' => 'password123456',
            'role_id' => $role->id,
            'holding_id' => $holding->id,
            'brand_id' => $brand->id,
            'city_id' => $city->id,
            'branch_id' => $branch->id,
            'warehouse_id' => $warehouse->id,
        ]);

        $managedUser = User::query()->where('email', 'purchasing-scoped@example.test')->firstOrFail();

        $response->assertRedirect(route('it.users.show', $managedUser));
        $this->assertSame($warehouse->id, $managedUser->warehouse_id);
        $this->assertDatabaseHas('activity_logs', [
            'event' => 'it.user.created',
            'subject_type' => User::class,
            'subject_id' => $managedUser->id,
        ]);

        $this->actingAs($owner)->put(route('it.users.update', $managedUser), [
            'name' => 'Purchasing Scoped User Updated',
            'email' => 'purchasing-scoped-updated@example.test',
            'password' => '',
            'role_id' => $role->id,
            'holding_id' => $holding->id,
            'brand_id' => $brand->id,
            'city_id' => $city->id,
            'branch_id' => $branch->id,
            'warehouse_id' => $warehouse->id,
        ])->assertRedirect(route('it.users.show', $managedUser));

        $this->assertSame('Purchasing Scoped User Updated', $managedUser->refresh()->name);
        $this->assertDatabaseHas('activity_logs', [
            'event' => 'it.user.updated',
            'subject_type' => User::class,
            'subject_id' => $managedUser->id,
        ]);

        $this->actingAs($owner)
            ->post(route('it.users.deactivate', $managedUser))
            ->assertRedirect(route('it.users.show', $managedUser));

        $this->assertFalse($managedUser->refresh()->is_active);

        $this->actingAs($owner)
            ->post(route('it.users.reactivate', $managedUser))
            ->assertRedirect(route('it.users.show', $managedUser));

        $this->assertTrue($managedUser->refresh()->is_active);
    }

    public function test_user_without_it_permission_cannot_manage_users(): void
    {
        $this->seed(DatabaseSeeder::class);

        $user = User::query()->create([
            'role_id' => Role::query()->where('code', 'cashier')->firstOrFail()->id,
            'holding_id' => Holding::query()->firstOrFail()->id,
            'name' => 'Cashier User',
            'email' => 'it-cashier@example.test',
            'password' => Hash::make('password123456'),
            'is_active' => true,
        ]);

        $this->actingAs($user)
            ->get(route('it.users.index'))
            ->assertForbidden();
    }

    public function test_owner_cannot_deactivate_self(): void
    {
        $this->seed(DatabaseSeeder::class);

        $owner = User::query()->where('email', 'owner@holding.test')->firstOrFail();

        $this->actingAs($owner)
            ->post(route('it.users.deactivate', $owner))
            ->assertForbidden();
    }
}
