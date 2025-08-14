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
use App\Models\ManagerLevel;
use App\Services\HierarchyPermissionService;
use App\Services\HierarchyFilterService;
use App\Traits\HierarchyAccessControl;

echo "=== Complete Authorization & Access Control System Test ===\n\n";

class TestController {
    use HierarchyAccessControl;
    
    public function testHierarchySystem() {
        $this->initializeHierarchyAccessControl();
        
        echo "1. Testing Manager Permissions:\n";
        $manager = Manager::first();
        if ($manager) {
            $this->testManagerPermissions($manager);
        } else {
            echo "No managers found.\n";
        }
        
        echo "\n2. Testing Employee Access:\n";
        $employee = Employee::first();
        if ($employee) {
            $this->testEmployeeAccess($employee);
        } else {
            echo "No employees found.\n";
        }
        
        echo "\n3. Testing Filtering System:\n";
        $this->testFilteringSystem();
        
        echo "\n4. Testing Bulk Access Validation:\n";
        $this->testBulkAccessValidation();
        
        echo "\n5. Testing Permission Matrix:\n";
        $this->testPermissionMatrix();
    }
    
    private function testManagerPermissions($manager) {
        echo "Manager: {$manager->name} (Level: {$manager->level_name})\n";
        
        $permissionService = new HierarchyPermissionService();
        
        // Test subordinate creation
        $availableLevels = $permissionService->getAvailableSubordinateLevels($manager);
        echo "- Can create subordinates at levels: " . $availableLevels->pluck('level_name')->implode(', ') . "\n";
        
        // Test specific level creation
        $testLevel = 'Team Leader';
        $canCreate = $permissionService->canCreateSubordinate($manager, $testLevel);
        echo "- Can create {$testLevel}: " . ($canCreate ? 'Yes' : 'No') . "\n";
        
        // Test report permissions
        $canViewFinancial = $permissionService->canViewReports($manager, 'financial');
        echo "- Can view financial reports: " . ($canViewFinancial ? 'Yes' : 'No') . "\n";
        
        // Test territorial boundaries
        $boundaries = $permissionService->getTerritorialBoundaries($manager);
        echo "- Subordinate managers: " . $boundaries['subordinate_managers']->count() . "\n";
        echo "- Employees under hierarchy: " . $boundaries['employees']->count() . "\n";
        echo "- Agencies: " . $boundaries['agencies']->count() . "\n";
        echo "- Customers: " . $boundaries['customers']->count() . "\n";
    }
    
    private function testEmployeeAccess($employee) {
        echo "Employee: {$employee->name}\n";
        
        if ($employee->manager) {
            echo "- Reports to: {$employee->manager->name} ({$employee->manager->level_name})\n";
            
            // Test accessible managers
            $accessibleManagers = $employee->accessibleManagers();
            echo "- Accessible managers: " . $accessibleManagers->count() . "\n";
            
            // Test colleagues
            $colleagues = $employee->colleagues()->get();
            echo "- Colleagues: " . $colleagues->count() . "\n";
            
            // Test access permissions
            echo "- Can access manager: " . ($employee->canAccessManager($employee->manager) ? 'Yes' : 'No') . "\n";
        } else {
            echo "- No assigned manager\n";
        }
        
        // Test employee's territorial scope
        $territorialScope = $employee->getTerritorialScope();
        echo "- Agencies: " . $territorialScope['agencies']->count() . "\n";
        echo "- Customers: " . $territorialScope['customers']->count() . "\n";
    }
    
    private function testFilteringSystem() {
        $filterService = new HierarchyFilterService();
        
        $manager = Manager::first();
        $employee = Employee::first();
        
        if ($manager) {
            echo "Manager filtering:\n";
            $managerStats = $filterService->getFilteredStats($manager);
            echo "- Scope: {$managerStats['scope']}\n";
            echo "- Visible managers: {$managerStats['managers']}\n";
            echo "- Visible employees: {$managerStats['employees']}\n";
            
            // Test searchable resources
            $searchableResources = $filterService->getSearchableResources($manager);
            echo "- Searchable resources: " . implode(', ', $searchableResources) . "\n";
        }
        
        if ($employee) {
            echo "Employee filtering:\n";
            $employeeStats = $filterService->getFilteredStats($employee);
            echo "- Scope: {$employeeStats['scope']}\n";
            echo "- Visible employees: {$employeeStats['employees']}\n";
            echo "- Visible agencies: {$employeeStats['agencies']}\n";
        }
    }
    
