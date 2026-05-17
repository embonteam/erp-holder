<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Holding\Models\Brand;
use Modules\Holding\Models\Warehouse;
use Modules\Inventory\Models\Category;
use Modules\Inventory\Models\Product;
use Modules\Purchasing\Models\Purchase;
use Modules\Purchasing\Models\PurchaseItem;
use Modules\Purchasing\Models\Supplier;

class DemoOperationalSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment(['local', 'testing'])) {
            return;
        }

        $brand = Brand::query()->where('code', 'ICONMART')->firstOrFail();
        $warehouse = Warehouse::query()->where('code', 'ICONMART-SMD-WH-001')->firstOrFail();
        $supplier = Supplier::query()->updateOrCreate(
            ['code' => 'SUP-ICON-001'],
            [
                'holding_id' => $brand->holding_id,
                'name' => 'Supplier Sembako Utama',
                'is_active' => true,
            ],
        );
        $category = Category::query()->updateOrCreate(
            ['brand_id' => $brand->id, 'code' => 'SEMBAKO'],
            ['name' => 'Sembako', 'is_active' => true],
        );
        $product = Product::query()->updateOrCreate(
            ['brand_id' => $brand->id, 'sku' => 'BERAS-5KG'],
            [
                'category_id' => $category->id,
                'barcode' => '899999900001',
                'name' => 'Beras Premium 5 Kg',
                'product_type' => 'stock',
                'track_stock' => true,
                'is_active' => true,
            ],
        );

        $purchase = Purchase::query()->updateOrCreate(
            ['po_number' => 'PO-DEMO-001'],
            [
                'brand_id' => $brand->id,
                'city_id' => $warehouse->city_id,
                'branch_id' => $warehouse->branch_id,
                'warehouse_id' => $warehouse->id,
                'supplier_id' => $supplier->id,
                'status' => 'approved',
                'subtotal' => 750000,
                'tax_amount' => 82500,
                'total_amount' => 832500,
                'ordered_at' => now(),
            ],
        );

        PurchaseItem::query()->updateOrCreate(
            ['purchase_id' => $purchase->id, 'product_id' => $product->id],
            [
                'quantity' => 50,
                'unit_price' => 15000,
                'tax_rate' => 11,
                'line_total' => 750000,
                'received_quantity' => 0,
            ],
        );
    }
}
