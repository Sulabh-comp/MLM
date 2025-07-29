<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Manager\{AuthController, DashboardController, EmployeeController, AgencyController, CustomerController, RegionController, NotificationController};

Route::controller(AuthController::class)->group(function() {

    Route::get('login', 'showLoginForm')->name('login');

    Route::post('login', 'login')->name('login.post');

    Route::get('logout', 'logout')->name('logout')->middleware(['auth:manager']);

});

Route::controller(DashboardController::class)->group(function() {

    Route::get('/', 'index')->name('dashboard')->middleware(['auth:manager']);

    Route::post('change-password', 'changePassword')->name('change.password')->middleware(['auth:manager']);

});

Route::group(['middleware' => ['auth:manager']], function() {

    Route::resource('employees', EmployeeController::class);

    Route::put('employees/updateStatus', EmployeeController::class . '@updateStatus')->name('employees.updateStatus');

    Route::resource('agencies', AgencyController::class);

    Route::put('agencies/updateStatus', AgencyController::class . '@updateStatus')->name('agencies.updateStatus');

    Route::resource('customers', CustomerController::class);

    Route::put('customers/updateStatus', [CustomerController::class, 'updateStatus'])->name('customers.updateStatus');

    Route::resource('regions', RegionController::class)->only(['index', 'show']);

    Route::resource('notifications', NotificationController::class);

});
