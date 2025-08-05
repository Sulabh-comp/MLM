<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\{AuthController, DashboardController, EmployeeController, RolePermissionController, AgencyController, CustomerController, FamilyMemberController, ManagerController, RegionController};

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

    Route::put('managers/updateStatus', ManagerController::class . '@updateStatus')->name('managers.updateStatus');

    Route::resource('managers', ManagerController::class);

    Route::put('regions/updateStatus', RegionController::class . '@updateStatus')->name('regions.updateStatus');

    Route::resource('regions', RegionController::class);

    Route::resource('roles-permissions', RolePermissionController::class);

    Route::put('agencies/updateStatus', AgencyController::class . '@updateStatus')->name('agencies.updateStatus');
    Route::post('agencies/{agency}/verify-documents', [AgencyController::class, 'verifyDocuments'])->name('agencies.verify-documents');
    Route::get('agencies/export', AgencyController::class . '@export')->name('agencies.export');

    Route::resource('agencies', AgencyController::class);

    Route::put('customers/updateStatus', [CustomerController::class, 'updateStatus'])->name('customers.updateStatus');
    Route::get('customers/export', [CustomerController::class, 'export'])->name('customers.export');
    Route::get('customers/{customer}/export-family-members', [CustomerController::class, 'exportFamilyMembers'])->name('customers.export-family-members');
    Route::get('customers/export-all-family-members', [CustomerController::class, 'exportAllFamilyMembers'])->name('customers.export-all-family-members');

    Route::resource('customers', CustomerController::class);

    Route::put('family-members/updateStatus', [FamilyMemberController::class, 'updateStatus'])->name('family-members.updateStatus');

    Route::resource('family-members', FamilyMemberController::class);

    Route::resource('notifications', NotificationController::class);

});
