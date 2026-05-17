<?php

namespace Modules\Distribution\Providers;

use App\Core\Support\Module\AbstractModuleServiceProvider;

class DistributionServiceProvider extends AbstractModuleServiceProvider
{
    protected function moduleName(): string
    {
        return 'Distribution';
    }
}
