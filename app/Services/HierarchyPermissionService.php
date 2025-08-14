<?php

namespace App\Services;

use App\Models\Manager;
use App\Models\Employee;
use App\Models\Agency;
use App\Models\Customer;
use App\Models\ManagerLevel;
use Illuminate\Support\Collection;

class HierarchyPermissionService
{
    /**
     * Check if a manager can create subordinates at a specific level
     */
    public function canCreateSubordinate(Manager $manager, string $levelName): bool
    {
        $targetLevel = ManagerLevel::where('name', $levelName)->first();
        
        if (!$targetLevel) {
            return false;
        }

        $managerHierarchyLevel = $manager->managerLevel?->hierarchy_level;
        
        if (!$managerHierarchyLevel) {
            return false;
        }

        // Manager can only create subordinates at levels below their own
        return $managerHierarchyLevel < $targetLevel->hierarchy_level;
    }

    /**
     * Check if a manager can manage (edit/delete) another manager
     */
    public function canManageManager(Manager $manager, Manager $targetManager): bool
    {
        // Can't manage yourself
        if ($manager->id === $targetManager->id) {
            return false;
        }

        // Can only manage direct subordinates or managers below in hierarchy
        return $manager->allSubordinates()->contains('id', $targetManager->id);
    }

    /**
     * Check if a manager can create employees
     */
    public function canCreateEmployee(Manager $manager): bool
    {
        // All managers can create employees
        return true;
    }

    /**
     * Check if a manager can manage an employee
     */
    public function canManageEmployee(Manager $manager, Employee $employee): bool
    {
        // Manager can manage employees under their hierarchy
        return $manager->allEmployees()->contains('id', $employee->id);
    }

    /**
     * Check if a manager can access an agency
     */
    public function canAccessAgency(Manager $manager, Agency $agency): bool
    {
        return $manager->allAgencies()->contains('id', $agency->id);
    }

    /**
     * Check if a manager can access a customer
     */
    public function canAccessCustomer(Manager $manager, Customer $customer): bool
    {
        return $manager->allCustomers()->contains('id', $customer->id);
    }

    /**
     * Get available subordinate levels for manager creation
     */
    public function getAvailableSubordinateLevels(Manager $manager): Collection
    {
        $managerHierarchyLevel = $manager->managerLevel?->hierarchy_level;
        
        if (!$managerHierarchyLevel) {
            return collect(); // No levels available if manager level is not set
        }

        return ManagerLevel::where('hierarchy_level', '>', $managerHierarchyLevel)
                          ->orderBy('hierarchy_level')
                          ->get();
    }

    /**
     * Get territorial boundaries for a manager
     */
    public function getTerritorialBoundaries(Manager $manager): array
    {
        return [
            'subordinate_managers' => $manager->allSubordinates(),
            'employees' => $manager->allEmployees(),
            'agencies' => $manager->allAgencies(),
            'customers' => $manager->allCustomers(),
            'total_subordinates' => $manager->allSubordinates()->count(),
            'total_employees' => $manager->allEmployees()->count(),
            'total_agencies' => $manager->allAgencies()->count(),
            'total_customers' => $manager->allCustomers()->count(),
        ];
    }

    /**
     * Check if a manager can approve/reject agencies
     */
    public function canApproveAgency(Manager $manager, Agency $agency): bool
    {
        // Manager can approve agencies under their territorial control
        return $this->canAccessAgency($manager, $agency);
    }

    /**
     * Check if a manager can view reports
     */
    public function canViewReports(Manager $manager, string $reportType = 'all'): bool
    {
        $managerHierarchyLevel = $manager->managerLevel?->hierarchy_level;
        
        if (!$managerHierarchyLevel) {
            return false;
        }

        switch ($reportType) {
            case 'financial':
                // Only senior managers (level 1-3) can view financial reports
                return $managerHierarchyLevel <= 3;
            
            case 'performance':
                // All managers can view performance reports for their territory
                return true;
            
            case 'customer':
                // All managers can view customer reports for their territory
                return true;
            
            default:
                return true;
        }
    }

