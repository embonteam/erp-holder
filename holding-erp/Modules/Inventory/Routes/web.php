<?php

use Illuminate\Support\Facades\Route;
use Modules\Inventory\Http\Controllers\InventoryDashboardController;
use Modules\Inventory\Http\Controllers\StockAdjustmentController;
use Modules\Inventory\Http\Controllers\StockOpnameController;
use Modules\Inventory\Http\Controllers\StockTransferController;

Route::prefix('inventory')
    ->name('inventory.')
    ->middleware(['auth', 'scope.context'])
    ->group(function (): void {
        Route::middleware('permission:inventory.stock.view')->group(function (): void {
            Route::get('/', InventoryDashboardController::class)->name('dashboard');
            Route::get('/adjustments', [StockAdjustmentController::class, 'index'])->name('adjustments.index');
            Route::get('/adjustments/create', [StockAdjustmentController::class, 'create'])->name('adjustments.create');
            Route::post('/adjustments', [StockAdjustmentController::class, 'store'])->name('adjustments.store');
            Route::get('/adjustments/{adjustment}', [StockAdjustmentController::class, 'show'])->name('adjustments.show');
            Route::post('/adjustments/{adjustment}/approve', [StockAdjustmentController::class, 'approve'])->name('adjustments.approve');

            Route::get('/opnames', [StockOpnameController::class, 'index'])->name('opnames.index');
            Route::get('/opnames/create', [StockOpnameController::class, 'create'])->name('opnames.create');
            Route::post('/opnames', [StockOpnameController::class, 'store'])->name('opnames.store');
            Route::get('/opnames/{opname}', [StockOpnameController::class, 'show'])->name('opnames.show');
            Route::post('/opnames/{opname}/approve', [StockOpnameController::class, 'approve'])->name('opnames.approve');

            Route::get('/transfers', [StockTransferController::class, 'index'])->name('transfers.index');
            Route::get('/transfers/create', [StockTransferController::class, 'create'])->name('transfers.create');
            Route::post('/transfers', [StockTransferController::class, 'store'])->name('transfers.store');
            Route::get('/transfers/{transfer}', [StockTransferController::class, 'show'])->name('transfers.show');
            Route::post('/transfers/{transfer}/approve', [StockTransferController::class, 'approve'])->name('transfers.approve');
            Route::post('/transfers/{transfer}/dispatch', [StockTransferController::class, 'dispatch'])->name('transfers.dispatch');
            Route::post('/transfers/{transfer}/receive', [StockTransferController::class, 'receive'])->name('transfers.receive');
        });
    });
