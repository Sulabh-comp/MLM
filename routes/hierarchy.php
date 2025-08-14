<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HierarchyManagerController;

/*
|--------------------------------------------------------------------------
| Hierarchy Management Routes
|--------------------------------------------------------------------------
|
| These routes demonstrate the hierarchy-based access control system
| with middleware protection and role-based permissions.
|
*/

Route::prefix('hierarchy')->name('hierarchy.')->group(function () {
    
    // Dashboard route - accessible to all authenticated users
    Route::get('/dashboard', [HierarchyManagerController::class, 'dashboard'])
        ->middleware('hierarchy.access')
        ->name('dashboard');

    // Manager management routes with hierarchy access control
    Route::middleware(['hierarchy.access:manager'])->group(function () {
        
        // Manager CRUD operations
        Route::get('/managers', [HierarchyManagerController::class, 'index'])
            ->name('managers.index');
        
        Route::post('/managers', [HierarchyManagerController::class, 'store'])
            ->name('managers.store');
        
        Route::get('/managers/{manager}', [HierarchyManagerController::class, 'show'])
            ->name('managers.show');
        
        Route::put('/managers/{manager}', [HierarchyManagerController::class, 'update'])
            ->name('managers.update');
        
        Route::delete('/managers/{manager}', [HierarchyManagerController::class, 'destroy'])
            ->name('managers.destroy');
        
        // Manager hierarchy operations
        Route::get('/managers/{manager}/subordinates', [HierarchyManagerController::class, 'subordinates'])
            ->name('managers.subordinates');
        
        Route::get('/managers/{manager}/territory', [HierarchyManagerController::class, 'territory'])
            ->name('managers.territory');
        
        // Bulk operations
        Route::post('/managers/bulk-action', [HierarchyManagerController::class, 'bulkAction'])
            ->name('managers.bulk-action');
    });
    
    // Employee management routes (will be created later)
    Route::middleware(['hierarchy.access:employee'])->group(function () {
        // Employee routes will be added here
    });
    
    // Agency management routes (will be created later)
    Route::middleware(['hierarchy.access:agency'])->group(function () {
        // Agency routes will be added here
    });
    
    // Customer management routes (will be created later)
    Route::middleware(['hierarchy.access:customer'])->group(function () {
        // Customer routes will be added here
    });
});
