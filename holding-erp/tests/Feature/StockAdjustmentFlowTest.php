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
use Modules\Inventory\Models\StockAdjustment;
use Modules\Inventory\Models\StockMovement;
use Modules\Inventory\Models\WarehouseStock;
use Tests\TestCase;

class StockAdjustmentFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_create_and_approve_positive_stock_adjustment_from_ui_route(): void
    {
        $this->seed(DatabaseSeeder::class);

        $owner = User::query()->where('email', 'owner@holding.test')->firstOrFail();
        $warehouse = Warehouse::query()->where('code', 'ICONMART-SMD-WH-001')->firstOrFail();
        $product = Product::query()->where('sku', 'BERAS-5KG')->firstOrFail();

        $response = $this->actingAs($owner)->post(route('inventory.adjustments.store'), [
            'warehouse_id' => $warehouse->id,
            'reason' => 'Cycle count positive correction',
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity_delta' => 7.5,
                    'note' => 'Physical stock found higher than system.',
                ],
            ],
        ]);

        $adjustment = StockAdjustment::query()
            ->where('reason', 'Cycle count positive correction')
            ->firstOrFail();

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('inventory.adjustments.show', $adjustment));

        $this->assertSame('draft', $adjustment->status);
        $this->assertSame($owner->id, $adjustment->requested_by);
        $this->assertDatabaseHas('activity_logs', [
            'event' => 'inventory.adjustment.created',
            'subject_type' => StockAdjustment::class,
            'subject_id' => $adjustment->id,
        ]);

        $this->actingAs($owner)
            ->post(route('inventory.adjustments.approve', $adjustment))
            ->assertRedirect(route('inventory.adjustments.show', $adjustment));

        $adjustment->refresh();
        $stock = WarehouseStock::query()
            ->where('warehouse_id', $warehouse->id)
            ->where('product_id', $product->id)
            ->firstOrFail();
        $movement = StockMovement::query()
            ->where('source_type', StockAdjustment::class)
            ->where('source_id', $adjustment->id)
            ->firstOrFail();

        $this->assertSame('approved', $adjustment->status);
        $this->assertSame($owner->id, $adjustment->approved_by);
        $this->assertNotNull($adjustment->approved_at);
        $this->assertSame(7.5, (float) $stock->on_hand);
        $this->assertSame('adjustment_in', $movement->movement_type);
        $this->assertSame(7.5, (float) $movement->quantity);
        $this->assertDatabaseHas('activity_logs', [
            'event' => 'inventory.adjustment.approved',
            'subject_type' => StockAdjustment::class,
            'subject_id' => $adjustment->id,
        ]);

        $this->actingAs($owner)
            ->get(route('inventory.adjustments.show', $adjustment))
            ->assertOk()
            ->assertSee('Activity Timeline')
            ->assertSee('inventory.adjustment.approved');
    }

    public function test_owner_can_approve_negative_adjustment_when_stock_is_available(): void
    {
        $this->seed(DatabaseSeeder::class);

        $owner = User::query()->where('email', 'owner@holding.test')->firstOrFail();
        $warehouse = Warehouse::query()->where('code', 'ICONMART-SMD-WH-001')->firstOrFail();
        $product = Product::query()->where('sku', 'BERAS-5KG')->firstOrFail();

        WarehouseStock::query()->create([
            'brand_id' => $warehouse->brand_id,
            'city_id' => $warehouse->city_id,
            'branch_id' => $warehouse->branch_id,
            'warehouse_id' => $warehouse->id,
            'product_id' => $product->id,
            'on_hand' => 20,
            'reserved' => 0,
            'average_cost' => 15000,
            'reorder_level' => 5,
        ]);

        $this->actingAs($owner)->post(route('inventory.adjustments.store'), [
            'warehouse_id' => $warehouse->id,
            'reason' => 'Damaged stock write-off',
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity_delta' => -4,
                    'note' => 'Damaged pack discovered during daily check.',
                ],
            ],
        ])->assertSessionHasNoErrors();

        $adjustment = StockAdjustment::query()
            ->where('reason', 'Damaged stock write-off')
            ->firstOrFail();

        $this->actingAs($owner)
            ->post(route('inventory.adjustments.approve', $adjustment))
            ->assertRedirect(route('inventory.adjustments.show', $adjustment));

        $stock = WarehouseStock::query()
            ->where('warehouse_id', $warehouse->id)
            ->where('product_id', $product->id)
            ->firstOrFail();
        $movement = StockMovement::query()
            ->where('source_type', StockAdjustment::class)
            ->where('source_id', $adjustment->id)
            ->firstOrFail();

        $this->assertSame(16.0, (float) $stock->on_hand);
        $this->assertSame('adjustment_out', $movement->movement_type);
        $this->assertSame(4.0, (float) $movement->quantity);
    }

    public function test_user_without_inventory_permission_cannot_access_stock_adjustments(): void
    {
        $this->seed(DatabaseSeeder::class);

        $cashier = User::query()->create([
            'role_id' => Role::query()->where('code', 'cashier')->firstOrFail()->id,
            'holding_id' => Holding::query()->firstOrFail()->id,
            'name' => 'Cashier Without Inventory Permission',
            'email' => 'cashier-stock-adjustment@example.test',
            'password' => Hash::make('password123456'),
            'is_active' => true,
        ]);

        $this->actingAs($cashier)
            ->get(route('inventory.adjustments.index'))
            ->assertForbidden();
    }
}
