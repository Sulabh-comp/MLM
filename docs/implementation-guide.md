# Implementation Guide: Hierarchy Authorization System

## Quick Start

### 1. Controller Setup
```php
<?php
namespace App\Http\Controllers;

use App\Traits\HierarchyAccessControl;
use Illuminate\Http\Request;

class YourController extends Controller
{
    use HierarchyAccessControl;
    
    public function __construct()
    {
        $this->initializeHierarchyAccessControl();
    }
    
    public function index(Request $request)
    {
        // Automatically filtered by user's hierarchy
        $data = $this->applySearchAndPagination(
            YourModel::query(),
            $request,
            'resource_type', // 'managers', 'employees', 'agencies', 'customers'
            ['searchable_field1', 'searchable_field2']
        );
        
        return response()->json($data);
    }
}
```

### 2. Route Protection
```php
// In your routes file
Route::middleware(['hierarchy.access:manager'])->group(function () {
    Route::resource('managers', ManagerController::class);
});

Route::middleware(['hierarchy.access:employee'])->group(function () {
    Route::resource('employees', EmployeeController::class);
});
```

### 3. Permission Checking
```php
public function store(Request $request)
{
    $user = $this->getCurrentUser();
    
    // Check specific permissions
    if (!$this->canAccessResource('managers', $managerId)) {
        return $this->handleUnauthorizedAccess();
    }
    
    // Get user's permission matrix
    $permissions = $this->getPermissionMatrix();
    
    if (!$permissions['creation_permissions']['can_create_managers']) {
        return response()->json(['error' => 'Insufficient permissions'], 403);
    }
    
    // Continue with creation...
}
```

## Advanced Usage

### 1. Custom Permission Checking
```php
use App\Services\HierarchyPermissionService;

public function customPermissionCheck()
{
    $user = $this->getCurrentUser();
    $permissionService = new HierarchyPermissionService();
    
    if ($this->isManager($user)) {
        // Check if manager can create specific level
        $canCreate = $permissionService->canCreateSubordinate($user, 'Team Leader');
        
        // Get territorial boundaries
        $boundaries = $permissionService->getTerritorialBoundaries($user);
        
        // Check report access
        $canViewFinancial = $permissionService->canViewReports($user, 'financial');
    }
}
```

### 2. Bulk Operations with Validation
```php
public function bulkUpdate(Request $request)
{
    $resourceIds = $request->input('resource_ids');
    
    // Validate bulk access
    $validation = $this->validateBulkAccess('employees', $resourceIds);
    
    if ($validation['has_unauthorized']) {
        return response()->json([
            'error' => 'Access denied to some resources',
            'unauthorized_ids' => $validation['unauthorized']
        ], 403);
    }
    
    // Process only authorized resources
    foreach ($validation['authorized'] as $id) {
        // Update logic here
    }
}
```

### 3. Dashboard with Filtered Stats
```php
public function dashboard()
{
    $user = $this->getCurrentUser();
    $stats = $this->getFilteredStats($user);
    $permissions = $this->getPermissionMatrix($user);
    
    return response()->json([
        'stats' => $stats,
        'permissions' => $permissions,
        'user_type' => $this->getUserType($user),
        'searchable_resources' => $this->getSearchableResources($user)
    ]);
}

private function getUserType($user): string
{
    if ($this->isAdmin($user)) return 'admin';
    if ($this->isManager($user)) return 'manager';
    if ($this->isEmployee($user)) return 'employee';
    return 'unknown';
}
```

## Form Request Implementation

### 1. Manager Creation Request
```php
<?php
namespace App\Http\Requests;

use App\Http\Requests\CreateManagerRequest;

// Use the existing CreateManagerRequest class
class CreateManagerRequest extends FormRequest
{
    // Already implemented with:
    // - Authorization checking
    // - Hierarchy validation
    // - Business rule validation
    // - Automatic parent assignment
}
```

### 2. Custom Validation Rules
```php
use App\Rules\ValidHierarchyLevel;
use App\Rules\ValidHierarchyParent;
use App\Rules\ValidEmployeeManager;

$request->validate([
    'level_name' => [
        'required',
        new ValidHierarchyLevel($currentUser, 'create')
    ],
    'parent_id' => [
        'nullable',
        new ValidHierarchyParent($currentUser)
    ],
    'manager_id' => [
        'required',
        new ValidEmployeeManager($currentUser)
    ]
]);
```

## Service Layer Usage

### 1. Filter Service
```php
use App\Services\HierarchyFilterService;

$filterService = new HierarchyFilterService();

// Get filtered statistics
$stats = $filterService->getFilteredStats($user);

// Apply filtering to queries
$query = Manager::query();
$filteredQuery = $filterService->filterManagers($query, $user);

// Validate resource access
$validation = $filterService->validateResourceAccess($user, 'employees', $employeeIds);

// Get export data
$exportData = $filterService->getFilteredExportData($user, 'customers', ['id', 'name', 'email']);
```

