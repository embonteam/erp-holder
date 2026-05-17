<?php

namespace Modules\Delivery\Providers;

use App\Core\Support\Module\AbstractModuleServiceProvider;

class DeliveryServiceProvider extends AbstractModuleServiceProvider
{
    protected function moduleName(): string
    {
        return 'Delivery';
    }
}
