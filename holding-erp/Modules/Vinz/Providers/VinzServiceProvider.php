<?php

namespace Modules\Vinz\Providers;

use App\Core\Support\Module\AbstractModuleServiceProvider;

class VinzServiceProvider extends AbstractModuleServiceProvider
{
    protected function moduleName(): string
    {
        return 'Vinz';
    }
}
