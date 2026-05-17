<?php

namespace Tests\Feature;

use App\Core\Support\Scoping\ScopeContext;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Holding\Models\Brand;
use Modules\Holding\Models\Branch;
use Modules\Holding\Models\City;
use Modules\Holding\Models\Holding;
use Modules\Holding\Models\Warehouse;
use Modules\Inventory\Models\Product;
use Tests\TestCase;

class ScopedReferenceModelsTest extends TestCase
{
    use RefreshDatabase;

    public function test_products_and_warehouses_follow_declared_scope_columns(): void
    {
        $holding = Holding::query()->create(['code' => 'HLD', 'name' => 'Holding']);
        $city = City::query()->create(['holding_id' => $holding->id, 'code' => 'SMD', 'name' => 'Samarinda']);
        $brandA = Brand::query()->create([
            'holding_id' => $holding->id,
            'code' => 'A',
            'name' => 'Brand A',
            'business_type' => 'retail',
        ]);
        $brandB = Brand::query()->create([
            'holding_id' => $holding->id,
            'code' => 'B',
            'name' => 'Brand B',
            'business_type' => 'retail',
        ]);
        $branchA = Branch::query()->create([
            'holding_id' => $holding->id,
            'brand_id' => $brandA->id,
            'city_id' => $city->id,
            'code' => 'A-001',
            'name' => 'Branch A',
        ]);
        $branchB = Branch::query()->create([
            'holding_id' => $holding->id,
            'brand_id' => $brandB->id,
            'city_id' => $city->id,
            'code' => 'B-001',
            'name' => 'Branch B',
        ]);
        Warehouse::query()->create([
            'holding_id' => $holding->id,
            'brand_id' => $brandA->id,
            'city_id' => $city->id,
            'branch_id' => $branchA->id,
            'code' => 'A-WH',
            'name' => 'Warehouse A',
        ]);
        Warehouse::query()->create([
            'holding_id' => $holding->id,
            'brand_id' => $brandB->id,
            'city_id' => $city->id,
            'branch_id' => $branchB->id,
            'code' => 'B-WH',
            'name' => 'Warehouse B',
        ]);
        Product::query()->create(['brand_id' => $brandA->id, 'sku' => 'A-001', 'name' => 'Product A']);
        Product::query()->create(['brand_id' => $brandB->id, 'sku' => 'B-001', 'name' => 'Product B']);

        app(ScopeContext::class)->hydrate([
            'brand_id' => $brandA->id,
            'city_id' => $city->id,
            'branch_id' => $branchA->id,
            'warehouse_id' => null,
        ]);

        $this->assertSame(['Warehouse A'], Warehouse::query()->pluck('name')->all());
        $this->assertSame(['Product A'], Product::query()->pluck('name')->all());
    }
}
