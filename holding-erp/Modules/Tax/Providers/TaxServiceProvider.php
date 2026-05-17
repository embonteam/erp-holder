<?php

namespace Modules\Tax\Providers;

use App\Core\Support\Module\AbstractModuleServiceProvider;

class TaxServiceProvider extends AbstractModuleServiceProvider
{
    protected function moduleName(): string
    {
        return 'Tax';
    }
}
