<?php

namespace App\Traits;

use App\Services\HierarchyFilterService;
use App\Services\HierarchyPermissionService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

trait HierarchyAccessControl
{
    protected $hierarchyFilterService;
    protected $hierarchyPermissionService;

    /**
     * Initialize the hierarchy services
     */
    public function initializeHierarchyAccessControl()
    {
        $this->hierarchyFilterService = new HierarchyFilterService();
        $this->hierarchyPermissionService = new HierarchyPermissionService();
    }

    /**
     * Get the current authenticated user from any guard
     */
    protected function getCurrentUser()
    {
        if (auth()->guard('admin')->check()) {
            return auth()->guard('admin')->user();
        }

        if (auth()->guard('manager')->check()) {
            return auth()->guard('manager')->user();
        }

        if (auth()->guard('employee')->check()) {
            return auth()->guard('employee')->user();
        }

        return null;
    }

    /**
     * Apply hierarchy filtering to a query
     */
    protected function applyHierarchyFilter(Builder $query, string $resourceType, $user = null): Builder
    {
        if (!$this->hierarchyFilterService) {
            $this->initializeHierarchyAccessControl();
        }

        $user = $user ?: $this->getCurrentUser();
        
        if (!$user) {
            return $query->whereRaw('1 = 0'); // No access
        }

        return $this->hierarchyFilterService->applyTerritorialFilter($query, $user, $resourceType);
    }

    /**
     * Get filtered statistics for dashboard
     */
    protected function getFilteredStats($user = null): array
    {
        if (!$this->hierarchyFilterService) {
            $this->initializeHierarchyAccessControl();
        }

        $user = $user ?: $this->getCurrentUser();
        
        if (!$user) {
            return [
                'managers' => 0,
                'employees' => 0,
                'agencies' => 0,
                'customers' => 0,
                'scope' => 'No Access'
            ];
        }

        return $this->hierarchyFilterService->getFilteredStats($user);
    }

    /**
     * Check if current user can access a specific resource
     */
    protected function canAccessResource(string $resourceType, int $resourceId, $user = null): bool
    {
        if (!$this->hierarchyFilterService) {
            $this->initializeHierarchyAccessControl();
        }

        $user = $user ?: $this->getCurrentUser();
        
        if (!$user) {
            return false;
        }

        $accessibleIds = $this->hierarchyFilterService->getAccessibleResourceIds($user, $resourceType);
        return in_array($resourceId, $accessibleIds);
    }

    /**
     * Validate bulk resource access
     */
    protected function validateBulkAccess(string $resourceType, array $resourceIds, $user = null): array
    {
        if (!$this->hierarchyFilterService) {
            $this->initializeHierarchyAccessControl();
        }

        $user = $user ?: $this->getCurrentUser();
        
        if (!$user) {
            return [
                'authorized' => [],
                'unauthorized' => $resourceIds,
                'has_unauthorized' => true
            ];
        }

        return $this->hierarchyFilterService->validateResourceAccess($user, $resourceType, $resourceIds);
    }

    /**
     * Get permission matrix for current user
     */
    protected function getPermissionMatrix($user = null): array
    {
        $user = $user ?: $this->getCurrentUser();
        
        if (!$user) {
            return [];
        }

        if (get_class($user) === 'App\Models\Admin') {
            return [
                'level_info' => [
                    'level_name' => 'Administrator',
                    'hierarchy_level' => 0,
                    'can_create_subordinates' => true,
                ],
                'creation_permissions' => [
                    'can_create_managers' => true,
                    'can_create_employees' => true,
                    'available_subordinate_levels' => \App\Models\ManagerLevel::orderBy('hierarchy_level')->pluck('name'),
                ],
                'management_permissions' => [
                    'subordinate_managers_count' => \App\Models\Manager::count(),
                    'employees_count' => \App\Models\Employee::count(),
                    'agencies_count' => \App\Models\Agency::count(),
                    'customers_count' => \App\Models\Customer::count(),
                ],
                'reporting_permissions' => [
                    'can_view_financial_reports' => true,
                    'can_view_performance_reports' => true,
                    'can_view_customer_reports' => true,
                ],
                'territorial_scope' => [
                    'max_depth' => 'unlimited',
                    'regional_coverage' => 'global',
                ]
            ];
        }

        if (get_class($user) === 'App\Models\Manager') {
            if (!$this->hierarchyPermissionService) {
                $this->initializeHierarchyAccessControl();
            }
            
            return $this->hierarchyPermissionService->getPermissionMatrix($user);
        }

        if (get_class($user) === 'App\Models\Employee') {
            $territorialScope = $user->getTerritorialScope();
            
            return [
                'level_info' => [
                    'level_name' => 'Employee',
                    'hierarchy_level' => 'N/A',
                    'can_create_subordinates' => false,
                ],
                'creation_permissions' => [
                    'can_create_managers' => false,
                    'can_create_employees' => false,
                    'available_subordinate_levels' => [],
                ],
                'management_permissions' => [
                    'subordinate_managers_count' => 0,
                    'employees_count' => $user->colleagues()->count(),
                    'agencies_count' => $territorialScope['agencies']->count(),
                    'customers_count' => $territorialScope['customers']->count(),
                ],
                'reporting_permissions' => [
                    'can_view_financial_reports' => false,
                    'can_view_performance_reports' => false,
                    'can_view_customer_reports' => true,
                ],
                'territorial_scope' => [
                    'max_depth' => 0,
                    'regional_coverage' => 'employee_level',
                ]
            ];
        }

        return [];
    }

    /**
     * Check user type helpers
     */
    protected function isAdmin($user = null): bool
    {
        $user = $user ?: $this->getCurrentUser();
        return $user && get_class($user) === 'App\Models\Admin';
    }

    protected function isManager($user = null): bool
    {
        $user = $user ?: $this->getCurrentUser();
        return $user && get_class($user) === 'App\Models\Manager';
    }

    protected function isEmployee($user = null): bool
    {
        $user = $user ?: $this->getCurrentUser();
        return $user && get_class($user) === 'App\Models\Employee';
    }

    /**
     * Get searchable resources for current user
     */
    protected function getSearchableResources($user = null): array
    {
        if (!$this->hierarchyFilterService) {
            $this->initializeHierarchyAccessControl();
        }

        $user = $user ?: $this->getCurrentUser();
        
        if (!$user) {
            return [];
        }

        return $this->hierarchyFilterService->getSearchableResources($user);
    }

    /**
     * Handle unauthorized access
     */
    protected function handleUnauthorizedAccess(string $message = 'Unauthorized access')
    {
        if (request()->wantsJson()) {
            return response()->json([
                'error' => $message,
                'code' => 'HIERARCHY_ACCESS_DENIED'
            ], 403);
        }

        abort(403, $message);
    }

    /**
     * Apply search and pagination with hierarchy filtering
     */
    protected function applySearchAndPagination(Builder $query, Request $request, string $resourceType, array $searchFields = []): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        // Apply hierarchy filtering first
        $query = $this->applyHierarchyFilter($query, $resourceType);

        // Apply search if provided
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search, $searchFields) {
                foreach ($searchFields as $field) {
                    $q->orWhere($field, 'like', "%{$search}%");
                }
            });
        }

        // Apply sorting
        $sortField = $request->get('sort', 'id');
        $sortDirection = $request->get('direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        // Return paginated results
        $perPage = $request->get('per_page', 15);
        return $query->paginate($perPage);
    }
}
