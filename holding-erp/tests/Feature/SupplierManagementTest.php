<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Purchasing\Models\Supplier;
use Tests\TestCase;

class SupplierManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_manage_supplier_lifecycle(): void
    {
        $this->seed(DatabaseSeeder::class);

        $owner = User::query()->where('email', 'owner@holding.test')->firstOrFail();

        $response = $this->actingAs($owner)->post(route('purchasing.suppliers.store'), [
            'code' => 'sup-test-001',
            'name' => 'Supplier Test Enterprise',
            'tax_id' => '12.345.678.9-000.000',
            'phone' => '081234567890',
            'email' => 'supplier@example.test',
            'address' => 'Jl. Enterprise No. 1',
        ]);

        $supplier = Supplier::query()->where('code', 'SUP-TEST-001')->firstOrFail();

        $response->assertRedirect(route('purchasing.suppliers.show', $supplier));
        $this->assertDatabaseHas('activity_logs', [
            'event' => 'purchasing.supplier.created',
            'subject_type' => Supplier::class,
            'subject_id' => $supplier->id,
        ]);

        $this->actingAs($owner)->put(route('purchasing.suppliers.update', $supplier), [
            'code' => 'SUP-TEST-001',
            'name' => 'Supplier Test Updated',
            'tax_id' => '12.345.678.9-000.000',
            'phone' => '089999999999',
            'email' => 'updated-supplier@example.test',
            'address' => 'Jl. Enterprise Updated',
        ])->assertRedirect(route('purchasing.suppliers.show', $supplier));

        $this->assertSame('Supplier Test Updated', $supplier->refresh()->name);
        $this->assertDatabaseHas('activity_logs', [
            'event' => 'purchasing.supplier.updated',
            'subject_type' => Supplier::class,
            'subject_id' => $supplier->id,
        ]);

        $this->actingAs($owner)
            ->post(route('purchasing.suppliers.deactivate', $supplier))
            ->assertRedirect(route('purchasing.suppliers.show', $supplier));

        $this->assertFalse($supplier->refresh()->is_active);

        $this->actingAs($owner)
            ->post(route('purchasing.suppliers.reactivate', $supplier))
            ->assertRedirect(route('purchasing.suppliers.show', $supplier));

        $this->assertTrue($supplier->refresh()->is_active);
    }

    public function test_supplier_index_is_holding_scoped(): void
    {
        $this->seed(DatabaseSeeder::class);

        $owner = User::query()->where('email', 'owner@holding.test')->firstOrFail();

        $otherHolding = \Modules\Holding\Models\Holding::query()->create([
            'code' => 'OTHER-HOLDING',
            'name' => 'Other Holding',
        ]);

        Supplier::query()->create([
            'holding_id' => $otherHolding->id,
            'code' => 'SUP-OTHER-001',
            'name' => 'Other Holding Supplier',
        ]);

        $this->actingAs($owner)
            ->get(route('purchasing.suppliers.index'))
            ->assertOk()
            ->assertSee('Supplier Sembako Utama')
            ->assertDontSee('Other Holding Supplier');
    }
}
