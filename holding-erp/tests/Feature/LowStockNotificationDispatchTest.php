<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Modules\Holding\Models\Brand;
use Modules\Holding\Models\Branch;
use Modules\Holding\Models\City;
use Modules\Holding\Models\Holding;
use Modules\Holding\Models\Warehouse;
use Modules\Inventory\Models\Product;
use Modules\Inventory\Models\WarehouseStock;
use Modules\Inventory\Services\StockPostingService;
use Modules\Notifications\Jobs\CreateLowStockNotifications;
use Tests\TestCase;

class LowStockNotificationDispatchTest extends TestCase
{
    use RefreshDatabase;

    public function test_low_stock_job_is_dispatched_when_threshold_is_crossed(): void
    {
        Bus::fake();

        $holding = Holding::query()->create(['code' => 'HLD', 'name' => 'Holding']);
        $city = City::query()->create(['holding_id' => $holding->id, 'code' => 'SMD', 'name' => 'Samarinda']);
        $brand = Brand::query()->create([
            'holding_id' => $holding->id,
            'code' => 'ICONMART',
            'name' => 'ICONMART',
            'business_type' => 'retail',
        ]);
        $branch = Branch::query()->create([
            'holding_id' => $holding->id,
            'brand_id' => $brand->id,
            'city_id' => $city->id,
            'code' => 'ICON-SMD-001',
            'name' => 'ICONMART Samarinda',
        ]);
        $warehouse = Warehouse::query()->create([
            'holding_id' => $holding->id,
            'brand_id' => $brand->id,
            'city_id' => $city->id,
            'branch_id' => $branch->id,
            'code' => 'ICON-SMD-WH-001',
            'name' => 'ICONMART Warehouse',
        ]);
        $product = Product::query()->create([
            'brand_id' => $brand->id,
            'sku' => 'SKU-001',
            'name' => 'Beras',
        ]);
        $stock = WarehouseStock::query()->create([
            'brand_id' => $brand->id,
            'city_id' => $city->id,
            'branch_id' => $branch->id,
            'warehouse_id' => $warehouse->id,
            'product_id' => $product->id,
            'on_hand' => 5,
            'reserved' => 0,
            'average_cost' => 1000,
            'reorder_level' => 3,
        ]);

        app(StockPostingService::class)->post([
            'brand_id' => $brand->id,
            'city_id' => $city->id,
            'branch_id' => $branch->id,
            'warehouse_id' => $warehouse->id,
            'product_id' => $product->id,
            'movement_type' => 'adjustment_out',
            'quantity' => 2,
        ]);

        Bus::assertDispatched(CreateLowStockNotifications::class, fn ($job): bool => $job->warehouseStockId === $stock->id);
    }
}
