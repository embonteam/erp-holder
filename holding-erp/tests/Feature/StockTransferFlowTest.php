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
use Modules\Inventory\Models\StockMovement;
use Modules\Inventory\Models\StockTransfer;
use Modules\Inventory\Models\WarehouseStock;
use Tests\TestCase;

class StockTransferFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_create_approve_dispatch_and_receive_stock_transfer_from_ui_routes(): void
    {
        $this->seed(DatabaseSeeder::class);

        $owner = User::query()->where('email', 'owner@holding.test')->firstOrFail();
        $sourceWarehouse = Warehouse::query()->where('code', 'ICONMART-SMD-WH-001')->firstOrFail();
        $destinationWarehouse = Warehouse::query()->where('code', 'ICONMART-SMD-WH-002')->firstOrFail();
        $product = Product::query()->where('sku', 'BERAS-5KG')->firstOrFail();

        WarehouseStock::query()->create([
            'brand_id' => $sourceWarehouse->brand_id,
            'city_id' => $sourceWarehouse->city_id,
            'branch_id' => $sourceWarehouse->branch_id,
            'warehouse_id' => $sourceWarehouse->id,
            'product_id' => $product->id,
            'on_hand' => 12,
            'reserved' => 0,
            'average_cost' => 15000,
            'reorder_level' => 3,
        ]);

        $response = $this->actingAs($owner)->post(route('inventory.transfers.store'), [
            'source_warehouse_id' => $sourceWarehouse->id,
            'destination_warehouse_id' => $destinationWarehouse->id,
            'items' => [
                [
                    'product_id' => $product->id,
                    'requested_quantity' => 5,
                ],
            ],
        ]);

        $transfer = StockTransfer::query()->firstOrFail();

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('inventory.transfers.show', $transfer));

        $this->assertSame('draft', $transfer->status);
        $this->assertSame($owner->id, $transfer->requested_by);
        $this->assertDatabaseHas('activity_logs', [
            'event' => 'inventory.transfer.created',
            'subject_type' => StockTransfer::class,
            'subject_id' => $transfer->id,
        ]);

        $this->actingAs($owner)
            ->post(route('inventory.transfers.approve', $transfer))
            ->assertRedirect(route('inventory.transfers.show', $transfer));

        $this->assertSame('approved', $transfer->refresh()->status);
        $this->assertSame($owner->id, $transfer->approved_by);

        $this->actingAs($owner)
            ->post(route('inventory.transfers.dispatch', $transfer))
            ->assertRedirect(route('inventory.transfers.show', $transfer));

        $transfer->refresh();
        $sourceStock = WarehouseStock::query()
            ->where('warehouse_id', $sourceWarehouse->id)
            ->where('product_id', $product->id)
            ->firstOrFail();
        $outMovement = StockMovement::query()
            ->where('source_type', StockTransfer::class)
            ->where('source_id', $transfer->id)
            ->where('movement_type', 'transfer_out')
            ->firstOrFail();

        $this->assertSame('dispatched', $transfer->status);
        $this->assertNotNull($transfer->dispatched_at);
        $this->assertSame(7.0, (float) $sourceStock->on_hand);
        $this->assertSame(5.0, (float) $transfer->items()->firstOrFail()->dispatched_quantity);
        $this->assertSame(5.0, (float) $outMovement->quantity);

        $this->actingAs($owner)
            ->post(route('inventory.transfers.receive', $transfer))
            ->assertRedirect(route('inventory.transfers.show', $transfer));

        $transfer->refresh();
        $destinationStock = WarehouseStock::query()
            ->where('warehouse_id', $destinationWarehouse->id)
            ->where('product_id', $product->id)
            ->firstOrFail();
        $inMovement = StockMovement::query()
            ->where('source_type', StockTransfer::class)
            ->where('source_id', $transfer->id)
            ->where('movement_type', 'transfer_in')
            ->firstOrFail();

        $this->assertSame('received', $transfer->status);
        $this->assertNotNull($transfer->received_at);
        $this->assertSame(5.0, (float) $destinationStock->on_hand);
        $this->assertSame(5.0, (float) $transfer->items()->firstOrFail()->received_quantity);
        $this->assertSame(5.0, (float) $inMovement->quantity);
        $this->assertDatabaseHas('activity_logs', [
            'event' => 'inventory.transfer.received',
            'subject_type' => StockTransfer::class,
            'subject_id' => $transfer->id,
        ]);

        $this->actingAs($owner)
            ->get(route('inventory.transfers.show', $transfer))
            ->assertOk()
            ->assertSee('Activity Timeline')
            ->assertSee('inventory.transfer.received');
    }

    public function test_user_without_inventory_permission_cannot_access_stock_transfers(): void
    {
        $this->seed(DatabaseSeeder::class);

        $cashier = User::query()->create([
            'role_id' => Role::query()->where('code', 'cashier')->firstOrFail()->id,
            'holding_id' => Holding::query()->firstOrFail()->id,
            'name' => 'Cashier Without Transfer Permission',
            'email' => 'cashier-stock-transfer@example.test',
            'password' => Hash::make('password123456'),
            'is_active' => true,
        ]);

        $this->actingAs($cashier)
            ->get(route('inventory.transfers.index'))
            ->assertForbidden();
    }
}
