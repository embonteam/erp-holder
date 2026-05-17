<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Inventory\Models\WarehouseStock;
use Modules\Purchasing\Models\Purchase;
use Tests\TestCase;

class PurchaseReceiveWebFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_receive_purchase_from_ui_route(): void
    {
        $this->seed(DatabaseSeeder::class);

        $owner = User::query()->where('email', 'owner@holding.test')->firstOrFail();
        $purchase = Purchase::query()->where('po_number', 'PO-DEMO-001')->firstOrFail();

        $this->actingAs($owner)
            ->post(route('purchasing.purchases.receive', $purchase))
            ->assertRedirect(route('purchasing.purchases.show', $purchase));

        $this->assertDatabaseHas('purchases', [
            'id' => $purchase->id,
            'status' => 'received',
        ]);
        $this->assertSame(50.0, (float) WarehouseStock::query()->firstOrFail()->on_hand);
        $this->assertDatabaseHas('activity_logs', [
            'event' => 'purchasing.purchase.received',
            'subject_type' => Purchase::class,
            'subject_id' => $purchase->id,
        ]);
    }
}
