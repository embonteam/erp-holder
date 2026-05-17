<?php

namespace Modules\Analytics\Providers;

use App\Core\Support\Module\AbstractModuleServiceProvider;

class AnalyticsServiceProvider extends AbstractModuleServiceProvider
{
    protected function moduleName(): string
    {
        return 'Analytics';
    }
}
