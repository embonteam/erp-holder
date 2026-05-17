<?php

namespace Modules\Finance\Providers;

use App\Core\Support\Module\AbstractModuleServiceProvider;

class FinanceServiceProvider extends AbstractModuleServiceProvider
{
    protected function moduleName(): string
    {
        return 'Finance';
    }
}
