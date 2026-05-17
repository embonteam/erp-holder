<?php

namespace Modules\SateMerah\Providers;

use App\Core\Support\Module\AbstractModuleServiceProvider;

class SateMerahServiceProvider extends AbstractModuleServiceProvider
{
    protected function moduleName(): string
    {
        return 'SateMerah';
    }
}
