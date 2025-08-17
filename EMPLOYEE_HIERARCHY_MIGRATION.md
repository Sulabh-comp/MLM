# Employee Hierarchy Migration Guide

## Overview

This document outlines the complete migration process from region-based employee management to hierarchy-based management in the MLM system.

## Migration Components

### 1. Code Updates ✅ COMPLETED

- **Employee Controller** (`app/Http/Controllers/Manager/EmployeeController.php`)
  - Converted from region-based to hierarchy-based access control
  - Added manager assignment validation
  - Implemented employee reassignment functionality
  - Added hierarchy statistics and filtering

- **Employee Views** (`resources/views/manager/employees/`)
  - **Index View**: Updated to show hierarchy statistics and manager assignments
  - **Create Form**: Replaced region selection with manager assignment interface
  - **Edit Form**: Added manager reassignment with hierarchy context
  - **Show View**: Display hierarchy information instead of region data

### 2. Data Migration Scripts

#### Script 1: `migrate_employees_to_hierarchy.php`

**Purpose**: Assigns employees without manager assignments to appropriate managers based on their region.

**Features**:
- Identifies employees without manager assignments
- Maps regions to available managers
- Assigns employees to most suitable managers (lowest hierarchy level preferred)
- Validates hierarchy relationships
- Provides detailed reporting and confirmation

**Usage**:
```bash
php migrate_employees_to_hierarchy.php
```

**Safety Features**:
- Transaction-based (rollback on errors)
- Confirmation prompt before committing changes
- Detailed logging of all assignments
- Validation of hierarchy integrity

#### Script 2: `cleanup_region_dependencies.php`

**Purpose**: Removes region dependencies from the employee system after successful hierarchy migration.

**Features**:
- Verifies all employees have valid manager assignments
- Creates backup of current state
- Multiple cleanup options:
  1. Remove region_id column (destructive)
  2. Set region_id to NULL (reversible)
  3. Generate Laravel migration file
  4. Exit without changes

**Usage**:
```bash
php cleanup_region_dependencies.php
```

**Safety Features**:
- Pre-flight checks for data integrity
- Automatic backup creation
- Multiple cleanup strategies
- Confirmation prompts for destructive actions

## Migration Process

### Step 1: Verify Current State

1. Check that all manager hierarchy relationships are properly established
2. Verify that managers have appropriate access levels
3. Confirm employee model has `manager_id` field and relationships

### Step 2: Run Employee-to-Manager Assignment

```bash
# Run the migration script
php migrate_employees_to_hierarchy.php

# Review the output carefully
# Confirm changes when prompted
```

**Expected Output**:
- Number of employees processed
- Successful assignments
- Validation results
- Hierarchy statistics

### Step 3: Verify Migration Results

1. Check manager interfaces to ensure employees appear correctly
2. Test employee CRUD operations through manager interface
3. Verify hierarchy access controls work properly
4. Confirm statistics and counts are accurate

### Step 4: Clean Up Region Dependencies

```bash
# Run the cleanup script
php cleanup_region_dependencies.php

# Choose appropriate cleanup option:
# - Option 2 (set to NULL) for reversible cleanup
# - Option 3 (generate migration) for Laravel workflow
# - Option 1 (remove column) for permanent cleanup
```

### Step 5: Post-Migration Validation

1. Test all employee management functions through manager interface
2. Verify hierarchy-based access controls
3. Check that employee reassignment works correctly
4. Confirm no orphaned employees exist

## Database Changes

### Before Migration
```sql
employees table:
- id
- name
- email
- phone
- designation
- region_id (foreign key to regions)
- manager_id (nullable, often unused)
- status
- address
- created_at
- updated_at
```

### After Migration
```sql
employees table:
- id
- name
- email
- phone
- designation
- manager_id (required, foreign key to managers)
- status
- address
- created_at
- updated_at
-- region_id removed or set to NULL
```

## Key Features of New System

### Manager Interface Features

1. **Hierarchy Statistics**
   - Direct employees count
   - Total employees in hierarchy
   - Active/inactive breakdown

2. **Employee Management**
   - View employees in accessible hierarchy
   - Create employees with manager assignment
   - Edit employee details and reassign managers
   - Hierarchy-aware access controls

3. **Manager Assignment Interface**
   - Visual hierarchy representation in dropdowns
   - Manager level information
   - Accessible manager filtering

### Access Control Logic

```php
// Managers can access employees assigned to:
// 1. Themselves directly
// 2. Their subordinate managers
// 3. Any manager in their accessible hierarchy

// Example hierarchy access:
Senior Manager (Level 1)
├── Regional Manager (Level 2)
│   ├── Employee A
│   └── Employee B
└── Area Manager (Level 2)
    ├── Employee C
    └── Team Lead (Level 3)
        ├── Employee D
        └── Employee E

// Senior Manager can access: All employees (A, B, C, D, E)
// Regional Manager can access: Employees A, B
// Area Manager can access: Employees C, D, E
// Team Lead can access: Employees D, E
```

## Rollback Procedures

### If Migration Fails

1. **Database Rollback**: Scripts use transactions and auto-rollback on errors
2. **Code Rollback**: Revert controller and view changes from git
3. **Manual Cleanup**: Check for partial assignments and clean up

### If Issues Found Post-Migration

1. **Restore from Backup**: Use JSON backup created by cleanup script
2. **Re-run Assignment**: Run migration script again with different parameters
3. **Manual Correction**: Use manager interface to reassign problematic employees

## Troubleshooting

### Common Issues

1. **Employees Not Appearing in Manager Interface**
   - Check manager_id assignments
   - Verify hierarchy access permissions
   - Confirm manager relationships

2. **Manager Assignment Errors**
   - Validate manager hierarchy integrity
   - Check circular reference prevention
   - Verify manager access levels

3. **Statistics Not Updating**
   - Clear application cache
   - Check relationship methods in models
   - Verify query logic in controllers

### Debug Commands

```php
// Check employee assignments
Employee::whereNull('manager_id')->count()

// Verify manager hierarchy
Manager::with('subordinates', 'employees')->get()

// Test access control
$manager->accessibleEmployees()->count()
```

## Testing Checklist

- [ ] All employees have valid manager assignments
- [ ] Manager interface displays correct employee lists
- [ ] Employee creation works with manager assignment
- [ ] Employee editing preserves hierarchy relationships
- [ ] Access controls prevent unauthorized access
- [ ] Statistics and counts are accurate
- [ ] No orphaned or circular references exist
- [ ] Performance is acceptable with hierarchy queries

## Performance Considerations

1. **Eager Loading**: Views use `with()` to prevent N+1 queries
2. **Indexing**: Ensure manager_id and hierarchy fields are indexed
3. **Caching**: Consider caching hierarchy trees for large organizations
4. **Pagination**: Large employee lists are paginated

## Security Notes

1. **Access Control**: All employee access goes through hierarchy validation
2. **Data Integrity**: Foreign key constraints prevent orphaned records
3. **Audit Trail**: Consider adding employee assignment change logging
4. **Permission Verification**: Manager permissions checked on every operation

---

## Contact Information

For issues or questions regarding this migration:
- Review hierarchy system documentation
- Check manager interface functionality
- Validate employee model relationships
- Test access control implementations
