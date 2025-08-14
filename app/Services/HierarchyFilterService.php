<?php

namespace App\Services;

use App\Models\Manager;
use App\Models\Employee;
use App\Models\Agency;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class HierarchyFilterService
{
    /**
     * Filter managers query based on user's hierarchy position
     */
    public function filterManagers(Builder $query, $user): Builder
    {
        if ($this->isAdmin($user)) {
            return $query; // Admin sees all
        }

        if ($this->isManager($user)) {
            // Manager sees themselves and all subordinates
            $accessibleIds = $user->allSubordinates()->pluck('id')->toArray();
            return $query->whereIn('id', $accessibleIds);
        }

        if ($this->isEmployee($user)) {
            // Employee sees their manager and accessible managers
            $accessibleIds = $user->accessibleManagers()->pluck('id')->toArray();
            return $query->whereIn('id', $accessibleIds);
        }

        return $query->whereRaw('1 = 0'); // No access
    }

    /**
     * Filter employees query based on user's hierarchy position
     */
    public function filterEmployees(Builder $query, $user): Builder
    {
        if ($this->isAdmin($user)) {
            return $query; // Admin sees all
        }

        if ($this->isManager($user)) {
            // Manager sees all employees under their hierarchy
            $accessibleIds = $user->allEmployees()->pluck('id')->toArray();
            return $query->whereIn('id', $accessibleIds);
        }

        if ($this->isEmployee($user)) {
            // Employee sees themselves and colleagues
            $colleagueIds = $user->colleagues()->pluck('id')->toArray();
            $colleagueIds[] = $user->id; // Add self
            return $query->whereIn('id', $colleagueIds);
        }

        return $query->whereRaw('1 = 0'); // No access
    }

    /**
     * Filter agencies query based on user's hierarchy position
     */
    public function filterAgencies(Builder $query, $user): Builder
    {
        if ($this->isAdmin($user)) {
            return $query; // Admin sees all
        }

        if ($this->isManager($user)) {
            // Manager sees all agencies under their hierarchy
            $accessibleIds = $user->allAgencies()->pluck('id')->toArray();
            return $query->whereIn('id', $accessibleIds);
        }

        if ($this->isEmployee($user)) {
            // Employee sees only their agencies
            return $query->where('employee_id', $user->id);
        }

        return $query->whereRaw('1 = 0'); // No access
    }

    /**
     * Filter customers query based on user's hierarchy position
     */
    public function filterCustomers(Builder $query, $user): Builder
    {
        if ($this->isAdmin($user)) {
            return $query; // Admin sees all
        }

        if ($this->isManager($user)) {
            // Manager sees all customers under their hierarchy
            $accessibleIds = $user->allCustomers()->pluck('id')->toArray();
            return $query->whereIn('id', $accessibleIds);
        }

        if ($this->isEmployee($user)) {
            // Employee sees customers through their agencies
            $agencyIds = $user->agencies()->pluck('id')->toArray();
            return $query->whereIn('agency_id', $agencyIds);
        }

        return $query->whereRaw('1 = 0'); // No access
    }

    /**
     * Get filtered statistics for dashboard
     */
    public function getFilteredStats($user): array
    {
        if ($this->isAdmin($user)) {
            return [
                'managers' => Manager::count(),
                'employees' => Employee::count(),
                'agencies' => Agency::count(),
                'customers' => Customer::count(),
                'scope' => 'Global (Admin)'
            ];
        }

        if ($this->isManager($user)) {
            return [
                'managers' => $user->allSubordinates()->count(),
                'employees' => $user->allEmployees()->count(),
                'agencies' => $user->allAgencies()->count(),
                'customers' => $user->allCustomers()->count(),
                'scope' => "Hierarchical ({$user->level_name})"
            ];
        }

        if ($this->isEmployee($user)) {
            return [
                'managers' => $user->accessibleManagers()->count(),
                'employees' => $user->colleagues()->count() + 1, // +1 for self
                'agencies' => $user->agencies()->count(),
                'customers' => $user->customers()->count(),
                'scope' => 'Employee Level'
            ];
        }

        return [
            'managers' => 0,
            'employees' => 0,
            'agencies' => 0,
            'customers' => 0,
            'scope' => 'No Access'
        ];
    }

    /**
     * Apply territorial filtering to any query
     */
    public function applyTerritorialFilter(Builder $query, $user, string $resourceType): Builder
    {
        switch ($resourceType) {
            case 'managers':
                return $this->filterManagers($query, $user);
            case 'employees':
                return $this->filterEmployees($query, $user);
            case 'agencies':
                return $this->filterAgencies($query, $user);
            case 'customers':
                return $this->filterCustomers($query, $user);
            default:
                return $query;
        }
    }

    /**
     * Get searchable resources for a user
     */
    public function getSearchableResources($user): array
    {
        $resources = [];

        if ($this->isAdmin($user)) {
            $resources = ['managers', 'employees', 'agencies', 'customers'];
        } elseif ($this->isManager($user)) {
            $resources = ['managers', 'employees', 'agencies', 'customers'];
        } elseif ($this->isEmployee($user)) {
            $resources = ['employees', 'agencies', 'customers'];
        }

        return $resources;
    }

    /**
     * Get accessible resource IDs for bulk operations
     */
    public function getAccessibleResourceIds($user, string $resourceType): array
    {
        switch ($resourceType) {
            case 'managers':
                if ($this->isAdmin($user)) {
                    return Manager::pluck('id')->toArray();
                } elseif ($this->isManager($user)) {
                    return $user->allSubordinates()->pluck('id')->toArray();
                } elseif ($this->isEmployee($user)) {
                    return $user->accessibleManagers()->pluck('id')->toArray();
                }
                break;

            case 'employees':
                if ($this->isAdmin($user)) {
                    return Employee::pluck('id')->toArray();
                } elseif ($this->isManager($user)) {
                    return $user->allEmployees()->pluck('id')->toArray();
                } elseif ($this->isEmployee($user)) {
                    $colleagueIds = $user->colleagues()->pluck('id')->toArray();
                    $colleagueIds[] = $user->id;
                    return $colleagueIds;
                }
                break;

            case 'agencies':
                if ($this->isAdmin($user)) {
                    return Agency::pluck('id')->toArray();
                } elseif ($this->isManager($user)) {
                    return $user->allAgencies()->pluck('id')->toArray();
                } elseif ($this->isEmployee($user)) {
                    return $user->agencies()->pluck('id')->toArray();
                }
                break;

            case 'customers':
                if ($this->isAdmin($user)) {
                    return Customer::pluck('id')->toArray();
                } elseif ($this->isManager($user)) {
                    return $user->allCustomers()->pluck('id')->toArray();
                } elseif ($this->isEmployee($user)) {
                    return $user->customers()->pluck('id')->toArray();
                }
                break;
        }

        return [];
    }

    /**
     * Validate if user can access specific resource IDs
     */
    public function validateResourceAccess($user, string $resourceType, array $resourceIds): array
    {
        $accessibleIds = $this->getAccessibleResourceIds($user, $resourceType);
        $unauthorizedIds = array_diff($resourceIds, $accessibleIds);
        
        return [
            'authorized' => array_intersect($resourceIds, $accessibleIds),
            'unauthorized' => $unauthorizedIds,
            'has_unauthorized' => !empty($unauthorizedIds)
        ];
    }

    /**
     * Get export data with proper filtering
     */
    public function getFilteredExportData($user, string $resourceType, array $columns = ['*']): Collection
    {
        switch ($resourceType) {
            case 'managers':
                $query = Manager::select($columns);
                return $this->filterManagers($query, $user)->get();

            case 'employees':
                $query = Employee::select($columns);
                return $this->filterEmployees($query, $user)->get();

            case 'agencies':
                $query = Agency::select($columns);
                return $this->filterAgencies($query, $user)->get();

            case 'customers':
                $query = Customer::select($columns);
                return $this->filterCustomers($query, $user)->get();

            default:
                return collect();
        }
    }

    /**
     * Helper methods to check user types
     */
    private function isAdmin($user): bool
    {
        return get_class($user) === 'App\Models\Admin';
    }

    private function isManager($user): bool
    {
        return get_class($user) === 'App\Models\Manager';
    }

    private function isEmployee($user): bool
    {
        return get_class($user) === 'App\Models\Employee';
    }
}
