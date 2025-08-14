<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Contracts\Console\Kernel;

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

// Import the necessary classes
use App\Models\Manager;
use App\Models\Employee;
use App\Models\Agency;
use App\Models\Customer;
use App\Services\HierarchyPermissionService;
use App\Services\HierarchyFilterService;

echo "=== Testing Authorization & Access Control System ===\n\n";

// Test 1: Permission Service
echo "1. Testing Permission Service:\n";
$permissionService = new HierarchyPermissionService();

$manager = Manager::first();
if ($manager) {
    echo "Manager: {$manager->name} (Level: {$manager->level_name})\n";
    
    // Test subordinate creation permissions
    $availableLevels = $permissionService->getAvailableSubordinateLevels($manager);
    echo "Can create subordinates at levels: " . $availableLevels->pluck('level_name')->implode(', ') . "\n";
    
    // Test permission matrix
    $permissionMatrix = $permissionService->getPermissionMatrix($manager);
    echo "Permission Matrix:\n";
    echo "- Can create managers: " . ($permissionMatrix['creation_permissions']['can_create_managers'] ? 'Yes' : 'No') . "\n";
    echo "- Can create employees: " . ($permissionMatrix['creation_permissions']['can_create_employees'] ? 'Yes' : 'No') . "\n";
    echo "- Subordinate managers: " . $permissionMatrix['management_permissions']['subordinate_managers_count'] . "\n";
    echo "- Employees under hierarchy: " . $permissionMatrix['management_permissions']['employees_count'] . "\n";
    echo "- Can view financial reports: " . ($permissionMatrix['reporting_permissions']['can_view_financial_reports'] ? 'Yes' : 'No') . "\n";
} else {
    echo "No managers found.\n";
}

echo "\n";

// Test 2: Filter Service
echo "2. Testing Filter Service:\n";
$filterService = new HierarchyFilterService();

if ($manager) {
    // Test stats filtering
    $stats = $filterService->getFilteredStats($manager);
    echo "Filtered Stats for Manager {$manager->name}:\n";
    echo "- Scope: {$stats['scope']}\n";
    echo "- Managers: {$stats['managers']}\n";
    echo "- Employees: {$stats['employees']}\n";
    echo "- Agencies: {$stats['agencies']}\n";
    echo "- Customers: {$stats['customers']}\n";
}

echo "\n";

// Test 3: Employee Access
echo "3. Testing Employee Access:\n";
$employee = Employee::first();
if ($employee) {
    echo "Employee: {$employee->name}\n";
    
    if ($employee->manager) {
        echo "Reports to: {$employee->manager->name} ({$employee->manager->level_name})\n";
        
        // Test accessible managers
        $accessibleManagers = $employee->accessibleManagers();
        echo "Accessible managers: " . $accessibleManagers->count() . "\n";
        
        // Test territorial scope
        $territorialScope = $employee->getTerritorialScope();
        echo "Territorial scope:\n";
        echo "- Agencies: " . $territorialScope['agencies']->count() . "\n";
        echo "- Customers: " . $territorialScope['customers']->count() . "\n";
        echo "- Managers: " . $territorialScope['managers']->count() . "\n";
    } else {
        echo "Employee has no assigned manager.\n";
    }
    
    // Test employee stats
    $employeeStats = $filterService->getFilteredStats($employee);
    echo "Employee filtered stats:\n";
    echo "- Scope: {$employeeStats['scope']}\n";
    echo "- Employees (colleagues): {$employeeStats['employees']}\n";
    echo "- Agencies: {$employeeStats['agencies']}\n";
    echo "- Customers: {$employeeStats['customers']}\n";
} else {
    echo "No employees found.\n";
}

echo "\n";

// Test 4: Resource Access Validation
echo "4. Testing Resource Access Validation:\n";

if ($manager) {
    // Test manager access to various resources
    $allEmployees = Employee::pluck('id')->toArray();
    $validation = $filterService->validateResourceAccess($manager, 'employees', $allEmployees);
    
    echo "Manager access to all employees:\n";
    echo "- Authorized: " . count($validation['authorized']) . "\n";
    echo "- Unauthorized: " . count($validation['unauthorized']) . "\n";
    echo "- Has unauthorized: " . ($validation['has_unauthorized'] ? 'Yes' : 'No') . "\n";
}

echo "\n";

// Test 5: Searchable Resources
echo "5. Testing Searchable Resources:\n";

if ($manager) {
    $searchableResources = $filterService->getSearchableResources($manager);
    echo "Manager can search: " . implode(', ', $searchableResources) . "\n";
}

if ($employee) {
    $searchableResources = $filterService->getSearchableResources($employee);
    echo "Employee can search: " . implode(', ', $searchableResources) . "\n";
}

echo "\n=== Authorization & Access Control Test Complete ===\n";