### 2. Permission Service
```php
use App\Services\HierarchyPermissionService;

$permissionService = new HierarchyPermissionService();

// Check creation permissions
$canCreate = $permissionService->canCreateSubordinate($manager, 'Area Manager');

// Get available levels for creation
$availableLevels = $permissionService->getAvailableSubordinateLevels($manager);

// Validate manager creation
$validationErrors = $permissionService->validateManagerCreation($manager, $requestData);

// Get complete permission matrix
$permissionMatrix = $permissionService->getPermissionMatrix($manager);
```

## Model Integration

### 1. Manager Model Usage
```php
// Access subordinates
$subordinates = $manager->allSubordinates();
$directReports = $manager->children;

// Access employees
$allEmployees = $manager->allEmployees();
$directEmployees = $manager->employees;

// Access agencies and customers
$agencies = $manager->allAgencies();
$customers = $manager->allCustomers();

// Permission checking
$canCreateLevel = $manager->canCreateLevel('Team Leader');
$permissions = $manager->getAllPermissions();
```

### 2. Employee Model Usage
```php
// Access manager hierarchy
$manager = $employee->manager;
$accessibleManagers = $employee->accessibleManagers();

// Get territorial scope
$territorialScope = $employee->getTerritorialScope();

// Check access permissions
$canAccessManager = $employee->canAccessManager($someManager);
$canAccessAgency = $employee->canAccessAgency($someAgency);
```

## Error Handling

### 1. Centralized Error Handling
```php
public function show($id)
{
    if (!$this->canAccessResource('managers', $id)) {
        return $this->handleUnauthorizedAccess('Access denied to this manager');
    }
    
    // Continue with show logic
}
```

### 2. Custom Error Responses
```php
if (!$hasPermission) {
    if (request()->wantsJson()) {
        return response()->json([
            'error' => 'Insufficient permissions',
            'code' => 'HIERARCHY_ACCESS_DENIED',
            'user_level' => $user->level_name ?? 'Unknown'
        ], 403);
    }
    
    abort(403, 'You do not have permission to perform this action');
}
```

## Testing Integration

### 1. Feature Testing
```php
<?php
namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Manager;
use App\Models\Employee;

class HierarchyAuthorizationTest extends TestCase
{
    public function test_manager_can_access_subordinates()
    {
        $manager = Manager::factory()->create();
        $subordinate = Manager::factory()->create(['parent_id' => $manager->id]);
        
        $this->actingAs($manager, 'manager');
        
        $response = $this->get("/hierarchy/managers/{$subordinate->id}");
        $response->assertStatus(200);
    }
    
    public function test_manager_cannot_access_superior()
    {
        $superior = Manager::factory()->create();
        $manager = Manager::factory()->create(['parent_id' => $superior->id]);
        
        $this->actingAs($manager, 'manager');
        
        $response = $this->get("/hierarchy/managers/{$superior->id}");
        $response->assertStatus(403);
    }
}
```

## Performance Optimization

### 1. Eager Loading
```php
// Load relationships for better performance
$managers = $this->applyHierarchyFilter(
    Manager::with(['managerLevel', 'parent', 'children', 'employees']),
    'managers'
)->paginate(15);
```

### 2. Caching Permissions
```php
// Cache permission matrix for session
$permissions = cache()->remember(
    "permissions.{$user->id}." . get_class($user),
    now()->addMinutes(30),
    fn() => $this->getPermissionMatrix($user)
);
```

## Common Patterns

### 1. Resource Controller Pattern
```php
class ResourceController extends Controller
{
    use HierarchyAccessControl;
    
    protected $resourceType;
    protected $searchFields;
    
    public function __construct()
    {
        $this->initializeHierarchyAccessControl();
    }
    
    public function index(Request $request)
    {
        return $this->applySearchAndPagination(
            $this->getModelClass()::query(),
            $request,
            $this->resourceType,
            $this->searchFields
        );
    }
    
    protected function getModelClass()
    {
        // Return appropriate model class
    }
}
```

### 2. API Response Pattern
```php
protected function successResponse($data, $message = 'Success')
{
    return response()->json([
        'success' => true,
        'message' => $message,
        'data' => $data,
        'permissions' => $this->getPermissionMatrix(),
        'user_context' => [
            'type' => $this->getUserType($this->getCurrentUser()),
            'searchable_resources' => $this->getSearchableResources()
        ]
    ]);
}
```

This implementation guide provides practical examples for integrating the hierarchy authorization system into your controllers, services, and business logic.
