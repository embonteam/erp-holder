<?php

namespace Modules\Purchasing\Providers;

use App\Core\Support\Module\AbstractModuleServiceProvider;

class PurchasingServiceProvider extends AbstractModuleServiceProvider
{
    protected function moduleName(): string
    {
        return 'Purchasing';
    }
}