    /**
     * Get permission matrix for a manager
     */
    public function getPermissionMatrix(Manager $manager): array
    {
        $boundaries = $this->getTerritorialBoundaries($manager);
        $managerHierarchyLevel = $manager->managerLevel?->hierarchy_level ?? 999;
        
        return [
            'level_info' => [
                'level_name' => $manager->level_name,
                'hierarchy_level' => $managerHierarchyLevel,
                'can_create_subordinates' => $managerHierarchyLevel < 6,
            ],
            'creation_permissions' => [
                'can_create_managers' => $managerHierarchyLevel < 6,
                'can_create_employees' => true,
                'available_subordinate_levels' => $this->getAvailableSubordinateLevels($manager)->pluck('name'),
            ],
            'management_permissions' => [
                'subordinate_managers_count' => $boundaries['subordinate_managers']->count(),
                'employees_count' => $boundaries['employees']->count(),
                'agencies_count' => $boundaries['agencies']->count(),
                'customers_count' => $boundaries['customers']->count(),
            ],
            'reporting_permissions' => [
                'can_view_financial_reports' => $this->canViewReports($manager, 'financial'),
                'can_view_performance_reports' => $this->canViewReports($manager, 'performance'),
                'can_view_customer_reports' => $this->canViewReports($manager, 'customer'),
            ],
            'territorial_scope' => [
                'max_depth' => $this->getMaxHierarchyDepth($manager),
                'regional_coverage' => $this->getRegionalCoverage($manager),
            ]
        ];
    }

    /**
     * Get maximum hierarchy depth under this manager
     */
    private function getMaxHierarchyDepth(Manager $manager): int
    {
        $subordinates = $manager->allSubordinates();
        
        if ($subordinates->isEmpty()) {
            return 0;
        }

        return $subordinates->max('depth') - $manager->depth;
    }

    /**
     * Get regional coverage information
     */
    private function getRegionalCoverage(Manager $manager): array
    {
        // This would analyze the geographical spread of the manager's territory
        // For now, return basic information
        return [
            'regions_covered' => $manager->allEmployees()->with('agencies')->get()
                                       ->flatMap->agencies
                                       ->unique('id')
                                       ->count(),
            'geographical_spread' => 'calculated_based_on_agency_locations'
        ];
    }

    /**
     * Validate manager creation request
     */
    public function validateManagerCreation(Manager $createdBy, array $data): array
    {
        $errors = [];

        // Check if creator can create at the requested level
        if (isset($data['level_name'])) {
            if (!$this->canCreateSubordinate($createdBy, $data['level_name'])) {
                $errors['level_name'] = 'You cannot create managers at this level.';
            }
        }

        // Check hierarchy depth limits
        if ($createdBy->depth >= 10) { // Maximum depth limit
            $errors['depth'] = 'Maximum hierarchy depth reached.';
        }

        // Check if parent manager has capacity
        $currentSubordinates = $createdBy->children()->count();
        $maxSubordinates = $this->getMaxSubordinates($createdBy);
        
        if ($currentSubordinates >= $maxSubordinates) {
            $errors['capacity'] = "Maximum subordinate limit ({$maxSubordinates}) reached.";
        }

        return $errors;
    }

    /**
     * Get maximum number of subordinates a manager can have
     */
    private function getMaxSubordinates(Manager $manager): int
    {
        $managerHierarchyLevel = $manager->managerLevel?->hierarchy_level ?? 999;
        
        // Define limits based on hierarchy level
        $limits = [
            1 => 10, // CEO - 10 Regional Managers
            2 => 15, // Regional Manager - 15 Area Managers
            3 => 20, // Area Manager - 20 Zone Managers
            4 => 25, // Zone Manager - 25 Team Leaders
            5 => 30, // Team Leader - 30 Managers
            6 => 0,  // Manager - No subordinate managers
        ];

        return $limits[$managerHierarchyLevel] ?? 0;
    }
}
