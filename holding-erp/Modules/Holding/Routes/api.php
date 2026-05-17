<?php

use Illuminate\Support\Facades\Route;

Route::prefix('holding')
    ->name('api.holding.')
    ->group(function (): void {
        Route::get('/health', fn () => ['module' => 'holding', 'status' => 'ok'])->name('health');
    });
