<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Manager;
use App\Models\ManagerLevel;
use App\Http\Requests\CreateManagerRequest;
use App\Traits\HierarchyAccessControl;
use App\Services\HierarchyPermissionService;

class HierarchyManagerController extends Controller
{
    use HierarchyAccessControl;

    protected $permissionService;

    public function __construct()
    {
        $this->initializeHierarchyAccessControl();
        $this->permissionService = new HierarchyPermissionService();
        
        // Middleware will be applied via routes
    }

    /**
     * Display a listing of managers based on user's hierarchy access
     */
    public function index(Request $request)
    {
        $user = $this->getCurrentUser();
        
        if (!$user) {
            return $this->handleUnauthorizedAccess();
        }

        // Apply hierarchy filtering and search
        $managers = $this->applySearchAndPagination(
            Manager::with(['managerLevel', 'parent', 'region']),
            $request,
            'managers',
            ['name', 'email', 'phone', 'designation', 'level_name']
        );

        // Get permission matrix for current user
        $permissions = $this->getPermissionMatrix($user);
        
        // Get available levels for creation
        $availableLevels = collect();
        if ($this->isManager($user)) {
            /** @var Manager $user */
            $availableLevels = $this->permissionService->getAvailableSubordinateLevels($user);
        } elseif ($this->isAdmin($user)) {
            $availableLevels = ManagerLevel::orderBy('hierarchy_level')->get();
        }

        return response()->json([
            'managers' => $managers,
            'permissions' => $permissions,
            'available_levels' => $availableLevels,
            'stats' => $this->getFilteredStats($user)
        ]);
    }

    /**
     * Dashboard with hierarchy-based statistics
     */
    public function dashboard()
    {
        $user = $this->getCurrentUser();
        
        if (!$user) {
            return $this->handleUnauthorizedAccess();
        }

        $stats = $this->getFilteredStats($user);
        $permissions = $this->getPermissionMatrix($user);

        // Get hierarchy tree for visualization
        $hierarchyTree = [];
        if ($this->isAdmin($user)) {
            $hierarchyTree = Manager::whereNull('parent_id')
                                   ->with(['children.children.children'])
                                   ->get();
        } elseif ($this->isManager($user)) {
            $hierarchyTree = [$user->load(['children.children.children'])];
        }

        return response()->json([
            'stats' => $stats,
            'permissions' => $permissions,
            'hierarchy_tree' => $hierarchyTree,
            'user_type' => $this->getUserType($user),
            'searchable_resources' => $this->getSearchableResources($user)
        ]);
    }

    /**
     * Store a newly created manager
     */
    public function store(CreateManagerRequest $request)
    {
        $user = $this->getCurrentUser();

        // Validate permissions
        if (!$this->canCreateManager($user, $request->level_name)) {
            return response()->json([
                'error' => 'You do not have permission to create managers at this level.',
                'code' => 'INSUFFICIENT_PERMISSIONS'
            ], 403);
        }

        // Create manager with validated data
        $manager = Manager::create($request->validated());

        // Log the creation
        \Log::info('Manager created', [
            'created_by' => $user->name,
            'created_by_type' => get_class($user),
            'manager_id' => $manager->id,
            'manager_name' => $manager->name,
            'manager_level' => $manager->level_name
        ]);

        return response()->json([
            'message' => 'Manager created successfully',
            'manager' => $manager->load(['managerLevel', 'parent', 'region']),
            'permissions' => $this->getPermissionMatrix($user)
        ], 201);
    }

    /**
     * Display the specified manager
     */
    public function show(Manager $manager)
    {
        $user = $this->getCurrentUser();

        // Check access permissions
        if (!$this->canAccessResource('managers', $manager->id, $user)) {
            return $this->handleUnauthorizedAccess('You cannot access this manager.');
        }

        // Load relationships
        $manager->load(['managerLevel', 'parent', 'children', 'region', 'employees']);

        // Get subordinate hierarchy
        $subordinates = $manager->allSubordinates();
        
        // Get territorial boundaries
        $territorialBoundaries = $this->permissionService->getTerritorialBoundaries($manager);

        return response()->json([
            'manager' => $manager,
            'subordinates' => $subordinates,
            'territorial_boundaries' => $territorialBoundaries,
            'can_edit' => $this->canEditManager($user, $manager),
            'can_delete' => $this->canDeleteManager($user, $manager)
        ]);
    }

