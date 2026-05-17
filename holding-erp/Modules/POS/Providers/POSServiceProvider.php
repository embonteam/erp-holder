<?php

namespace Modules\POS\Providers;

use App\Core\Support\Module\AbstractModuleServiceProvider;

class POSServiceProvider extends AbstractModuleServiceProvider
{
    protected function moduleName(): string
    {
        return 'POS';
    }
}
