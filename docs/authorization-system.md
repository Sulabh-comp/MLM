# Authorization & Access Control System

## Overview

A comprehensive hierarchy-based authorization and access control system for the MLM application that implements role-based permissions with territorial filtering and subordinate data access control.

## System Architecture

```
Admin (Global Access)
│
├── Managers (Hierarchy-based Access)
│   ├── CEO (Level 1) → Regional Managers
│   ├── Regional Manager (Level 2) → Area Managers  
│   ├── Area Manager (Level 3) → Zone Managers
│   ├── Zone Manager (Level 4) → Team Leaders
│   ├── Team Leader (Level 5) → Managers
│   └── Manager (Level 6) → Employees
│
├── Employees (Employee-level Access)
│   └── Agencies → Customers
│
└── Data Flow: Admin → Managers → Employees → Agencies → Customers
```

## Components Implemented

### 1. Middleware System

**File**: `app/Http/Middleware/HierarchyAccessMiddleware.php`

- **Purpose**: Centralized access control for all hierarchy-based operations
- **Features**:
  - Multi-guard authentication (admin, manager, employee)
  - Resource-specific access validation
  - Automatic user context injection
  - Route parameter extraction

**Usage**:
```php
Route::middleware('hierarchy.access:manager')->group(function () {
    // Manager-specific routes
});
```

### 2. Permission Service

**File**: `app/Services/HierarchyPermissionService.php`

- **Purpose**: Business logic for hierarchy-based permissions
- **Key Methods**:
  - `canCreateSubordinate()` - Manager creation permissions
  - `canManageManager()` - Manager management permissions
  - `getTerritorialBoundaries()` - Geographic/hierarchical scope
  - `getPermissionMatrix()` - Complete permission overview
  - `validateManagerCreation()` - Creation validation

**Features**:
- Level-based subordinate creation limits
- Territorial access control
- Report viewing permissions (financial, performance, customer)
- Subordinate count limits per level

### 3. Filter Service

**File**: `app/Services/HierarchyFilterService.php`

- **Purpose**: Data filtering based on user's hierarchy position
- **Key Methods**:
  - `filterManagers()` - Manager query filtering
  - `filterEmployees()` - Employee query filtering
  - `filterAgencies()` - Agency query filtering
  - `filterCustomers()` - Customer query filtering
  - `validateResourceAccess()` - Bulk access validation

**Features**:
- Automatic query filtering based on user role
- Bulk operation access validation
- Export data filtering
- Search scope limitation

### 4. Validation Rules

#### `ValidHierarchyLevel.php`
- Validates manager creation at appropriate levels
- Ensures subordinates can only be created below current level

#### `ValidHierarchyParent.php`
- Validates parent-child relationships
- Prevents circular references
- Ensures parent is within accessible hierarchy

#### `ValidEmployeeManager.php`
- Validates employee-manager assignments
- Ensures manager is within user's territory

#### `HierarchyDepthLimit.php`
- Prevents excessive hierarchy depth (max 10 levels)
- Maintains organizational structure integrity

### 5. Form Requests

#### `CreateManagerRequest.php`
- Comprehensive validation for manager creation
- Authorization checking
- Business rule validation
- Automatic parent assignment

#### `CreateEmployeeRequest.php`
- Employee creation validation
- Manager assignment validation
- Capacity checking

### 6. Access Control Trait

**File**: `app/Traits/HierarchyAccessControl.php`

- **Purpose**: Reusable access control methods for controllers
- **Key Methods**:
  - `applyHierarchyFilter()` - Query filtering
  - `getFilteredStats()` - Dashboard statistics
  - `getPermissionMatrix()` - User permissions
  - `validateBulkAccess()` - Bulk operation validation
  - `applySearchAndPagination()` - Search with filtering

**Features**:
- Multi-guard user detection
- Automatic permission checking
- Centralized error handling
- Search and pagination integration

### 7. Controller Implementation

**File**: `app/Http/Controllers/HierarchyManagerController.php`

- **Purpose**: Demonstrates complete system usage
- **Features**:
  - Hierarchy-filtered CRUD operations
  - Permission-based action authorization
  - Territorial data access
  - Bulk operations with validation
  - Dashboard with role-based statistics

## Permission Matrix

