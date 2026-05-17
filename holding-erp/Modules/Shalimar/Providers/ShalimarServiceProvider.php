<?php

namespace Modules\Shalimar\Providers;

use App\Core\Support\Module\AbstractModuleServiceProvider;

class ShalimarServiceProvider extends AbstractModuleServiceProvider
{
    protected function moduleName(): string
    {
        return 'Shalimar';
    }
}
