<?php

namespace App\Core\Support\Module;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

abstract class AbstractModuleServiceProvider extends ServiceProvider
{
    abstract protected function moduleName(): string;

    public function boot(): void
    {
        $basePath = base_path('Modules/'.$this->moduleName());

        if (is_dir($basePath.'/Database/Migrations')) {
            $this->loadMigrationsFrom($basePath.'/Database/Migrations');
        }

        if (is_dir($basePath.'/Resources/views')) {
            $this->loadViewsFrom($basePath.'/Resources/views', strtolower($this->moduleName()));
        }

        if (file_exists($basePath.'/Routes/web.php')) {
            Route::middleware('web')
                ->group($basePath.'/Routes/web.php');
        }

        if (file_exists($basePath.'/Routes/api.php')) {
            Route::prefix('api/v1')
                ->middleware('api')
                ->group($basePath.'/Routes/api.php');
        }
    }
}
