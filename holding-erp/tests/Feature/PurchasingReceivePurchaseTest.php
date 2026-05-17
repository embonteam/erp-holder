<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Holding\Models\Brand;
use Modules\Holding\Models\Branch;
use Modules\Holding\Models\City;
use Modules\Holding\Models\Holding;
use Modules\Holding\Models\Warehouse;
use Modules\Inventory\Models\Product;
use Modules\Inventory\Models\StockMovement;
use Modules\Inventory\Models\WarehouseStock;
use Modules\Purchasing\Models\Purchase;
use Modules\Purchasing\Models\PurchaseItem;
use Modules\Purchasing\Models\Supplier;
use Modules\Purchasing\Services\PurchaseService;
use Tests\TestCase;

class PurchasingReceivePurchaseTest extends TestCase
{
    use RefreshDatabase;

    public function test_receiving_an_approved_purchase_posts_stock_and_marks_it_received(): void
    {
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
        $supplier = Supplier::query()->create([
            'holding_id' => $holding->id,
            'code' => 'SUP-001',
            'name' => 'Main Supplier',
        ]);
        $product = Product::query()->create([
            'brand_id' => $brand->id,
            'sku' => 'SKU-001',
            'name' => 'Beras',
        ]);
        $purchase = Purchase::query()->create([
            'brand_id' => $brand->id,
            'city_id' => $city->id,
            'branch_id' => $branch->id,
            'warehouse_id' => $warehouse->id,
            'supplier_id' => $supplier->id,
            'po_number' => 'PO-001',
            'status' => 'approved',
        ]);
        PurchaseItem::query()->create([
            'purchase_id' => $purchase->id,
            'product_id' => $product->id,
            'quantity' => 10,
            'unit_price' => 15000,
            'line_total' => 150000,
        ]);

        app(PurchaseService::class)->receive($purchase);

        $this->assertDatabaseHas('purchases', [
            'id' => $purchase->id,
            'status' => 'received',
        ]);
        $this->assertDatabaseHas('purchase_items', [
            'purchase_id' => $purchase->id,
            'received_quantity' => 10,
        ]);
        $stock = WarehouseStock::query()->firstOrFail();

        $this->assertSame(10.0, (float) $stock->on_hand);
        $this->assertSame(15000.0, (float) $stock->average_cost);
        $this->assertSame('purchase', StockMovement::query()->firstOrFail()->movement_type);
    }
}
