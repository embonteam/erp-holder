<?php

use Illuminate\Support\Facades\Route;
use Modules\Notifications\Http\Controllers\NotificationController;

Route::prefix('notifications')
    ->name('notifications.')
    ->middleware(['auth', 'permission:notifications.view'])
    ->group(function (): void {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::post('/mark-all-read', [NotificationController::class, 'markAllRead'])->name('mark-all-read');
        Route::post('/{notification}/read', [NotificationController::class, 'markRead'])->name('mark-read');
    });
