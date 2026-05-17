<?php

namespace Modules\Holding\Providers;

use App\Core\Support\Module\AbstractModuleServiceProvider;

class HoldingServiceProvider extends AbstractModuleServiceProvider
{
    protected function moduleName(): string
    {
        return 'Holding';
    }
}
