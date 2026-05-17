<?php

return [
    'items' => [
        [
            'label' => 'Holding Dashboard',
            'route' => 'holding.dashboard',
            'active' => 'holding.*',
            'permission' => 'holding.dashboard.view',
        ],
        [
            'label' => 'Approval Inbox',
            'route' => 'approvals.index',
            'active' => 'approvals.*',
            'permission' => 'approval.inbox.view',
        ],
        [
            'label' => 'Notifications',
            'route' => 'notifications.index',
            'active' => 'notifications.*',
            'permission' => 'notifications.view',
        ],
        [
            'label' => 'Purchase Orders',
            'route' => 'purchasing.purchases.index',
            'active' => 'purchasing.purchases.*',
            'permission' => 'purchasing.purchase.view',
        ],
        [
            'label' => 'Suppliers',
            'route' => 'purchasing.suppliers.index',
            'active' => 'purchasing.suppliers.*',
            'permission' => 'purchasing.supplier.view',
        ],
        [
            'label' => 'Warehouse & Inventory',
            'route' => 'inventory.dashboard',
            'active' => 'inventory.*',
            'permission' => 'inventory.stock.view',
        ],
        [
            'label' => 'Stock Adjustments',
            'route' => 'inventory.adjustments.index',
            'active' => 'inventory.adjustments.*',
            'permission' => 'inventory.stock.view',
        ],
        [
            'label' => 'Stock Opname',
            'route' => 'inventory.opnames.index',
            'active' => 'inventory.opnames.*',
            'permission' => 'inventory.stock.view',
        ],
        [
            'label' => 'Warehouse Transfers',
            'route' => 'inventory.transfers.index',
            'active' => 'inventory.transfers.*',
            'permission' => 'inventory.stock.view',
        ],
        [
            'label' => 'User Management',
            'route' => 'it.users.index',
            'active' => 'it.users.*',
            'permission' => 'it.user.manage',
        ],
        [
            'label' => 'Roles & Permissions',
            'route' => 'it.roles.index',
            'active' => 'it.roles.*',
            'permission' => 'it.role.manage',
        ],
        [
            'label' => 'Audit Logs',
            'route' => 'audit.activity-logs.index',
            'active' => 'audit.*',
            'permission' => 'audit.log.view',
        ],
    ],
];



