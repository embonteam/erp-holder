<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Modules\Audit\Models\ActivityLog;
use Modules\Audit\Models\Role;
use Modules\Holding\Models\Holding;
use Modules\Purchasing\Models\Supplier;
use Tests\TestCase;

class AuditLogViewerTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_view_audit_log_index_and_detail(): void
    {
        $this->seed(DatabaseSeeder::class);

        $owner = User::query()->where('email', 'owner@holding.test')->firstOrFail();

        $this->actingAs($owner)->post(route('purchasing.suppliers.store'), [
            'code' => 'AUDIT-SUP-001',
            'name' => 'Audit Supplier',
            'email' => 'audit-supplier@example.test',
        ]);

        $log = ActivityLog::query()
            ->where('event', 'purchasing.supplier.created')
            ->latest('id')
            ->firstOrFail();

        $this->actingAs($owner)
            ->get(route('audit.activity-logs.index'))
            ->assertOk()
            ->assertSee('Activity Logs')
            ->assertSee('purchasing.supplier.created')
            ->assertSee('AUDIT-SUP-001');

        $this->actingAs($owner)
            ->get(route('audit.activity-logs.show', $log))
            ->assertOk()
            ->assertSee('Audit Detail')
            ->assertSee('purchasing.supplier.created')
            ->assertSee('AUDIT-SUP-001');
    }

    public function test_user_without_audit_permission_cannot_view_audit_logs(): void
    {
        $this->seed(DatabaseSeeder::class);

        $user = User::query()->create([
            'role_id' => Role::query()->where('code', 'cashier')->firstOrFail()->id,
            'holding_id' => Holding::query()->firstOrFail()->id,
            'name' => 'Cashier User',
            'email' => 'audit-cashier@example.test',
            'password' => Hash::make('password123456'),
            'is_active' => true,
        ]);

        $this->actingAs($user)
            ->get(route('audit.activity-logs.index'))
            ->assertForbidden();
    }

    public function test_supplier_detail_shows_subject_activity_timeline(): void
    {
        $this->seed(DatabaseSeeder::class);

        $owner = User::query()->where('email', 'owner@holding.test')->firstOrFail();

        $this->actingAs($owner)->post(route('purchasing.suppliers.store'), [
            'code' => 'TIMELINE-SUP-001',
            'name' => 'Timeline Supplier',
        ]);

        $supplier = Supplier::query()->where('code', 'TIMELINE-SUP-001')->firstOrFail();

        $this->actingAs($owner)
            ->get(route('purchasing.suppliers.show', $supplier))
            ->assertOk()
            ->assertSee('Activity Timeline')
            ->assertSee('purchasing.supplier.created')
            ->assertSee('Audit detail');
    }
}

