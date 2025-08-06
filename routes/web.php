<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

Route::group(['as'=>'admin.'], function() {
    Route::prefix('admin')->group(base_path('routes/admin.php'));
});

Route::group(['as'=>'agency.'], function() {
    Route::prefix('agency')->group(base_path('routes/agency.php'));
});

Route::group(['as'=>'employee.'], function() {
    Route::prefix('employee')->group(base_path('routes/employee.php'));
});

Route::group(['as'=>'manager.'], function() {
    Route::prefix('manager')->group(base_path('routes/manager.php'));
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
    }elseif (strpos($previousUrl, 'manager') !== false) {
        return redirect()->route('manager.login');
    }else {
        return redirect()->route('agency.login');
    }

    return view('default-login');
})->name('login');

Route::get('/deploy', function() {
    try {
        // Add code column to customers table
        Schema::table('customers', function (Blueprint $table) {
            if (!Schema::hasColumn('customers', 'code')) {
                $table->string('code')->unique()->nullable()->after('id');
            }
        });

        // Add code column to employees table
        Schema::table('employees', function (Blueprint $table) {
            if (!Schema::hasColumn('employees', 'code')) {
                $table->string('code')->unique()->nullable()->after('id');
            }
        });

        // Add code column to agencies table
        Schema::table('agencies', function (Blueprint $table) {
            if (!Schema::hasColumn('agencies', 'code')) {
                $table->string('code')->unique()->nullable()->after('id');
            }
        });

        // Add code column to managers table
        Schema::table('managers', function (Blueprint $table) {
            if (!Schema::hasColumn('managers', 'code')) {
                $table->string('code')->unique()->nullable()->after('id');
            }
        });

        // Add code column to family-members table
        Schema::table('family-members', function (Blueprint $table) {
            if (!Schema::hasColumn('family-members', 'code')) {
                $table->string('code')->unique()->nullable()->after('id');
            }
        });

        return "Code columns added successfully.";
     } catch (\Exception $e) {
        return "Error: " . $e->getMessage();
    }
});