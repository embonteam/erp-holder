<?php

namespace Modules\Notifications\Providers;

use App\Core\Support\Module\AbstractModuleServiceProvider;

class NotificationsServiceProvider extends AbstractModuleServiceProvider
{
    protected function moduleName(): string
    {
        return 'Notifications';
    }
}
