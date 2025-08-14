# Manager Hierarchy System - Enhanced Features

## Overview
The Manager model has been enhanced with comprehensive parent-child relationships, hierarchy management, tree methods, and permission systems for flexible manager creation rights.

## Core Relationships

### Basic Hierarchy Relationships
- `parent()` - Get the parent manager
- `children()` - Get direct children managers (ordered by hierarchy_path)
- `subordinates()` - Get all subordinate managers (recursive with eager loading)
- `allSubordinates()` - Get all managers in this manager's hierarchy branch (including self)

### Advanced Hierarchy Queries
- `getSubordinatesByPath()` - Efficient subordinate retrieval using hierarchy path
- `ancestors()` - Get all parent managers up the hierarchy
- `getAncestorsByPath()` - Efficient ancestor retrieval using hierarchy path
- `siblings()` - Get managers with same parent
- `getDescendantCountAtDepth(int $depth)` - Count descendants at specific depth

## Hierarchy Validation & Analysis

### Relationship Checking
- `isSubordinateOf(Manager $manager)` - Check if manager is subordinate of another
- `isAncestorOf(Manager $manager)` - Check if manager is ancestor of another
- `isInSameBranch(Manager $manager)` - Check if managers are in same branch
- `getCommonAncestor(Manager $manager)` - Find common ancestor with another manager

### Utility Methods
- `isTopLevel()` - Check if manager has no parent
- `isLeaf()` - Check if manager has no subordinates
- `getTeamSize()` - Get total subordinates count
- `getDirectReportsCount()` - Get direct reports count
- `getSpanOfControl()` - Get hierarchy depth span
- `getTerritory()` - Get accessible regions

## Tree Management

### Tree Building
- `buildTree()` - Build complete hierarchy tree structure
- `getHierarchyList(string $indent)` - Get flat list with indentation
- `getHierarchyBreadcrumb(string $separator)` - Get display breadcrumb
- `getAvailableParents()` - Get valid parent options for manager

### Path Management
- `getHierarchyPath()` - Get path as array of manager IDs
- `updateHierarchyPath()` - Update hierarchy path for manager and children
- `calculateHierarchyPath()` - Calculate what path should be

## Permission System

### Creation Rights
- `canCreateSubordinates()` - Check if manager can create subordinates
- `canManageSubordinate(Manager $subordinate)` - Check management rights
- `canCreateManagerAtLevel(ManagerLevel $level)` - Check level-specific creation
- `getAssignableLevels()` - Get levels manager can assign to subordinates

### Permission Management
- `hasPermission(string $permission)` - Check specific permission
- `getAllPermissions()` - Get all permissions for manager
- `getDefaultPermissionsByLevel()` - Get default permissions by hierarchy level
- `canAccessRegion(int $regionId)` - Check regional access rights

### Default Permissions by Level
1. **CEO (Level 1)**: `create_subordinates`, `manage_all`, `view_all_reports`, `manage_levels`
2. **Regional Manager (Level 2)**: `create_subordinates`, `manage_region`, `view_region_reports`
3. **Area Manager (Level 3)**: `create_subordinates`, `manage_area`, `view_area_reports`
4. **Zone Manager (Level 4)**: `create_subordinates`, `manage_zone`, `view_zone_reports`
5. **Team Leader (Level 5)**: `manage_team`, `view_team_reports`
6. **Manager (Level 6+)**: `view_own_reports`

## Static Query Methods

### Hierarchy Queries
- `Manager::getTopLevelManagers()` - Get all managers without parents
- `Manager::getHierarchyTree()` - Get complete hierarchy as tree collection
- `Manager::getByLevel(string $levelName)` - Get managers by level name
- `Manager::getByDepth(int $depth)` - Get managers at specific depth
- `Manager::findInHierarchyPath(string $path)` - Find managers within path

### Data Management
- `Manager::getOrphanedManagers()` - Find managers with invalid parent_id
- `Manager::rebuildHierarchyPaths()` - Rebuild all hierarchy paths
- `Manager::getHierarchyStats()` - Get comprehensive hierarchy statistics
- `Manager::validateHierarchy()` - Validate hierarchy integrity

## Management Command

### Usage
```bash
# Show hierarchy statistics
php artisan hierarchy:manage stats

# Display hierarchy tree
php artisan hierarchy:manage tree
php artisan hierarchy:manage tree --manager=123

# Validate hierarchy integrity
php artisan hierarchy:manage validate

# Rebuild hierarchy paths
php artisan hierarchy:manage rebuild

# Show permissions analysis
php artisan hierarchy:manage permissions
php artisan hierarchy:manage permissions --manager=123

# Run comprehensive tests
php artisan hierarchy:manage test
```

### Command Features
- **Statistics**: Total managers, depth analysis, level distribution
- **Tree Visualization**: ASCII tree with icons and team sizes
- **Validation**: Circular reference detection, orphan identification
- **Permissions**: Detailed capability analysis and assignable levels
- **Testing**: Comprehensive method testing with results table

## Database Structure

### Required Fields
- `parent_id` - Foreign key to parent manager
- `level_name` - Manager level name (references manager_levels.name)
- `depth` - Hierarchy depth (0 = top level)
- `hierarchy_path` - Materialized path (e.g., "/1/3/7/")

### Indexes for Performance
- Index on `parent_id` for parent-child queries
- Index on `hierarchy_path` for path-based queries
- Index on `level_name` for level-based filtering
- Index on `depth` for depth-based queries

## Security & Validation

### Circular Reference Prevention
- Path calculation includes visited node tracking
- Hierarchy validation detects circular references
- Parent selection excludes subordinates

### Data Integrity
- Parent validation prevents self-referencing
- Level validation ensures proper hierarchy
- Permission checks for manager creation
- Automatic path updates on parent changes

## Performance Optimizations

### Efficient Queries
- Materialized paths for fast subtree queries
- Eager loading for relationship queries
- Indexed fields for common lookups
- Batch operations for tree rebuilding

### Memory Management
- Lazy loading for large hierarchies
- Collection-based processing
- Efficient tree traversal algorithms
- Minimal recursion depth

## Example Usage

```php
// Get a manager's team
$manager = Manager::find(1);
$teamSize = $manager->getTeamSize();
$directReports = $manager->getDirectReportsCount();

// Check permissions
if ($manager->canCreateSubordinates()) {
    $assignableLevels = $manager->getAssignableLevels();
}

// Build hierarchy tree
$tree = $manager->buildTree();

// Get hierarchy breadcrumb
$breadcrumb = $manager->getHierarchyBreadcrumb(' → ');

// Validate hierarchy
$issues = Manager::validateHierarchy();
if (empty($issues)) {
    echo "Hierarchy is valid!";
}

// Get statistics
$stats = Manager::getHierarchyStats();
echo "Total managers: " . $stats['total_managers'];
```

## Integration Points

### Admin Interface
- Manager creation forms with parent selection
- Level assignment with permission validation
- Hierarchy tree display in admin panel
- Permission management interface

### API Endpoints
- Hierarchy tree JSON endpoints
- Manager relationship queries
- Permission checking endpoints
- Statistics and reporting APIs

### Reporting
- Team size reports by manager
- Hierarchy depth analysis
- Permission audit reports
- Territory coverage reports
