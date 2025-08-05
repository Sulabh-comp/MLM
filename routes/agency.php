<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Agency\{AuthController, DashboardController, CustomerController, FamilyMemberController, NotificationController};

Route::controller(AuthController::class)->group(function() {

    Route::get('login', 'showLoginForm')->name('login');

    Route::post('login', 'login')->name('login.post');

    Route::get('logout', 'logout')->name('logout')->middleware(['auth:agency']);

});

Route::controller(DashboardController::class)->group(function() {

    Route::get('/', 'index')->name('dashboard')->middleware(['auth:agency']);

    Route::post('change-password', 'changePassword')->name('change.password')->middleware(['auth:agency']);

    Route::get('profile', 'profile')->name('profile')->middleware(['auth:agency']);

    Route::post('profile', 'updateProfile')->name('profile.update')->middleware(['auth:agency']);

});

Route::group(['middleware' => ['auth:agency']], function() {

    Route::put('customers/updateStatus', [CustomerController::class, 'updateStatus'])->name('customers.updateStatus');

    Route::resource('customers', CustomerController::class);

    Route::put('family-members/updateStatus', [FamilyMemberController::class, 'updateStatus'])->name('family-members.updateStatus');

    Route::resource('family-members', FamilyMemberController::class);

    Route::resource('notifications', NotificationController::class);

});
