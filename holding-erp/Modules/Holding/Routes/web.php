<?php

use Illuminate\Support\Facades\Route;
use Modules\Holding\Http\Controllers\HoldingDashboardController;

Route::prefix('holding')
    ->name('holding.')
    ->middleware(['auth', 'scope.context', 'permission:holding.dashboard.view'])
    ->group(function (): void {
        Route::get('/', HoldingDashboardController::class)->name('dashboard');
    });
