<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Agency\{AuthController, DashboardController, CustomerController};

Route::controller(AuthController::class)->group(function() {

    Route::get('login', 'showLoginForm')->name('login');

    Route::post('login', 'login')->name('login.post');

    Route::get('logout', 'logout')->name('logout')->middleware(['auth:agency']);

});

Route::controller(DashboardController::class)->group(function() {

    Route::get('/', 'index')->name('dashboard')->middleware(['auth:agency']);

    Route::post('change-password', 'changePassword')->name('change.password')->middleware(['auth:agency']);

});

Route::group(['middleware' => ['auth:agency']], function() {

    Route::resource('customers', CustomerController::class);

});
