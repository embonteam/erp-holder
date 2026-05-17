<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Modules\Audit\Models\Permission;
use Modules\Audit\Models\Role;
use Modules\Holding\Models\Holding;
use Tests\TestCase;

class RolePermissionManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_create_and_update_custom_role_permissions(): void
    {
        $this->seed(DatabaseSeeder::class);

        $owner = User::query()->where('email', 'owner@holding.test')->firstOrFail();
        $viewStock = Permission::query()->where('code', 'inventory.stock.view')->firstOrFail();
        $viewPurchase = Permission::query()->where('code', 'purchasing.purchase.view')->firstOrFail();
        $viewNotifications = Permission::query()->where('code', 'notifications.view')->firstOrFail();

        $response = $this->actingAs($owner)->post(route('it.roles.store'), [
            'code' => 'regional_custom_ops',
            'name' => 'Regional Custom Ops',
            'scope_level' => 'city',
            'permissions' => [$viewStock->id, $viewPurchase->id],
        ]);

        $role = Role::query()->where('code', 'regional_custom_ops')->firstOrFail();

        $response->assertRedirect(route('it.roles.show', $role));
        $this->assertTrue($role->permissions()->where('code', 'inventory.stock.view')->exists());
        $this->assertTrue($role->permissions()->where('code', 'purchasing.purchase.view')->exists());
        $this->assertDatabaseHas('activity_logs', [
            'event' => 'it.role.created',
            'subject_type' => Role::class,
            'subject_id' => $role->id,
        ]);

        $this->actingAs($owner)->put(route('it.roles.update', $role), [
            'code' => 'regional_custom_ops_updated',
            'name' => 'Regional Custom Ops Updated',
            'scope_level' => 'warehouse',
            'permissions' => [$viewNotifications->id],
        ])->assertRedirect(route('it.roles.show', $role));

        $role->refresh();
        $this->assertSame('regional_custom_ops_updated', $role->code);
        $this->assertSame('warehouse', $role->scope_level);
        $this->assertTrue($role->permissions()->where('code', 'notifications.view')->exists());
        $this->assertFalse($role->permissions()->where('code', 'inventory.stock.view')->exists());
        $this->assertDatabaseHas('activity_logs', [
            'event' => 'it.role.updated',
            'subject_type' => Role::class,
            'subject_id' => $role->id,
        ]);
    }

    public function test_owner_role_is_locked_from_update(): void
    {
        $this->seed(DatabaseSeeder::class);

        $owner = User::query()->where('email', 'owner@holding.test')->firstOrFail();
        $ownerRole = Role::query()->where('code', 'owner')->firstOrFail();
        $permission = Permission::query()->where('code', 'notifications.view')->firstOrFail();

        $this->actingAs($owner)->put(route('it.roles.update', $ownerRole), [
            'code' => 'owner_changed',
            'name' => 'Owner Changed',
            'scope_level' => 'holding',
            'permissions' => [$permission->id],
        ])->assertForbidden();
    }

    public function test_user_without_role_permission_cannot_manage_roles(): void
    {
        $this->seed(DatabaseSeeder::class);

        $user = User::query()->create([
            'role_id' => Role::query()->where('code', 'cashier')->firstOrFail()->id,
            'holding_id' => Holding::query()->firstOrFail()->id,
            'name' => 'Cashier User',
            'email' => 'role-cashier@example.test',
            'password' => Hash::make('password123456'),
            'is_active' => true,
        ]);

        $this->actingAs($user)
            ->get(route('it.roles.index'))
            ->assertForbidden();
    }
}
