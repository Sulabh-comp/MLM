<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Employee\{AuthController, DashboardController, CustomerController, AgencyController, NotificationController};

Route::controller(AuthController::class)->group(function() {

    Route::get('login', 'showLoginForm')->name('login');

    Route::post('login', 'login')->name('login.post');

    Route::get('logout', 'logout')->name('logout')->middleware(['auth:employee']);

});

Route::controller(DashboardController::class)->group(function() {

    Route::get('/', 'index')->name('dashboard')->middleware(['auth:employee']);

    Route::post('change-password', 'changePassword')->name('change.password')->middleware(['auth:employee']);

});

Route::group(['middleware' => ['auth:employee']], function() {

    Route::resource('customers', CustomerController::class);

    Route::resource('agencies', AgencyController::class);

    Route::resource('notifications', NotificationController::class);

});