    private function testBulkAccessValidation() {
        $filterService = new HierarchyFilterService();
        
        $manager = Manager::first();
        if ($manager) {
            $allEmployeeIds = Employee::pluck('id')->toArray();
            
            if (!empty($allEmployeeIds)) {
                $validation = $filterService->validateResourceAccess($manager, 'employees', $allEmployeeIds);
                
                echo "Manager bulk access to employees:\n";
                echo "- Total employees: " . count($allEmployeeIds) . "\n";
                echo "- Authorized: " . count($validation['authorized']) . "\n";
                echo "- Unauthorized: " . count($validation['unauthorized']) . "\n";
                echo "- Access percentage: " . round((count($validation['authorized']) / count($allEmployeeIds)) * 100, 2) . "%\n";
            } else {
                echo "No employees found for bulk access test.\n";
            }
        }
    }
    
    private function testPermissionMatrix() {
        $manager = Manager::first();
        $employee = Employee::first();
        
        if ($manager) {
            echo "Manager Permission Matrix:\n";
            $permissions = $this->getPermissionMatrix($manager);
            echo "- Level: {$permissions['level_info']['level_name']} (Level {$permissions['level_info']['hierarchy_level']})\n";
            echo "- Can create managers: " . ($permissions['creation_permissions']['can_create_managers'] ? 'Yes' : 'No') . "\n";
            echo "- Can create employees: " . ($permissions['creation_permissions']['can_create_employees'] ? 'Yes' : 'No') . "\n";
            echo "- Available subordinate levels: " . implode(', ', $permissions['creation_permissions']['available_subordinate_levels']->toArray()) . "\n";
            echo "- Financial reports access: " . ($permissions['reporting_permissions']['can_view_financial_reports'] ? 'Yes' : 'No') . "\n";
        }
        
        if ($employee) {
            echo "Employee Permission Matrix:\n";
            $permissions = $this->getPermissionMatrix($employee);
            echo "- Level: {$permissions['level_info']['level_name']}\n";
            echo "- Can create managers: " . ($permissions['creation_permissions']['can_create_managers'] ? 'Yes' : 'No') . "\n";
            echo "- Can create employees: " . ($permissions['creation_permissions']['can_create_employees'] ? 'Yes' : 'No') . "\n";
            echo "- Financial reports access: " . ($permissions['reporting_permissions']['can_view_financial_reports'] ? 'Yes' : 'No') . "\n";
            echo "- Customer reports access: " . ($permissions['reporting_permissions']['can_view_customer_reports'] ? 'Yes' : 'No') . "\n";
        }
    }
}

// Run the comprehensive test
$testController = new TestController();
$testController->testHierarchySystem();

echo "\n6. Testing Hierarchy Validation Rules:\n";

// Test validation rules
use App\Rules\ValidHierarchyLevel;
use App\Rules\ValidHierarchyParent;
use App\Rules\ValidEmployeeManager;
use App\Rules\HierarchyDepthLimit;

$manager = Manager::first();
if ($manager) {
    echo "Testing validation rules with Manager: {$manager->name}\n";
    
    // Test hierarchy level validation
    $levelRule = new ValidHierarchyLevel($manager, 'create');
    echo "- Can create 'Team Leader': ";
    $levelRule->validate('level_name', 'Team Leader', function($message) {
        echo "No - $message\n";
    });
    if (!isset($message)) echo "Yes\n";
    
    // Test parent validation
    $parentRule = new ValidHierarchyParent($manager);
    echo "- Can assign manager as parent: ";
    $parentRule->validate('parent_id', $manager->id, function($message) {
        echo "No - $message\n";
    });
    if (!isset($message)) echo "Yes\n";
    
    // Test depth limit
    $depthRule = new HierarchyDepthLimit(10);
    echo "- Within depth limit: ";
    $depthRule->validate('parent_id', $manager->parent_id, function($message) {
        echo "No - $message\n";
    });
    if (!isset($message)) echo "Yes\n";
}

echo "\n7. Testing Manager Level System:\n";

$levels = ManagerLevel::orderBy('hierarchy_level')->get();
echo "Available Manager Levels:\n";
foreach ($levels as $level) {
    echo "- {$level->level_name} (Level {$level->hierarchy_level})\n";
}

echo "\n8. Testing Complete Hierarchy Chain:\n";

echo "Complete hierarchy chain demonstration:\n";
echo "Admin -> Managers -> Employees -> Agencies -> Customers\n\n";

if ($manager) {
    echo "Manager: {$manager->name}\n";
    echo "├── Subordinate Managers: " . $manager->allSubordinates()->count() . "\n";
    echo "├── Direct Employees: " . $manager->employees()->count() . "\n";
    echo "├── All Employees (hierarchy): " . $manager->allEmployees()->count() . "\n";
    echo "├── Agencies (through employees): " . $manager->allAgencies()->count() . "\n";
    echo "└── Customers (through agencies): " . $manager->allCustomers()->count() . "\n";
}

echo "\n=== Complete Authorization & Access Control System Test Complete ===\n";
