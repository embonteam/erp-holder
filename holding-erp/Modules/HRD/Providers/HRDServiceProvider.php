<?php

namespace Modules\HRD\Providers;

use App\Core\Support\Module\AbstractModuleServiceProvider;

class HRDServiceProvider extends AbstractModuleServiceProvider
{
    protected function moduleName(): string
    {
        return 'HRD';
    }
}
