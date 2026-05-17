<?php

namespace Modules\Warehouse\Providers;

use App\Core\Support\Module\AbstractModuleServiceProvider;

class WarehouseServiceProvider extends AbstractModuleServiceProvider
{
    protected function moduleName(): string
    {
        return 'Warehouse';
    }
}
