<?php

require_once __DIR__ . '/vendor/autoload.php';

// Load Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Manager;
use App\Models\Employee;
use App\Models\Agency;
use App\Models\Customer;

echo "=== Testing Hierarchy Relationships ===\n\n";

// Test Manager-Employee relationship with territorial access
echo "1. Testing Manager territorial access:\n";
$manager = Manager::with('employees')->first();

if ($manager) {
    echo "Manager: {$manager->name} (Level: {$manager->level_name})\n";
    echo "  - Direct Employees: " . $manager->employees()->count() . "\n";
    
    try {
        echo "  - Total Territorial Employees: " . $manager->allTerritorialEmployees()->count() . "\n";
        echo "  - Direct Agencies: " . $manager->agencies()->count() . "\n";
        echo "  - Total Territorial Agencies: " . $manager->allTerritorialAgencies()->count() . "\n";
        echo "  - Direct Customers: " . $manager->customers()->count() . "\n";
        echo "  - Total Territorial Customers: " . $manager->allTerritorialCustomers()->count() . "\n";
        
        $stats = $manager->getTerritorialStats();
        echo "  - Territorial Stats Summary:\n";
        foreach ($stats as $key => $value) {
            echo "    * " . str_replace('_', ' ', ucfirst($key)) . ": {$value}\n";
        }
    } catch (Exception $e) {
        echo "  - Error with territorial methods: " . $e->getMessage() . "\n";
    }
    echo "\n";
}

// Test Employee relationships
echo "2. Testing Employee access through hierarchy:\n";
$employee = Employee::with('manager')->first();

if ($employee) {
    echo "Employee: {$employee->name}\n";
    echo "  - Manager: " . ($employee->manager ? $employee->manager->name : 'None') . "\n";
    echo "  - Agencies: " . $employee->agencies()->count() . "\n";
    
    try {
        echo "  - Accessible Managers: " . $employee->accessibleManagers()->count() . "\n";
        echo "  - Customers: " . $employee->customers()->count() . "\n";
        
        $scope = $employee->getTerritorialScope();
        echo "  - Territorial Scope:\n";
        echo "    * Agencies: " . $scope['agencies']->count() . "\n";
        echo "    * Customers: " . $scope['customers']->count() . "\n";
        echo "    * Managers: " . $scope['managers']->count() . "\n";
    } catch (Exception $e) {
        echo "  - Error with employee methods: " . $e->getMessage() . "\n";
    }
    echo "\n";
}

// Test access control
echo "3. Testing access control:\n";
if ($manager && $employee) {
    $firstAgency = $employee->agencies()->first();
    if ($firstAgency) {
        echo "Manager '{$manager->name}' can access Agency '{$firstAgency->name}': " . 
             ($manager->canAccessAgency($firstAgency) ? 'Yes' : 'No') . "\n";
        
        echo "Employee '{$employee->name}' can access Agency '{$firstAgency->name}': " . 
             ($employee->canAccessAgency($firstAgency) ? 'Yes' : 'No') . "\n";
    }
    
    echo "Employee '{$employee->name}' can access Manager '{$manager->name}': " . 
         ($employee->canAccessManager($manager) ? 'Yes' : 'No') . "\n";
}

echo "\n=== Test Complete ===\n";
