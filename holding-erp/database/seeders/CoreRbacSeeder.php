<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Audit\Models\Permission;
use Modules\Audit\Models\Role;

class CoreRbacSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['code' => 'owner', 'name' => 'Owner', 'scope_level' => 'holding', 'is_system' => true],
            ['code' => 'global_director', 'name' => 'Global Director', 'scope_level' => 'holding', 'is_system' => true],
            ['code' => 'global_finance', 'name' => 'Global Finance', 'scope_level' => 'holding', 'is_system' => true],
            ['code' => 'global_it', 'name' => 'Global IT', 'scope_level' => 'holding', 'is_system' => true],
            ['code' => 'global_legal', 'name' => 'Global Legal', 'scope_level' => 'holding', 'is_system' => true],
            ['code' => 'global_hrd', 'name' => 'Global HRD', 'scope_level' => 'holding', 'is_system' => true],
            ['code' => 'global_audit', 'name' => 'Global Audit', 'scope_level' => 'holding', 'is_system' => true],
            ['code' => 'regional_manager', 'name' => 'Regional Manager', 'scope_level' => 'city', 'is_system' => true],
            ['code' => 'regional_finance', 'name' => 'Regional Finance', 'scope_level' => 'city', 'is_system' => true],
            ['code' => 'regional_warehouse', 'name' => 'Regional Warehouse', 'scope_level' => 'city', 'is_system' => true],
            ['code' => 'regional_operational', 'name' => 'Regional Operational', 'scope_level' => 'city', 'is_system' => true],
            ['code' => 'brand_admin', 'name' => 'Brand Admin', 'scope_level' => 'brand', 'is_system' => true],
            ['code' => 'warehouse_staff', 'name' => 'Warehouse Staff', 'scope_level' => 'warehouse', 'is_system' => true],
            ['code' => 'purchasing', 'name' => 'Purchasing', 'scope_level' => 'warehouse', 'is_system' => true],
            ['code' => 'distribution_admin', 'name' => 'Distribution Admin', 'scope_level' => 'warehouse', 'is_system' => true],
            ['code' => 'delivery_coordinator', 'name' => 'Delivery Coordinator', 'scope_level' => 'warehouse', 'is_system' => true],
            ['code' => 'driver', 'name' => 'Driver', 'scope_level' => 'branch', 'is_system' => true],
            ['code' => 'sales_distribution', 'name' => 'Sales Distribution', 'scope_level' => 'branch', 'is_system' => true],
            ['code' => 'cashier', 'name' => 'Cashier', 'scope_level' => 'branch', 'is_system' => true],
            ['code' => 'kitchen', 'name' => 'Kitchen', 'scope_level' => 'branch', 'is_system' => true],
            ['code' => 'production_staff', 'name' => 'Production Staff', 'scope_level' => 'warehouse', 'is_system' => true],
            ['code' => 'outlet_supervisor', 'name' => 'Outlet Supervisor', 'scope_level' => 'branch', 'is_system' => true],
        ];

        foreach ($roles as $role) {
            Role::query()->updateOrCreate(['code' => $role['code']], $role);
        }

        $permissions = [
            ['code' => 'holding.dashboard.view', 'name' => 'View holding dashboard', 'module' => 'holding', 'action' => 'view'],
            ['code' => 'approval.inbox.view', 'name' => 'View approval inbox', 'module' => 'approval', 'action' => 'view'],
            ['code' => 'notifications.view', 'name' => 'View notifications', 'module' => 'notifications', 'action' => 'view'],
            ['code' => 'inventory.stock.view', 'name' => 'View stock', 'module' => 'inventory', 'action' => 'view'],
            ['code' => 'inventory.adjustment.create', 'name' => 'Create stock adjustment', 'module' => 'inventory', 'action' => 'create'],
            ['code' => 'inventory.adjustment.approve', 'name' => 'Approve stock adjustment', 'module' => 'inventory', 'action' => 'approve'],
            ['code' => 'inventory.opname.create', 'name' => 'Create stock opname', 'module' => 'inventory', 'action' => 'create'],
            ['code' => 'inventory.opname.approve', 'name' => 'Approve stock opname', 'module' => 'inventory', 'action' => 'approve'],
            ['code' => 'inventory.transfer.create', 'name' => 'Create warehouse transfer', 'module' => 'inventory', 'action' => 'create'],
            ['code' => 'inventory.transfer.approve', 'name' => 'Approve warehouse transfer', 'module' => 'inventory', 'action' => 'approve'],
            ['code' => 'inventory.transfer.dispatch', 'name' => 'Dispatch warehouse transfer', 'module' => 'inventory', 'action' => 'dispatch'],
            ['code' => 'inventory.transfer.receive', 'name' => 'Receive warehouse transfer', 'module' => 'inventory', 'action' => 'receive'],
            ['code' => 'purchasing.supplier.view', 'name' => 'View suppliers', 'module' => 'purchasing', 'action' => 'view'],
            ['code' => 'purchasing.supplier.create', 'name' => 'Create suppliers', 'module' => 'purchasing', 'action' => 'create'],
            ['code' => 'purchasing.supplier.update', 'name' => 'Update suppliers', 'module' => 'purchasing', 'action' => 'update'],
            ['code' => 'purchasing.supplier.deactivate', 'name' => 'Deactivate suppliers', 'module' => 'purchasing', 'action' => 'deactivate'],
            ['code' => 'purchasing.purchase.create', 'name' => 'Create purchase', 'module' => 'purchasing', 'action' => 'create'],
            ['code' => 'purchasing.purchase.view', 'name' => 'View purchase', 'module' => 'purchasing', 'action' => 'view'],
            ['code' => 'purchasing.purchase.approve', 'name' => 'Approve purchase', 'module' => 'purchasing', 'action' => 'approve'],
            ['code' => 'purchasing.purchase.receive', 'name' => 'Receive purchase', 'module' => 'purchasing', 'action' => 'receive'],
            ['code' => 'distribution.order.create', 'name' => 'Create sales order', 'module' => 'distribution', 'action' => 'create'],
            ['code' => 'delivery.dispatch.assign', 'name' => 'Assign delivery', 'module' => 'delivery', 'action' => 'assign'],
            ['code' => 'finance.payment.approve', 'name' => 'Approve payment', 'module' => 'finance', 'action' => 'approve'],
            ['code' => 'tax.report.export', 'name' => 'Export tax report', 'module' => 'tax', 'action' => 'export'],
            ['code' => 'pos.sale.create', 'name' => 'Create sale', 'module' => 'pos', 'action' => 'create'],
            ['code' => 'pos.sale.void', 'name' => 'Void sale', 'module' => 'pos', 'action' => 'void'],
            ['code' => 'hrd.employee.manage', 'name' => 'Manage employees', 'module' => 'hrd', 'action' => 'manage'],
            ['code' => 'legal.contract.manage', 'name' => 'Manage contracts', 'module' => 'legal', 'action' => 'manage'],
            ['code' => 'it.user.manage', 'name' => 'Manage users', 'module' => 'it', 'action' => 'manage'],
            ['code' => 'it.role.manage', 'name' => 'Manage roles and permissions', 'module' => 'it', 'action' => 'manage'],
            ['code' => 'audit.log.view', 'name' => 'View audit logs', 'module' => 'audit', 'action' => 'view'],
        ];

        foreach ($permissions as $permission) {
            Permission::query()->updateOrCreate(['code' => $permission['code']], $permission);
        }

        $owner = Role::query()->where('code', 'owner')->firstOrFail();
        $owner->permissions()->sync(Permission::query()->pluck('id'));

        $this->syncRolePermissions('purchasing', [
            'purchasing.supplier.view',
            'purchasing.supplier.create',
            'purchasing.supplier.update',
            'purchasing.purchase.view',
            'purchasing.purchase.create',
            'purchasing.purchase.receive',
            'inventory.stock.view',
            'inventory.adjustment.create',
            'inventory.opname.create',
            'inventory.transfer.create',
            'notifications.view',
        ]);

        $this->syncRolePermissions('regional_manager', [
            'holding.dashboard.view',
            'purchasing.supplier.view',
            'purchasing.purchase.view',
            'purchasing.purchase.approve',
            'approval.inbox.view',
            'inventory.stock.view',
            'inventory.adjustment.create',
            'inventory.adjustment.approve',
            'inventory.opname.create',
            'inventory.opname.approve',
            'inventory.transfer.create',
            'inventory.transfer.approve',
            'inventory.transfer.dispatch',
            'inventory.transfer.receive',
            'notifications.view',
        ]);

        $this->syncRolePermissions('regional_warehouse', [
            'inventory.stock.view',
            'inventory.adjustment.create',
            'inventory.adjustment.approve',
            'inventory.opname.create',
            'inventory.opname.approve',
            'inventory.transfer.create',
            'inventory.transfer.approve',
            'inventory.transfer.dispatch',
            'inventory.transfer.receive',
            'purchasing.purchase.view',
            'purchasing.purchase.receive',
            'notifications.view',
        ]);

        $this->syncRolePermissions('warehouse_staff', [
            'inventory.stock.view',
            'inventory.adjustment.create',
            'inventory.adjustment.approve',
            'inventory.opname.create',
            'inventory.opname.approve',
            'inventory.transfer.create',
            'inventory.transfer.approve',
            'inventory.transfer.dispatch',
            'inventory.transfer.receive',
            'purchasing.purchase.view',
            'purchasing.purchase.receive',
            'notifications.view',
        ]);

        $this->syncRolePermissions('global_finance', [
            'holding.dashboard.view',
            'approval.inbox.view',
            'notifications.view',
            'purchasing.purchase.view',
            'finance.payment.approve',
            'tax.report.export',
        ]);

        $this->syncRolePermissions('global_it', [
            'holding.dashboard.view',
            'notifications.view',
            'it.user.manage',
            'it.role.manage',
            'audit.log.view',
        ]);

        $this->syncRolePermissions('global_audit', [
            'holding.dashboard.view',
            'notifications.view',
            'audit.log.view',
        ]);
    }

    /**
     * @param array<int, string> $permissionCodes
     */
    private function syncRolePermissions(string $roleCode, array $permissionCodes): void
    {
        $role = Role::query()->where('code', $roleCode)->firstOrFail();
        $permissionIds = Permission::query()->whereIn('code', $permissionCodes)->pluck('id');

        $role->permissions()->sync($permissionIds);
    }
}










