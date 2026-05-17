<?php

namespace Modules\Inventory\Providers;

use App\Core\Support\Module\AbstractModuleServiceProvider;

class InventoryServiceProvider extends AbstractModuleServiceProvider
{
    protected function moduleName(): string
    {
        return 'Inventory';
    }
}
