<?php

namespace Modules\Reports\Providers;

use App\Core\Support\Module\AbstractModuleServiceProvider;

class ReportsServiceProvider extends AbstractModuleServiceProvider
{
    protected function moduleName(): string
    {
        return 'Reports';
    }
}
