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
use Modules\Inventory\Models\StockOpname;
use Modules\Inventory\Models\WarehouseStock;
use Tests\TestCase;

class StockOpnameFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_create_and_approve_stock_opname_with_negative_variance_from_ui_route(): void
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

        $response = $this->actingAs($owner)->post(route('inventory.opnames.store'), [
            'warehouse_id' => $warehouse->id,
            'counted_on' => now()->toDateString(),
            'items' => [
                [
                    'product_id' => $product->id,
                    'counted_quantity' => 17,
                ],
            ],
        ]);

        $opname = StockOpname::query()->firstOrFail();
        $item = $opname->items()->firstOrFail();

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('inventory.opnames.show', $opname));

        $this->assertSame('draft', $opname->status);
        $this->assertSame($owner->id, $opname->created_by);
        $this->assertSame(20.0, (float) $item->system_quantity);
        $this->assertSame(17.0, (float) $item->counted_quantity);
        $this->assertSame(-3.0, (float) $item->variance_quantity);
        $this->assertDatabaseHas('activity_logs', [
            'event' => 'inventory.opname.created',
            'subject_type' => StockOpname::class,
            'subject_id' => $opname->id,
        ]);

        $this->actingAs($owner)
            ->post(route('inventory.opnames.approve', $opname))
            ->assertRedirect(route('inventory.opnames.show', $opname));

        $opname->refresh();
        $stock = WarehouseStock::query()
            ->where('warehouse_id', $warehouse->id)
            ->where('product_id', $product->id)
            ->firstOrFail();
        $movement = StockMovement::query()
            ->where('source_type', StockOpname::class)
            ->where('source_id', $opname->id)
            ->firstOrFail();

        $this->assertSame('approved', $opname->status);
        $this->assertSame($owner->id, $opname->approved_by);
        $this->assertNotNull($opname->approved_at);
        $this->assertSame(17.0, (float) $stock->on_hand);
        $this->assertSame('stock_opname_out', $movement->movement_type);
        $this->assertSame(3.0, (float) $movement->quantity);
        $this->assertDatabaseHas('activity_logs', [
            'event' => 'inventory.opname.approved',
            'subject_type' => StockOpname::class,
            'subject_id' => $opname->id,
        ]);

        $this->actingAs($owner)
            ->get(route('inventory.opnames.show', $opname))
            ->assertOk()
            ->assertSee('Activity Timeline')
            ->assertSee('inventory.opname.approved');
    }

    public function test_owner_can_approve_stock_opname_with_positive_variance(): void
    {
        $this->seed(DatabaseSeeder::class);

        $owner = User::query()->where('email', 'owner@holding.test')->firstOrFail();
        $warehouse = Warehouse::query()->where('code', 'ICONMART-SMD-WH-001')->firstOrFail();
        $product = Product::query()->where('sku', 'BERAS-5KG')->firstOrFail();

        $this->actingAs($owner)->post(route('inventory.opnames.store'), [
            'warehouse_id' => $warehouse->id,
            'counted_on' => now()->toDateString(),
            'items' => [
                [
                    'product_id' => $product->id,
                    'counted_quantity' => 6,
                ],
            ],
        ])->assertSessionHasNoErrors();

        $opname = StockOpname::query()->firstOrFail();

        $this->actingAs($owner)
            ->post(route('inventory.opnames.approve', $opname))
            ->assertRedirect(route('inventory.opnames.show', $opname));

        $stock = WarehouseStock::query()
            ->where('warehouse_id', $warehouse->id)
            ->where('product_id', $product->id)
            ->firstOrFail();
        $movement = StockMovement::query()
            ->where('source_type', StockOpname::class)
            ->where('source_id', $opname->id)
            ->firstOrFail();

        $this->assertSame(6.0, (float) $stock->on_hand);
        $this->assertSame('stock_opname_in', $movement->movement_type);
        $this->assertSame(6.0, (float) $movement->quantity);
    }

    public function test_user_without_inventory_permission_cannot_access_stock_opname(): void
    {
        $this->seed(DatabaseSeeder::class);

        $cashier = User::query()->create([
            'role_id' => Role::query()->where('code', 'cashier')->firstOrFail()->id,
            'holding_id' => Holding::query()->firstOrFail()->id,
            'name' => 'Cashier Without Opname Permission',
            'email' => 'cashier-stock-opname@example.test',
            'password' => Hash::make('password123456'),
            'is_active' => true,
        ]);

        $this->actingAs($cashier)
            ->get(route('inventory.opnames.index'))
            ->assertForbidden();
    }
}