    /**
     * Update the specified manager
     */
    public function update(Request $request, Manager $manager)
    {
        $user = $this->getCurrentUser();

        // Check permissions
        if (!$this->canEditManager($user, $manager)) {
            return $this->handleUnauthorizedAccess('You cannot edit this manager.');
        }

        // Validate update data
        $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:managers,email,' . $manager->id,
            'phone' => 'sometimes|string|max:20',
            'designation' => 'sometimes|string|max:255',
            'territory_name' => 'sometimes|string|max:255',
            'territory_description' => 'sometimes|string|max:1000',
        ]);

        $manager->update($request->only([
            'name', 'email', 'phone', 'designation', 
            'territory_name', 'territory_description'
        ]));

        \Log::info('Manager updated', [
            'updated_by' => $user->name,
            'updated_by_type' => get_class($user),
            'manager_id' => $manager->id,
            'manager_name' => $manager->name
        ]);

        return response()->json([
            'message' => 'Manager updated successfully',
            'manager' => $manager->fresh(['managerLevel', 'parent', 'region'])
        ]);
    }

    /**
     * Remove the specified manager
     */
    public function destroy(Manager $manager)
    {
        $user = $this->getCurrentUser();

        // Check permissions
        if (!$this->canDeleteManager($user, $manager)) {
            return $this->handleUnauthorizedAccess('You cannot delete this manager.');
        }

        // Check if manager has subordinates
        if ($manager->children()->exists()) {
            return response()->json([
                'error' => 'Cannot delete manager with subordinates. Please reassign or delete subordinates first.',
                'code' => 'HAS_SUBORDINATES'
            ], 422);
        }

        // Check if manager has employees
        if ($manager->employees()->exists()) {
            return response()->json([
                'error' => 'Cannot delete manager with assigned employees. Please reassign employees first.',
                'code' => 'HAS_EMPLOYEES'
            ], 422);
        }

        $managerName = $manager->name;
        $manager->delete();

        \Log::info('Manager deleted', [
            'deleted_by' => $user->name,
            'deleted_by_type' => get_class($user),
            'manager_name' => $managerName
        ]);

        return response()->json([
            'message' => 'Manager deleted successfully'
        ]);
    }

    /**
     * Get manager's subordinate hierarchy
     */
    public function subordinates(Manager $manager)
    {
        $user = $this->getCurrentUser();

        if (!$this->canAccessResource('managers', $manager->id, $user)) {
            return $this->handleUnauthorizedAccess();
        }

        $subordinates = $manager->allSubordinates();
        $directReports = $manager->children;

        return response()->json([
            'manager' => $manager->only(['id', 'name', 'level_name']),
            'direct_reports' => $directReports,
            'all_subordinates' => $subordinates,
            'subordinate_count' => $subordinates->count()
        ]);
    }

    /**
     * Get manager's territorial coverage
     */
    public function territory(Manager $manager)
    {
        $user = $this->getCurrentUser();

        if (!$this->canAccessResource('managers', $manager->id, $user)) {
            return $this->handleUnauthorizedAccess();
        }

        $boundaries = $this->permissionService->getTerritorialBoundaries($manager);

        return response()->json([
            'manager' => $manager->only(['id', 'name', 'level_name']),
            'territorial_boundaries' => $boundaries
        ]);
    }

    /**
     * Bulk operations with hierarchy validation
     */
    public function bulkAction(Request $request)
    {
        $user = $this->getCurrentUser();

        $request->validate([
            'action' => 'required|in:activate,deactivate,reassign',
            'manager_ids' => 'required|array',
            'manager_ids.*' => 'exists:managers,id',
            'new_parent_id' => 'sometimes|exists:managers,id'
        ]);

        // Validate access to all selected managers
        $validation = $this->validateBulkAccess('managers', $request->manager_ids, $user);
        
        if ($validation['has_unauthorized']) {
            return response()->json([
                'error' => 'You do not have access to some of the selected managers.',
                'unauthorized_ids' => $validation['unauthorized'],
                'code' => 'BULK_ACCESS_DENIED'
            ], 403);
        }

        $managers = Manager::whereIn('id', $validation['authorized'])->get();
        $results = [];

        foreach ($managers as $manager) {
            try {
                switch ($request->action) {
                    case 'activate':
                        $manager->update(['status' => 1]);
                        $results[] = ['id' => $manager->id, 'status' => 'activated'];
                        break;
                    
                    case 'deactivate':
                        $manager->update(['status' => 0]);
                        $results[] = ['id' => $manager->id, 'status' => 'deactivated'];
                        break;
                    
                    case 'reassign':
                        if ($request->new_parent_id) {
                            $manager->update(['parent_id' => $request->new_parent_id]);
                            $results[] = ['id' => $manager->id, 'status' => 'reassigned'];
                        }
                        break;
                }
            } catch (\Exception $e) {
                $results[] = ['id' => $manager->id, 'status' => 'failed', 'error' => $e->getMessage()];
            }
        }

        return response()->json([
            'message' => 'Bulk action completed',
            'results' => $results
        ]);
    }

    /**
     * Helper methods for permission checking
     */
    private function canCreateManager($user, $levelName): bool
    {
        if ($this->isAdmin($user)) {
            return true;
        }

        if ($this->isManager($user)) {
            return $this->permissionService->canCreateSubordinate($user, $levelName);
        }

        return false;
    }

    private function canEditManager($user, Manager $manager): bool
    {
        if ($this->isAdmin($user)) {
            return true;
        }

        if ($this->isManager($user)) {
            return $this->permissionService->canManageManager($user, $manager);
        }

        return false;
    }

    private function canDeleteManager($user, Manager $manager): bool
    {
        return $this->canEditManager($user, $manager);
    }

    private function getUserType($user): string
    {
        if ($this->isAdmin($user)) return 'admin';
        if ($this->isManager($user)) return 'manager';
        if ($this->isEmployee($user)) return 'employee';
        return 'unknown';
    }
}
