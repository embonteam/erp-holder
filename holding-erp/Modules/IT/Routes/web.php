<?php

use Illuminate\Support\Facades\Route;
use Modules\IT\Http\Controllers\RoleController;
use Modules\IT\Http\Controllers\UserController;

Route::prefix('it')
    ->name('it.')
    ->middleware(['auth', 'scope.context'])
    ->group(function (): void {
        Route::middleware('permission:it.user.manage')->group(function (): void {
            Route::get('/users', [UserController::class, 'index'])->name('users.index');
            Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
            Route::post('/users', [UserController::class, 'store'])->name('users.store');
            Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
            Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
            Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
            Route::post('/users/{user}/deactivate', [UserController::class, 'deactivate'])->name('users.deactivate');
            Route::post('/users/{user}/reactivate', [UserController::class, 'reactivate'])->name('users.reactivate');
        });

        Route::middleware('permission:it.role.manage')->group(function (): void {
            Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
            Route::get('/roles/create', [RoleController::class, 'create'])->name('roles.create');
            Route::post('/roles', [RoleController::class, 'store'])->name('roles.store');
            Route::get('/roles/{role}', [RoleController::class, 'show'])->name('roles.show');
            Route::get('/roles/{role}/edit', [RoleController::class, 'edit'])->name('roles.edit');
            Route::put('/roles/{role}', [RoleController::class, 'update'])->name('roles.update');
        });
    });
