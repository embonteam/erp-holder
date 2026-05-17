<?php

namespace Modules\IT\Providers;

use App\Core\Support\Module\AbstractModuleServiceProvider;

class ITServiceProvider extends AbstractModuleServiceProvider
{
    protected function moduleName(): string
    {
        return 'IT';
    }
}
