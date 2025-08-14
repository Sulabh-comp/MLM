#!/usr/bin/env php
<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use App\Models\Employee;
use App\Models\Manager;
use App\Models\Region;
use Illuminate\Support\Facades\DB;

// Bootstrap Laravel
$app = require_once __DIR__ . '/../../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== MLM Hierarchy Migration: Region-to-Manager Employee Assignment ===\n\n";

try {
    DB::beginTransaction();
    
    // Step 1: Get all employees without manager assignments
    $employeesWithoutManager = Employee::whereNull('manager_id')->get();
    echo "Found {$employeesWithoutManager->count()} employees without manager assignment\n";
    
    // Step 2: Get region-manager mappings
    $regionManagerMappings = [];
    $managers = Manager::with('region')->get();
    
    foreach ($managers as $manager) {
        if ($manager->region_id) {
            if (!isset($regionManagerMappings[$manager->region_id])) {
                $regionManagerMappings[$manager->region_id] = [];
            }
            $regionManagerMappings[$manager->region_id][] = $manager;
        }
    }
    
    echo "Region-Manager mappings found for " . count($regionManagerMappings) . " regions\n";
    
    // Step 3: Assign employees to managers based on region
    $assignedCount = 0;
    $unassignedCount = 0;
    $errors = [];
    
    foreach ($employeesWithoutManager as $employee) {
        if ($employee->region_id && isset($regionManagerMappings[$employee->region_id])) {
            $availableManagers = $regionManagerMappings[$employee->region_id];
            
            // Find the most suitable manager (prefer lowest hierarchy level)
            $selectedManager = collect($availableManagers)->sortBy('level')->first();
            
            if ($selectedManager) {
                $employee->manager_id = $selectedManager->id;
                $employee->save();
                
                echo "✓ Assigned {$employee->name} to manager {$selectedManager->name} (Region: {$selectedManager->region->name})\n";
                $assignedCount++;
            } else {
                echo "✗ No suitable manager found for {$employee->name} in region {$employee->region->name}\n";
                $errors[] = "Employee {$employee->name} (ID: {$employee->id}) - No suitable manager in region {$employee->region->name}";
                $unassignedCount++;
            }
        } else {
            $regionName = $employee->region ? $employee->region->name : 'Unknown';
            echo "✗ No manager mapping for {$employee->name} in region {$regionName}\n";
            $errors[] = "Employee {$employee->name} (ID: {$employee->id}) - No manager mapping for region {$regionName}";
            $unassignedCount++;
        }
    }
    
    // Step 4: Validate hierarchy relationships
    echo "\n=== Validation Phase ===\n";
    $allEmployees = Employee::with('manager')->get();
    $validationErrors = [];
    
    foreach ($allEmployees as $employee) {
        if ($employee->manager_id) {
            $manager = $employee->manager;
            if (!$manager) {
                $validationErrors[] = "Employee {$employee->name} has invalid manager_id: {$employee->manager_id}";
            } else {
                // Check if manager has hierarchy access to employee
                if ($manager->region_id && $employee->region_id && $manager->region_id !== $employee->region_id) {
                    $validationErrors[] = "Employee {$employee->name} assigned to manager {$manager->name} from different region";
                }
            }
        }
    }
    
    if (empty($validationErrors)) {
        echo "✓ All hierarchy relationships are valid\n";
    } else {
        echo "✗ Found " . count($validationErrors) . " validation errors:\n";
        foreach ($validationErrors as $error) {
            echo "  - {$error}\n";
        }
    }
    
    // Step 5: Generate summary report
    echo "\n=== Migration Summary ===\n";
    echo "Total employees processed: " . $employeesWithoutManager->count() . "\n";
    echo "Successfully assigned: {$assignedCount}\n";
    echo "Unassigned (manual intervention needed): {$unassignedCount}\n";
    echo "Validation errors: " . count($validationErrors) . "\n";
    
    if (!empty($errors)) {
        echo "\n=== Employees requiring manual assignment ===\n";
        foreach ($errors as $error) {
            echo "- {$error}\n";
        }
    }
    
    // Step 6: Show hierarchy statistics
    echo "\n=== Current Hierarchy Statistics ===\n";
    $managersWithEmployees = Manager::withCount('directEmployees')->get();
    foreach ($managersWithEmployees as $manager) {
        $hierarchyCount = $manager->allSubordinateEmployees()->count();
        echo "Manager: {$manager->name} - Direct: {$manager->direct_employees_count}, Total in Hierarchy: {$hierarchyCount}\n";
    }
    
    // Ask for confirmation
    if ($assignedCount > 0) {
        echo "\nDo you want to commit these changes? (y/N): ";
        $handle = fopen("php://stdin", "r");
        $line = fgets($handle);
        fclose($handle);
        
        if (trim(strtolower($line)) === 'y') {
            DB::commit();
            echo "✓ Migration committed successfully!\n";
        } else {
            DB::rollBack();
            echo "✗ Migration rolled back.\n";
        }
    } else {
        DB::rollBack();
        echo "No changes to commit.\n";
    }
    
} catch (Exception $e) {
    DB::rollBack();
    echo "✗ Migration failed: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n=== Migration Complete ===\n";
