<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Holding\Models\Warehouse;
use Modules\Inventory\Models\Product;
use Modules\Purchasing\Models\Purchase;
use Modules\Purchasing\Models\Supplier;
use Tests\TestCase;

class PurchaseCreateApproveFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_create_and_approve_purchase(): void
    {
        $this->seed(DatabaseSeeder::class);

        $owner = User::query()->where('email', 'owner@holding.test')->firstOrFail();
        $supplier = Supplier::query()->firstOrFail();
        $warehouse = Warehouse::query()->firstOrFail();
        $product = Product::query()->firstOrFail();

        $response = $this->actingAs($owner)->post(route('purchasing.purchases.store'), [
            'supplier_id' => $supplier->id,
            'warehouse_id' => $warehouse->id,
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 2,
                    'unit_price' => 10000,
                    'tax_rate' => 11,
                ],
            ],
        ]);

        $purchase = Purchase::query()->latest('id')->firstOrFail();

        $response->assertRedirect(route('purchasing.purchases.show', $purchase));
        $this->assertSame('draft', $purchase->status);
        $this->assertSame(20000.0, (float) $purchase->subtotal);
        $this->assertSame(2200.0, (float) $purchase->tax_amount);
        $this->assertDatabaseHas('activity_logs', [
            'event' => 'purchasing.purchase.created',
            'subject_type' => Purchase::class,
            'subject_id' => $purchase->id,
        ]);

        $this->actingAs($owner)
            ->post(route('purchasing.purchases.approve', $purchase))
            ->assertRedirect(route('purchasing.purchases.show', $purchase));

        $this->assertSame('approved', $purchase->refresh()->status);
        $this->assertSame($owner->id, $purchase->approved_by);
        $this->assertDatabaseHas('activity_logs', [
            'event' => 'purchasing.purchase.approved',
            'subject_type' => Purchase::class,
            'subject_id' => $purchase->id,
        ]);
    }
}
