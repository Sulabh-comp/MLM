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
        // Create regions table
        try {
        \DB::statement("
            CREATE TABLE IF NOT EXISTS `regions` (
                `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                `name` varchar(255) NOT NULL,
                `code` varchar(255) NOT NULL,
                `description` text DEFAULT NULL,
                `states` json DEFAULT NULL,
                `status` tinyint(4) NOT NULL DEFAULT 1,
                `created_at` timestamp NULL DEFAULT NULL,
                `updated_at` timestamp NULL DEFAULT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `regions_name_unique` (`name`),
                UNIQUE KEY `regions_code_unique` (`code`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        } catch (\Exception $e) {
            // Table might already exist
            echo "Regions table might already exist: " . $e->getMessage();
        }

        // Create managers table
        try {
            \DB::statement("
                CREATE TABLE IF NOT EXISTS `managers` (
                    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                    `name` varchar(255) NOT NULL,
                    `email` varchar(255) NOT NULL,
                    `phone` varchar(255) NOT NULL,
                    `designation` varchar(255) NOT NULL,
                    `region_id` bigint(20) unsigned NOT NULL,
                    `password` varchar(255) NOT NULL,
                    `last_notification_read_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    `status` tinyint(4) NOT NULL DEFAULT 1,
                    `created_at` timestamp NULL DEFAULT NULL,
                    `updated_at` timestamp NULL DEFAULT NULL,
                    PRIMARY KEY (`id`),
                    UNIQUE KEY `managers_email_unique` (`email`),
                    KEY `managers_region_id_foreign` (`region_id`),
                    CONSTRAINT `managers_region_id_foreign` FOREIGN KEY (`region_id`) REFERENCES `regions` (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ");
        } catch (\Exception $e) {
            // Table might already exist
            echo "Managers table might already exist: " . $e->getMessage();
        }

        // Add financial details to agencies table
        try {
            \DB::statement("
                ALTER TABLE `agencies` 
                ADD COLUMN `bank_name` varchar(255) NULL AFTER `status`,
                ADD COLUMN `account_holder_name` varchar(255) NULL AFTER `bank_name`,
                ADD COLUMN `account_number` varchar(255) NULL AFTER `account_holder_name`,
                ADD COLUMN `ifsc_code` varchar(255) NULL AFTER `account_number`,
                ADD COLUMN `branch_name` varchar(255) NULL AFTER `ifsc_code`,
                ADD COLUMN `aadhar_number` varchar(255) NULL AFTER `branch_name`,
                ADD COLUMN `pan_number` varchar(255) NULL AFTER `aadhar_number`,
                ADD COLUMN `documents_verified` tinyint(4) DEFAULT 0 AFTER `pan_number`,
                ADD COLUMN `documents_submitted_at` timestamp NULL AFTER `documents_verified`
            ");
        } catch (\Exception $e) {
            // Columns might already exist
        }

        // Alter employees table to add region support
        try {
            \DB::statement("
                ALTER TABLE `employees` 
                ADD COLUMN `region_id` bigint(20) unsigned NULL AFTER `designation`
            ");
        } catch (\Exception $e) {
            // Column might already exist
        }

        try {
            \DB::statement("
                ALTER TABLE `employees` 
                ADD KEY `employees_region_id_foreign` (`region_id`)
            ");
        } catch (\Exception $e) {
            // Key might already exist
        }

        try {
            \DB::statement("
                ALTER TABLE `employees` 
                ADD CONSTRAINT `employees_region_id_foreign` FOREIGN KEY (`region_id`) REFERENCES `regions` (`id`)
            ");
        } catch (\Exception $e) {
            // Constraint might already exist
        }

        // Add financial details to agencies table with error handling
        try {
            DB::statement("
                ALTER TABLE `agencies` 
                ADD COLUMN `bank_name` varchar(255) NULL AFTER `status`,
                ADD COLUMN `account_holder_name` varchar(255) NULL AFTER `bank_name`,
                ADD COLUMN `account_number` varchar(255) NULL AFTER `account_holder_name`,
                ADD COLUMN `ifsc_code` varchar(255) NULL AFTER `account_number`,
                ADD COLUMN `branch_name` varchar(255) NULL AFTER `ifsc_code`,
                ADD COLUMN `aadhar_number` varchar(255) NULL AFTER `branch_name`,
                ADD COLUMN `pan_number` varchar(255) NULL AFTER `aadhar_number`,
                ADD COLUMN `documents_verified` tinyint(4) DEFAULT 0 AFTER `pan_number`,
                ADD COLUMN `documents_submitted_at` timestamp NULL AFTER `documents_verified`
            ");
        } catch (\Exception $e) {
            // Columns might already exist
            echo "Financial columns might already exist: " . $e->getMessage();
        }

        return "Tables created and updated successfully!";
    } catch (\Exception $e) {
        return "Error: " . $e->getMessage();
    }
});