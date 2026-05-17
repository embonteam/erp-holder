<?php

namespace Tests\Feature;

use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Holding\Models\Brand;
use Modules\Holding\Models\Warehouse;
use Modules\Inventory\Models\Product;
use Modules\Inventory\Models\WarehouseStock;
use Modules\Notifications\Jobs\CreateLowStockNotifications;
use Tests\TestCase;

class CreateLowStockNotificationsJobTest extends TestCase
{
    use RefreshDatabase;

    public function test_low_stock_job_creates_notifications_for_relevant_users(): void
    {
        $this->seed(DatabaseSeeder::class);

        $brand = Brand::query()->where('code', 'ICONMART')->firstOrFail();
        $warehouse = Warehouse::query()->where('code', 'ICONMART-SMD-WH-001')->firstOrFail();
        $product = Product::query()->where('sku', 'BERAS-5KG')->firstOrFail();
        $stock = WarehouseStock::query()->create([
            'brand_id' => $brand->id,
            'city_id' => $warehouse->city_id,
            'branch_id' => $warehouse->branch_id,
            'warehouse_id' => $warehouse->id,
            'product_id' => $product->id,
            'on_hand' => 2,
            'reserved' => 0,
            'average_cost' => 15000,
            'reorder_level' => 3,
        ]);

        app(CreateLowStockNotifications::class, ['warehouseStockId' => $stock->id])->handle();

        $this->assertDatabaseHas('enterprise_notifications', [
            'type' => 'inventory.low_stock',
            'severity' => 'warning',
            'title' => 'Low stock alert',
        ]);
    }
}
