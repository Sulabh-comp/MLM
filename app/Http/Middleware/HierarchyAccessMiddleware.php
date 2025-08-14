<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Manager;
use App\Models\Employee;
use App\Models\Agency;
use App\Models\Customer;

class HierarchyAccessMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $resource = null): Response
    {
        $user = $this->getAuthenticatedUser($request);
        
        if (!$user) {
            return redirect()->route('login');
        }

        // Admin has access to everything
        if ($this->isAdmin($user)) {
            return $next($request);
        }

        // Check resource-specific access
        if ($resource) {
            if (!$this->checkResourceAccess($user, $request, $resource)) {
                abort(403, 'Unauthorized access to this resource.');
            }
        }

        // Add user context to request for controllers
        $request->merge(['hierarchy_user' => $user]);

        return $next($request);
    }

    /**
     * Get the authenticated user from any guard
     */
    private function getAuthenticatedUser(Request $request)
    {
        // Check admin guard first
        if (auth()->guard('admin')->check()) {
            return auth()->guard('admin')->user();
        }

        // Check manager guard
        if (auth()->guard('manager')->check()) {
            return auth()->guard('manager')->user();
        }

        // Check employee guard
        if (auth()->guard('employee')->check()) {
            return auth()->guard('employee')->user();
        }

        return null;
    }

    /**
     * Check if user is admin
     */
    private function isAdmin($user): bool
    {
        return get_class($user) === 'App\Models\Admin';
    }

    /**
     * Check if user is manager
     */
    private function isManager($user): bool
    {
        return get_class($user) === 'App\Models\Manager';
    }

    /**
     * Check if user is employee
     */
    private function isEmployee($user): bool
    {
        return get_class($user) === 'App\Models\Employee';
    }

    /**
     * Check resource-specific access permissions
     */
    private function checkResourceAccess($user, Request $request, string $resource): bool
    {
        $resourceId = $this->extractResourceId($request, $resource);

        switch ($resource) {
            case 'manager':
                return $this->checkManagerAccess($user, $resourceId);
            
            case 'employee':
                return $this->checkEmployeeAccess($user, $resourceId);
            
            case 'agency':
                return $this->checkAgencyAccess($user, $resourceId);
            
            case 'customer':
                return $this->checkCustomerAccess($user, $resourceId);
            
            default:
                return false;
        }
    }

    /**
     * Extract resource ID from request
     */
    private function extractResourceId(Request $request, string $resource): ?int
    {
        // Try route parameters first
        if ($request->route($resource)) {
            return is_object($request->route($resource)) 
                ? $request->route($resource)->id 
                : $request->route($resource);
        }

        // Try common parameter names
        $paramNames = [$resource, $resource . '_id', 'id'];
        
        foreach ($paramNames as $param) {
            if ($request->has($param)) {
                return $request->get($param);
            }
        }

        return null;
    }

    /**
     * Check manager access permissions
     */
    private function checkManagerAccess($user, ?int $managerId): bool
    {
        if (!$managerId) {
            return true; // Allow access to list/create operations
        }

        $targetManager = Manager::find($managerId);
        if (!$targetManager) {
            return false;
        }

        if ($this->isAdmin($user)) {
            return true;
        }

        if ($this->isManager($user)) {
            // Manager can access themselves and their subordinates
            return $user->id === $managerId || 
                   $user->allSubordinates()->contains('id', $managerId);
        }

        if ($this->isEmployee($user)) {
            // Employee can only view their direct manager and accessible managers
            return $user->canAccessManager($targetManager);
        }

        return false;
    }

    /**
     * Check employee access permissions
     */
    private function checkEmployeeAccess($user, ?int $employeeId): bool
    {
        if (!$employeeId) {
            return true; // Allow access to list/create operations
        }

        $targetEmployee = Employee::find($employeeId);
        if (!$targetEmployee) {
            return false;
        }

        if ($this->isAdmin($user)) {
            return true;
        }

        if ($this->isManager($user)) {
            // Manager can access employees under their hierarchy
            return $user->allEmployees()->contains('id', $employeeId);
        }

        if ($this->isEmployee($user)) {
            // Employee can only access themselves and colleagues
            return $user->id === $employeeId || 
                   $user->colleagues()->where('id', $employeeId)->exists();
        }

        return false;
    }

    /**
     * Check agency access permissions
     */
    private function checkAgencyAccess($user, ?int $agencyId): bool
    {
        if (!$agencyId) {
            return true; // Allow access to list/create operations
        }

        $targetAgency = Agency::find($agencyId);
        if (!$targetAgency) {
            return false;
        }

        if ($this->isAdmin($user)) {
            return true;
        }

        if ($this->isManager($user)) {
            // Manager can access agencies under their hierarchy
            return $user->allAgencies()->contains('id', $agencyId);
        }

        if ($this->isEmployee($user)) {
            // Employee can access their own agencies
            return $user->canAccessAgency($targetAgency);
        }

        return false;
    }

    /**
     * Check customer access permissions
     */
    private function checkCustomerAccess($user, ?int $customerId): bool
    {
        if (!$customerId) {
            return true; // Allow access to list/create operations
        }

        $targetCustomer = Customer::find($customerId);
        if (!$targetCustomer) {
            return false;
        }

        if ($this->isAdmin($user)) {
            return true;
        }

        if ($this->isManager($user)) {
            // Manager can access customers under their hierarchy
            return $user->allCustomers()->contains('id', $customerId);
        }

        if ($this->isEmployee($user)) {
            // Employee can access customers through their agencies
            return $user->canAccessCustomer($targetCustomer);
        }

        return false;
    }
}
