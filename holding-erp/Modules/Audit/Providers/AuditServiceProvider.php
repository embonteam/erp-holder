<?php

namespace Modules\Audit\Providers;

use App\Core\Support\Module\AbstractModuleServiceProvider;

class AuditServiceProvider extends AbstractModuleServiceProvider
{
    protected function moduleName(): string
    {
        return 'Audit';
    }
}
