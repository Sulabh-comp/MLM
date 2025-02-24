<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\{AuthController, DashboardController, EmployeeController, RolePermissionController, AgencyController, CustomerController};

Route::controller(AuthController::class)->group(function() {

    Route::get('login', 'showLoginForm')->name('login');

    Route::post('login', 'login')->name('login.post');

    Route::get('logout', 'logout')->name('logout')->middleware(['auth:admin']);

});

Route::controller(DashboardController::class)->group(function() {

    Route::get('/', 'index')->name('dashboard')->middleware(['auth:admin']);

    Route::post('change-password', 'changePassword')->name('change.password')->middleware(['auth:admin']);

});

Route::group(['middleware' => ['auth:admin']], function() {

    Route::put('employees/updateStatus', EmployeeController::class . '@updateStatus')->name('employees.updateStatus');

    Route::resource('employees', EmployeeController::class);

    Route::resource('roles-permissions', RolePermissionController::class);

    Route::put('agencies/updateStatus', AgencyController::class . '@updateStatus')->name('agencies.updateStatus');

    Route::resource('agencies', AgencyController::class);

    Route::resource('customers', CustomerController::class);

});
