<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Module Providers
    |--------------------------------------------------------------------------
    |
    | Every bounded context registers itself here. Keeping the registry explicit
    | makes the modular monolith predictable in production and simple to audit.
    |
    */
    'providers' => [
        Modules\Holding\Providers\HoldingServiceProvider::class,
        Modules\Inventory\Providers\InventoryServiceProvider::class,
        Modules\Warehouse\Providers\WarehouseServiceProvider::class,
        Modules\Purchasing\Providers\PurchasingServiceProvider::class,
        Modules\Distribution\Providers\DistributionServiceProvider::class,
        Modules\Delivery\Providers\DeliveryServiceProvider::class,
        Modules\Finance\Providers\FinanceServiceProvider::class,
        Modules\Tax\Providers\TaxServiceProvider::class,
        Modules\POS\Providers\POSServiceProvider::class,
        Modules\Vinz\Providers\VinzServiceProvider::class,
        Modules\SateMerah\Providers\SateMerahServiceProvider::class,
        Modules\Shalimar\Providers\ShalimarServiceProvider::class,
        Modules\HRD\Providers\HRDServiceProvider::class,
        Modules\Legal\Providers\LegalServiceProvider::class,
        Modules\IT\Providers\ITServiceProvider::class,
        Modules\Audit\Providers\AuditServiceProvider::class,
        Modules\Reports\Providers\ReportsServiceProvider::class,
        Modules\Notifications\Providers\NotificationsServiceProvider::class,
        Modules\Analytics\Providers\AnalyticsServiceProvider::class,
    ],
];
