<?php

use Illuminate\Support\Facades\Route;
use Modules\Purchasing\Http\Controllers\PurchaseController;
use Modules\Purchasing\Http\Controllers\SupplierController;

Route::prefix('purchasing')
    ->name('purchasing.')
    ->middleware(['auth', 'scope.context'])
    ->group(function (): void {
        Route::middleware('permission:purchasing.purchase.view')->group(function (): void {
            Route::get('/purchases', [PurchaseController::class, 'index'])->name('purchases.index');
            Route::get('/purchases/create', [PurchaseController::class, 'create'])->name('purchases.create');
            Route::post('/purchases', [PurchaseController::class, 'store'])->name('purchases.store');
            Route::get('/purchases/{purchase}', [PurchaseController::class, 'show'])->name('purchases.show');
            Route::post('/purchases/{purchase}/approve', [PurchaseController::class, 'approve'])->name('purchases.approve');
            Route::post('/purchases/{purchase}/receive', [PurchaseController::class, 'receive'])->name('purchases.receive');
        });

        Route::middleware('permission:purchasing.supplier.view')->group(function (): void {
            Route::get('/suppliers', [SupplierController::class, 'index'])->name('suppliers.index');
            Route::get('/suppliers/create', [SupplierController::class, 'create'])->name('suppliers.create');
            Route::post('/suppliers', [SupplierController::class, 'store'])->name('suppliers.store');
            Route::get('/suppliers/{supplier}', [SupplierController::class, 'show'])->name('suppliers.show');
            Route::get('/suppliers/{supplier}/edit', [SupplierController::class, 'edit'])->name('suppliers.edit');
            Route::put('/suppliers/{supplier}', [SupplierController::class, 'update'])->name('suppliers.update');
            Route::post('/suppliers/{supplier}/deactivate', [SupplierController::class, 'deactivate'])->name('suppliers.deactivate');
            Route::post('/suppliers/{supplier}/reactivate', [SupplierController::class, 'reactivate'])->name('suppliers.reactivate');
        });
    });
