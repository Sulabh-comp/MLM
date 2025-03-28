<?php

use Illuminate\Support\Facades\Route;

Route::group(['as'=>'admin.'], function() {
    Route::prefix('admin')->group(base_path('routes/admin.php'));
});

Route::group(['as'=>'agency.'], function() {
    Route::prefix('agency')->group(base_path('routes/agency.php'));
});

Route::group(['as'=>'employee.'], function() {
    Route::prefix('employee')->group(base_path('routes/employee.php'));
});

Route::get('/login', function() {
    // check the previous URL
    $previousUrl = url()->previous();
    $previousUrl = str_replace(url('/'), '', $previousUrl);

    // check if the previous URL is from the admin
    if (strpos($previousUrl, 'admin') !== false) {
        return redirect()->route('admin.login');
    }elseif (strpos($previousUrl, 'employee') !== false) {
        return redirect()->route('employee.login');
    }else {
        return redirect()->route('agency.login');
    }

    return view('default-login');
})->name('login');