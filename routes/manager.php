<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Manager\NotificationController;
use App\Http\Controllers\Manager\{AuthController, DashboardController, EmployeeController, AgentController, CustomerController, FamilyMemberController};

Route::controller(AuthController::class)->group(function() {

    Route::get('login', 'showLoginForm')->name('login');

    Route::post('login', 'login')->name('login.post');

    Route::get('logout', 'logout')->name('logout')->middleware(['auth:manager']);

});

Route::controller(DashboardController::class)->group(function() {

    Route::get('/', 'index')->name('dashboard')->middleware(['auth:manager']);

});

Route::controller(EmployeeController::class)->group(function() {

    Route::get('employees', 'index')->name('employees')->middleware(['auth:manager']);

    Route::get('employees/create', 'create')->name('employees.create')->middleware(['auth:manager']);

    Route::post('employees', 'store')->name('employees.store')->middleware(['auth:manager']);

    Route::get('employees/{employee}', 'show')->name('employees.show')->middleware(['auth:manager']);

    Route::get('employees/{employee}/edit', 'edit')->name('employees.edit')->middleware(['auth:manager']);

    Route::put('employees/{employee}', 'update')->name('employees.update')->middleware(['auth:manager']);

    Route::delete('employees/{employee}', 'destroy')->name('employees.destroy')->middleware(['auth:manager']);

    Route::post('employees/{employee}/toggle-status', 'toggleStatus')->name('employees.toggle-status')->middleware(['auth:manager']);

});

Route::controller(AgentController::class)->group(function() {

    Route::get('agencies', 'index')->name('agencies')->middleware(['auth:manager']);

    Route::get('agencies/{agency}', 'show')->name('agencies.show')->middleware(['auth:manager']);

});

Route::controller(CustomerController::class)->group(function() {

    Route::get('customers', 'index')->name('customers')->middleware(['auth:manager']);

    Route::get('customers/{customer}', 'show')->name('customers.show')->middleware(['auth:manager']);

});

Route::controller(FamilyMemberController::class)->group(function() {

    Route::get('family-members', 'index')->name('family-members')->middleware(['auth:manager']);

    Route::get('family-members/{familyMember}', 'show')->name('family-members.show')->middleware(['auth:manager']);

});

Route::controller(NotificationController::class)->group(function() {

    Route::get('notifications', 'index')->name('notifications')->middleware(['auth:manager']);

    Route::post('notifications/{notification}/mark-as-read', 'markAsRead')->name('notifications.mark-as-read')->middleware(['auth:manager']);

    Route::post('notifications/mark-all-as-read', 'markAllAsRead')->name('notifications.mark-all-as-read')->middleware(['auth:manager']);

});
