<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Modules\Audit\Models\Role;
use Modules\Holding\Models\Holding;
use Modules\Holding\Models\Warehouse;
use Modules\Inventory\Models\Product;
use Modules\Purchasing\Models\Purchase;
use Modules\Purchasing\Models\Supplier;
use Tests\TestCase;

class ApprovalInboxTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_see_pending_purchase_approval(): void
    {
        $this->seed(DatabaseSeeder::class);

        $owner = User::query()->where('email', 'owner@holding.test')->firstOrFail();
        $supplier = Supplier::query()->firstOrFail();
        $warehouse = Warehouse::query()->firstOrFail();
        $product = Product::query()->firstOrFail();

        $this->actingAs($owner)->post(route('purchasing.purchases.store'), [
            'supplier_id' => $supplier->id,
            'warehouse_id' => $warehouse->id,
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 3,
                    'unit_price' => 12000,
                    'tax_rate' => 11,
                ],
            ],
        ]);

        $purchase = Purchase::query()->latest('id')->firstOrFail();

        $this->actingAs($owner)
            ->get(route('approvals.index'))
            ->assertOk()
            ->assertSee('Approval Inbox')
            ->assertSee($purchase->po_number)
            ->assertSee('Purchase Order menunggu approval');
    }

    public function test_user_without_approval_permission_cannot_open_approval_inbox(): void
    {
        $this->seed(DatabaseSeeder::class);

        $user = User::query()->create([
            'role_id' => Role::query()->where('code', 'cashier')->firstOrFail()->id,
            'holding_id' => Holding::query()->firstOrFail()->id,
            'name' => 'Cashier User',
            'email' => 'approval-cashier@example.test',
            'password' => Hash::make('password123456'),
            'is_active' => true,
        ]);

        $this->actingAs($user)
            ->get(route('approvals.index'))
            ->assertForbidden();
    }
}
