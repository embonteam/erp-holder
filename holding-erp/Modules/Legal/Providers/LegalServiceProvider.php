<?php

namespace Modules\Legal\Providers;

use App\Core\Support\Module\AbstractModuleServiceProvider;

class LegalServiceProvider extends AbstractModuleServiceProvider
{
    protected function moduleName(): string
    {
        return 'Legal';
    }
}
