<?php

namespace App\Providers;

use App\Core\Navigation\MenuBuilder;
use App\Core\Support\Scoping\ScopeContext;
use App\Models\User;
use App\Policies\PurchasePolicy;
use App\Policies\RolePolicy;
use App\Policies\StockAdjustmentPolicy;
use App\Policies\StockOpnamePolicy;
use App\Policies\StockTransferPolicy;
use App\Policies\SupplierPolicy;
use App\Policies\UserPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Modules\Audit\Models\Role;
use Modules\Inventory\Models\StockAdjustment;
use Modules\Inventory\Models\StockOpname;
use Modules\Inventory\Models\StockTransfer;
use Modules\Purchasing\Models\Purchase;
use Modules\Purchasing\Models\Supplier;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(ScopeContext::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(Purchase::class, PurchasePolicy::class);
        Gate::policy(Supplier::class, SupplierPolicy::class);
        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(Role::class, RolePolicy::class);
        Gate::policy(StockAdjustment::class, StockAdjustmentPolicy::class);
        Gate::policy(StockOpname::class, StockOpnamePolicy::class);
        Gate::policy(StockTransfer::class, StockTransferPolicy::class);

        View::composer('layouts.app', function ($view): void {
            $view->with('navigation', app(MenuBuilder::class)->forUser(auth()->user()));
        });
    }
}
