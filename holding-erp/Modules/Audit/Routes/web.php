<?php

use Illuminate\Support\Facades\Route;
use Modules\Audit\Http\Controllers\ActivityLogController;

Route::prefix('audit')
    ->name('audit.')
    ->middleware(['auth', 'permission:audit.log.view'])
    ->group(function (): void {
        Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
        Route::get('/activity-logs/{activityLog}', [ActivityLogController::class, 'show'])->name('activity-logs.show');
    });