### Admin Permissions
- **Global Access**: All managers, employees, agencies, customers
- **Creation Rights**: Any level manager, unlimited employees
- **Management Rights**: Complete hierarchy management
- **Reporting Access**: All report types
- **Territorial Scope**: Global

### Manager Permissions (Level-based)
- **Hierarchical Access**: Self + all subordinates
- **Creation Rights**: 
  - Managers: Only at levels below current
  - Employees: Unlimited within territory
- **Management Rights**: Direct reports + subordinate hierarchy
- **Reporting Access**: 
  - Financial: Levels 1-3 only
  - Performance: All levels
  - Customer: All levels
- **Territorial Scope**: Based on hierarchy position

### Employee Permissions
- **Limited Access**: Self + colleagues + accessible managers
- **Creation Rights**: None
- **Management Rights**: Own agencies and customers only
- **Reporting Access**: Customer reports only
- **Territorial Scope**: Employee level

## Security Features

### 1. Access Control
- **Multi-level Authorization**: Request validation, middleware, service layer
- **Resource Protection**: Every resource access validated
- **Bulk Operation Security**: Mass assignment protection
- **Circular Reference Prevention**: Hierarchy integrity maintained

### 2. Data Filtering
- **Automatic Filtering**: All queries filtered by hierarchy
- **Scope Limitation**: Users see only accessible data
- **Search Restriction**: Search limited to accessible resources
- **Export Protection**: Downloads filtered by permissions

### 3. Audit Trail
- **Action Logging**: All CRUD operations logged
- **User Tracking**: Complete user action history
- **Permission Changes**: Level changes tracked
- **Error Monitoring**: Unauthorized access attempts logged

## Usage Examples

### 1. Basic Controller Usage
```php
class MyController extends Controller
{
    use HierarchyAccessControl;
    
    public function index(Request $request)
    {
        $managers = $this->applySearchAndPagination(
            Manager::query(),
            $request,
            'managers',
            ['name', 'email']
        );
        
        return response()->json($managers);
    }
}
```

### 2. Permission Checking
```php
$user = $this->getCurrentUser();
$permissions = $this->getPermissionMatrix($user);

if ($permissions['creation_permissions']['can_create_managers']) {
    // Allow manager creation
}
```

### 3. Resource Access Validation
```php
$accessValidation = $this->validateBulkAccess('employees', $employeeIds);
if ($accessValidation['has_unauthorized']) {
    return response()->json(['error' => 'Unauthorized access'], 403);
}
```

## Route Protection

```php
// In routes/hierarchy.php
Route::middleware(['hierarchy.access:manager'])->group(function () {
    Route::get('/managers', [HierarchyManagerController::class, 'index']);
    Route::post('/managers', [HierarchyManagerController::class, 'store']);
    Route::get('/managers/{manager}', [HierarchyManagerController::class, 'show']);
});
```

## Database Integration

### Required Relationships
- **Manager Model**: `managerLevel()` relationship for level access
- **Employee Model**: `manager()` relationship for hierarchy access
- **Agency Model**: `employee()` relationship for access chain
- **Customer Model**: `agency()` relationship for access chain

### Access Chain
```
Manager → allSubordinates() → allEmployees() → allAgencies() → allCustomers()
Employee → manager → accessibleManagers() → agencies() → customers()
```

## Testing

**File**: `test_complete_authorization.php`

Comprehensive test suite covering:
- Permission matrix validation
- Filtering system testing
- Bulk access validation
- Hierarchy rule validation
- Complete access chain testing

## Benefits

1. **Security**: Multi-layer protection against unauthorized access
2. **Scalability**: Handles complex hierarchy structures efficiently
3. **Flexibility**: Easy to extend with new roles and permissions
4. **Maintainability**: Centralized logic with reusable components
5. **Performance**: Efficient filtering with minimal database queries
6. **Auditability**: Complete tracking of all access and changes

## Configuration

The system is automatically configured through:
- **Middleware Registration**: `bootstrap/app.php`
- **Route Protection**: `routes/hierarchy.php`
- **Model Relationships**: Automatic hierarchy detection
- **Service Integration**: Dependency injection ready

This authorization system provides enterprise-level security and access control for hierarchical organizations while maintaining flexibility and performance.
