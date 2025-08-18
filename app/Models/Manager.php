<?php

namespace App\Models;

use Illuminate\Support\Str;
use App\Mail\UserCreationMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Manager extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $guard = 'manager';
    
    public $isHierarchyUpdating = false;

    protected $fillable = [
        'name', 'email', 'phone', 'designation', 'region_id', 'password',
        'parent_id', 'level_name', 'depth', 'hierarchy_path',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    /**
     * Get the manager level
     */
    public function managerLevel()
    {
        return $this->belongsTo(ManagerLevel::class, 'level_name', 'name');
    }

    /**
     * Get direct employees reporting to this manager
     */
    public function employees()
    {
        return $this->hasMany(Employee::class);
    }

    /**
     * Get all employees accessible to this manager (direct + subordinates' employees)
     * Alias for allTerritorialEmployees for backward compatibility
     */
    public function allEmployees()
    {
        // Get all subordinate manager IDs plus the current manager's ID
        $subordinateManagerIds = $this->allSubordinates()->pluck('id')->toArray();
        $subordinateManagerIds[] = $this->id; // Include current manager's direct employees
        
        return Employee::whereIn('manager_id', $subordinateManagerIds);
    }

    /**
     * Get all employees in this manager's territory (including subordinate managers' employees)
     */
    public function allTerritorialEmployees()
    {
        $subordinateManagerIds = $this->allSubordinates()->pluck('id')->toArray();
        $subordinateManagerIds[] = $this->id; // Include current manager's direct employees
        
        return Employee::whereIn('manager_id', $subordinateManagerIds);
    }

    /**
     * Get all agencies through direct employees
     */
    public function agencies()
    {
        return Agency::whereHas('employee', function($query) {
            $query->where('manager_id', $this->id);
        });
    }

    /**
     * Get all agencies in this manager's territory (including subordinate managers' agencies)
     */
    public function allTerritorialAgencies()
    {
        $employeeIds = $this->allTerritorialEmployees()->pluck('id')->toArray();
        
        return Agency::whereIn('employee_id', $employeeIds);
    }

    /**
     * Get all agencies accessible to this manager (alias for allTerritorialAgencies)
     */
    public function allAgencies()
    {
        return $this->allTerritorialAgencies();
    }

    /**
     * Get customers through direct employees' agencies
     */
    public function customers()
    {
        return Customer::whereHas('agency.employee', function($query) {
            $query->where('manager_id', $this->id);
        });
    }

    /**
     * Get all customers in this manager's territory (including subordinate managers' customers)
     */
    public function allTerritorialCustomers()
    {
        $agencyIds = $this->allTerritorialAgencies()->pluck('id')->toArray();
        
        return Customer::whereIn('agency_id', $agencyIds);
    }

    /**
     * Get all customers accessible to this manager (alias for allTerritorialCustomers)
     */
    public function allCustomers()
    {
        return $this->allTerritorialCustomers();
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class,'user_id','id')->where('model', self::class);
    }

    /**
     * Get complete territorial statistics
     */
    public function getTerritorialStats()
    {
        return [
            'direct_employees' => $this->employees()->count(),
            'total_employees' => $this->allTerritorialEmployees()->count(),
            'direct_agencies' => $this->agencies()->count(),
            'total_agencies' => $this->allTerritorialAgencies()->count(),
            'direct_customers' => $this->customers()->count(),
            'total_customers' => $this->allTerritorialCustomers()->count(),
            'subordinate_managers' => $this->allSubordinates()->count(),
        ];
    }

    /**
     * Check if manager can access specific employee
     */
    public function canAccessEmployee(Employee $employee)
    {
        return $this->allTerritorialEmployees()->where('id', $employee->id)->exists();
    }

    /**
     * Check if manager can access specific agency
     */
    public function canAccessAgency(Agency $agency)
    {
        return $this->allTerritorialAgencies()->where('id', $agency->id)->exists();
    }

    /**
     * Check if manager can access specific customer
     */
    public function canAccessCustomer(Customer $customer)
    {
        return $this->allTerritorialCustomers()->where('id', $customer->id)->exists();
    }

    // === ENHANCED HIERARCHY RELATIONSHIPS ===
    
    /**
     * Get the parent manager
     */
    public function parent()
    {
        return $this->belongsTo(Manager::class, 'parent_id');
    }

    /**
     * Get direct children managers
     */
    public function children()
    {
        return $this->hasMany(Manager::class, 'parent_id')->orderBy('hierarchy_path');
    }

    /**
     * Get all subordinate managers (recursive with eager loading)
     */
    public function subordinates()
    {
        return $this->hasMany(Manager::class, 'parent_id')->with(['subordinates', 'managerLevel']);
    }

    /**
     * Get all managers in this manager's hierarchy branch (including self)
     */
    public function allSubordinates()
    {
        $subordinates = collect([]);
        $this->loadMissing('subordinates');

        foreach ($this->subordinates as $subordinate) {
            $subordinates = $subordinates->merge($subordinate->allSubordinates());
        }
        return $subordinates;
    }

    /**
     * Get all subordinates using hierarchy path (more efficient for large trees)
     */
    public function getSubordinatesByPath(): Collection
    {
        if (!$this->hierarchy_path) {
            return collect();
        }

        return static::where('hierarchy_path', 'like', $this->hierarchy_path . '%')
                    ->where('id', '!=', $this->id)
                    ->with(['managerLevel', 'region'])
                    ->orderBy('hierarchy_path')
                    ->get();
    }

    /**
     * Get ancestors (all parent managers up the hierarchy)
     */
    public function ancestors(): Collection
    {
        $ancestors = collect();
        $current = $this->parent;
        
        while ($current) {
            $ancestors->push($current);
            $current = $current->parent;
        }
        
        return $ancestors;
    }

    /**
     * Get ancestors using hierarchy path (more efficient)
     */
    public function getAncestorsByPath(): Collection
    {
        if (!$this->hierarchy_path) {
            return collect();
        }

        $pathIds = array_filter(explode('/', $this->hierarchy_path));
        array_pop($pathIds); // Remove self

        if (empty($pathIds)) {
            return collect();
        }

        return static::whereIn('id', $pathIds)
                    ->with(['managerLevel', 'region'])
                    ->orderBy('depth')
                    ->get();
    }

    /**
     * Get siblings (managers with same parent)
     */
    public function siblings(): Collection
    {
        if (!$this->parent_id) {
            return static::whereNull('parent_id')
                        ->where('id', '!=', $this->id)
                        ->with(['managerLevel', 'region'])
                        ->get();
        }

        return static::where('parent_id', $this->parent_id)
                    ->where('id', '!=', $this->id)
                    ->with(['managerLevel', 'region'])
                    ->orderBy('hierarchy_path')
                    ->get();
    }

    /**
     * Get descendant count at specific depth
     */
    public function getDescendantCountAtDepth(int $relativeDepth): int
    {
        $targetDepth = $this->depth + $relativeDepth;
        
        return static::where('hierarchy_path', 'like', $this->hierarchy_path . '%')
                    ->where('depth', $targetDepth)
                    ->count();
    }

    /**
     * Check if this manager is a subordinate of given manager
     */
    public function isSubordinateOf(Manager $manager): bool
    {
        if (!$this->hierarchy_path || !$manager->hierarchy_path) {
            return false;
        }

        return str_starts_with($this->hierarchy_path, $manager->hierarchy_path) 
               && $this->id !== $manager->id;
    }

    /**
     * Check if this manager is an ancestor of given manager
     */
    public function isAncestorOf(Manager $manager): bool
    {
        return $manager->isSubordinateOf($this);
    }

    /**
     * Check if managers are in the same branch
     */
    public function isInSameBranch(Manager $manager): bool
    {
        if (!$this->hierarchy_path || !$manager->hierarchy_path) {
            return false;
        }

        $thisPath = explode('/', trim($this->hierarchy_path, '/'));
        $otherPath = explode('/', trim($manager->hierarchy_path, '/'));
        
        $minLength = min(count($thisPath), count($otherPath));
        
        for ($i = 0; $i < $minLength; $i++) {
            if ($thisPath[$i] !== $otherPath[$i]) {
                return false;
            }
        }
        
        return true;
    }

    /**
     * Get the common ancestor with another manager
     */
    public function getCommonAncestor(Manager $manager): ?Manager
    {
        if (!$this->hierarchy_path || !$manager->hierarchy_path) {
            return null;
        }

        $thisPath = array_filter(explode('/', $this->hierarchy_path));
        $otherPath = array_filter(explode('/', $manager->hierarchy_path));
        
        $commonIds = [];
        $minLength = min(count($thisPath), count($otherPath));
        
        for ($i = 0; $i < $minLength; $i++) {
            if ($thisPath[$i] === $otherPath[$i]) {
                $commonIds[] = $thisPath[$i];
            } else {
                break;
            }
        }
        
        if (empty($commonIds)) {
            return null;
        }
        
        $commonAncestorId = end($commonIds);
        return static::find($commonAncestorId);
    }

    // === HIERARCHY TREE METHODS ===

    /**
     * Build hierarchy tree structure
     */
    public function buildTree(): array
    {
        return $this->buildSubTree($this);
    }

    /**
     * Build subtree for given manager
     */
    private function buildSubTree(Manager $manager): array
    {
        $tree = [
            'id' => $manager->id,
            'name' => $manager->name,
            'email' => $manager->email,
            'level_name' => $manager->level_name,
            'hierarchy_level' => $manager->managerLevel?->hierarchy_level,
            'depth' => $manager->depth,
            'code' => $manager->code,
            'children' => []
        ];

        foreach ($manager->children as $child) {
            $tree['children'][] = $this->buildSubTree($child);
        }

        return $tree;
    }

    /**
     * Get hierarchy as flat list with indentation
     */
    public function getHierarchyList(string $indent = '— '): Collection
    {
        $list = collect();
        $this->addToHierarchyList($this, $list, '', $indent);
        return $list;
    }

    /**
     * Add manager and children to hierarchy list
     */
    private function addToHierarchyList(Manager $manager, Collection &$list, string $currentIndent, string $indent): void
    {
        $list->push([
            'id' => $manager->id,
            'display_name' => $currentIndent . $manager->name,
            'level_name' => $manager->level_name,
            'depth' => $manager->depth,
            'manager' => $manager
        ]);

        foreach ($manager->children as $child) {
            $this->addToHierarchyList($child, $list, $currentIndent . $indent, $indent);
        }
    }

    /**
     * Get managers available as parents for this manager
     */
    public function getAvailableParents(): Collection
    {
        // Cannot be parent of yourself or your subordinates
        $excludeIds = $this->getSubordinatesByPath()->pluck('id')->push($this->id);
        
        return static::whereNotIn('id', $excludeIds)
                    ->with(['managerLevel', 'region'])
                    ->orderBy('hierarchy_path')
                    ->get();
    }

    // === PERMISSION METHODS ===

    /**
     * Check if manager can create subordinate managers
     */
    public function canCreateSubordinates(): bool
    {
        if (!$this->managerLevel) {
            return false;
        }

        // Check if manager level allows subordinate creation
        $permissions = $this->managerLevel->permissions ?? [];
        
        return in_array('create_subordinates', $permissions) || 
               $this->managerLevel->hierarchy_level <= 3; // CEO, RM, AM can create by default
    }

    /**
     * Check if manager can manage specific subordinate
     */
    public function canManageSubordinate(Manager $subordinate): bool
    {
        return $subordinate->isSubordinateOf($this);
    }

    /**
     * Check if manager can create manager at specific level
     */
    public function canCreateManagerAtLevel(ManagerLevel $level): bool
    {
        if (!$this->canCreateSubordinates()) {
            return false;
        }

        if (!$this->managerLevel) {
            return false;
        }

        // Can only create managers at levels below their own
        return $level->hierarchy_level > $this->managerLevel->hierarchy_level;
    }

    /**
     * Get manager levels this manager can assign to subordinates
     */
    public function getAssignableLevels(): Collection
    {
        if (!$this->canCreateSubordinates() || !$this->managerLevel) {
            return collect();
        }

        return ManagerLevel::where('hierarchy_level', '>', $this->managerLevel->hierarchy_level)
                          ->where('status', true)
                          ->orderBy('hierarchy_level')
                          ->get();
    }

    /**
     * Check if manager has permission for specific action
     */
    public function hasPermission(string $permission): bool
    {
        if (!$this->managerLevel) {
            return false;
        }

        $permissions = $this->managerLevel->permissions ?? [];
        return in_array($permission, $permissions);
    }

    /**
     * Get all permissions for this manager
     */
    public function getAllPermissions(): array
    {
        if (!$this->managerLevel) {
            return [];
        }

        $permissions = $this->managerLevel->permissions ?? [];
        
        // Add default permissions based on hierarchy level
        $defaultPermissions = $this->getDefaultPermissionsByLevel();
        
        return array_unique(array_merge($permissions, $defaultPermissions));
    }

    /**
     * Get default permissions based on hierarchy level
     */
    private function getDefaultPermissionsByLevel(): array
    {
        if (!$this->managerLevel) {
            return [];
        }

        return match($this->managerLevel->hierarchy_level) {
            1 => ['create_subordinates', 'manage_all', 'view_all_reports', 'manage_levels'], // CEO
            2 => ['create_subordinates', 'manage_region', 'view_region_reports'], // Regional Manager
            3 => ['create_subordinates', 'manage_area', 'view_area_reports'], // Area Manager
            4 => ['create_subordinates', 'manage_zone', 'view_zone_reports'], // Zone Manager
            5 => ['manage_team', 'view_team_reports'], // Team Leader
            default => ['view_own_reports'] // Manager and below
        };
    }

    /**
     * Check if manager can access specific geographic scope
     */
    public function canAccessRegion(int $regionId): bool
    {
        // Can access own region
        if ($this->region_id === $regionId) {
            return true;
        }

        // Check if any subordinates are in this region
        return $this->getSubordinatesByPath()
                   ->where('region_id', $regionId)
                   ->isNotEmpty();
    }

    // === STATIC HIERARCHY QUERY METHODS ===

    /**
     * Get all top-level managers (no parent)
     */
    public static function getTopLevelManagers(): Collection
    {
        return static::whereNull('parent_id')
                    ->with(['managerLevel', 'region', 'children'])
                    ->orderBy('hierarchy_path')
                    ->get();
    }

    /**
     * Get complete hierarchy tree
     */
    public static function getHierarchyTree(): Collection
    {
        $topLevel = static::getTopLevelManagers();
        
        return $topLevel->map(function ($manager) {
            return $manager->buildTree();
        });
    }

    /**
     * Get managers by level
     */
    public static function getByLevel(string $levelName): Collection
    {
        return static::where('level_name', $levelName)
                    ->with(['managerLevel', 'region', 'parent'])
                    ->orderBy('hierarchy_path')
                    ->get();
    }

    /**
     * Get managers at specific depth
     */
    public static function getByDepth(int $depth): Collection
    {
        return static::where('depth', $depth)
                    ->with(['managerLevel', 'region', 'parent'])
                    ->orderBy('hierarchy_path')
                    ->get();
    }

    /**
     * Find managers within hierarchy path
     */
    public static function findInHierarchyPath(string $path): Collection
    {
        return static::where('hierarchy_path', 'like', $path . '%')
                    ->with(['managerLevel', 'region'])
                    ->orderBy('hierarchy_path')
                    ->get();
    }

    /**
     * Get orphaned managers (have parent_id but parent doesn't exist)
     */
    public static function getOrphanedManagers(): Collection
    {
        return static::whereNotNull('parent_id')
                    ->whereNotExists(function ($query) {
                        $query->select(DB::raw(1))
                              ->from('managers as parent')
                              ->whereColumn('parent.id', 'managers.parent_id');
                    })
                    ->get();
    }

    /**
     * Rebuild all hierarchy paths (useful for data cleanup)
     */
    public static function rebuildHierarchyPaths(): int
    {
        $updated = 0;
        $topLevel = static::whereNull('parent_id')->get();
        
        foreach ($topLevel as $manager) {
            $updated += $manager->rebuildBranchPaths();
        }
        
        return $updated;
    }

    /**
     * Rebuild hierarchy paths for this branch
     */
    public function rebuildBranchPaths(): int
    {
        $updated = 0;
        $this->updateHierarchyPath();
        $updated++;
        
        foreach ($this->children as $child) {
            $updated += $child->rebuildBranchPaths();
        }
        
        return $updated;
    }

    /**
     * Get hierarchy statistics
     */
    public static function getHierarchyStats(): array
    {
        $stats = [
            'total_managers' => static::count(),
            'top_level_managers' => static::whereNull('parent_id')->count(),
            'max_depth' => static::max('depth') ?? 0,
            'levels_in_use' => static::distinct('level_name')->count('level_name'),
            'managers_by_level' => [],
            'managers_by_depth' => [],
        ];

        // Get managers count by level
        $levelCounts = static::select('level_name', DB::raw('count(*) as count'))
                           ->whereNotNull('level_name')
                           ->groupBy('level_name')
                           ->get()
                           ->pluck('count', 'level_name')
                           ->toArray();
        
        $stats['managers_by_level'] = $levelCounts;

        // Get managers count by depth
        $depthCounts = static::select('depth', DB::raw('count(*) as count'))
                           ->groupBy('depth')
                           ->orderBy('depth')
                           ->get()
                           ->pluck('count', 'depth')
                           ->toArray();
        
        $stats['managers_by_depth'] = $depthCounts;

        return $stats;
    }

    /**
     * Validate hierarchy integrity
     */
    public static function validateHierarchy(): array
    {
        $issues = [];

        // Check for circular references
        $managers = static::whereNotNull('parent_id')->get();
        foreach ($managers as $manager) {
            $visited = [];
            $current = $manager;
            
            while ($current && $current->parent_id) {
                if (in_array($current->id, $visited)) {
                    $issues[] = "Circular reference detected for manager ID: {$manager->id}";
                    break;
                }
                $visited[] = $current->id;
                $current = $current->parent;
            }
        }

        // Check for orphaned managers
        $orphaned = static::getOrphanedManagers();
        if ($orphaned->isNotEmpty()) {
            $issues[] = "Orphaned managers found: " . $orphaned->pluck('id')->implode(', ');
        }

        // Check hierarchy path consistency
        $managersWithIncorrectPaths = static::get()->filter(function ($manager) {
            $calculatedPath = $manager->calculateHierarchyPath();
            return $calculatedPath !== $manager->hierarchy_path;
        });

        if ($managersWithIncorrectPaths->isNotEmpty()) {
            $issues[] = "Incorrect hierarchy paths for managers: " . 
                       $managersWithIncorrectPaths->pluck('id')->implode(', ');
        }

        return $issues;
    }

    /**
     * Calculate what the hierarchy path should be
     */
    public function calculateHierarchyPath(): string
    {
        $path = [];
        $current = $this->parent;
        $visited = [$this->id];
        
        while ($current && !in_array($current->id, $visited)) {
            array_unshift($path, $current->id);
            $visited[] = $current->id;
            $current = $current->parent;
        }
        
        array_push($path, $this->id);
        return '/' . implode('/', $path) . '/';
    }

    // === UTILITY METHODS ===

    /**
     * Get the hierarchy path as an array of manager IDs
     */
    public function getHierarchyPath(): array
    {
        if ($this->hierarchy_path) {
            return array_filter(explode('/', $this->hierarchy_path));
        }
        
        $path = [];
        $current = $this;
        
        while ($current) {
            array_unshift($path, $current->id);
            $current = $current->parent;
        }
        
        return $path;
    }

    /**
     * Get hierarchy breadcrumb for display
     */
    public function getHierarchyBreadcrumb(string $separator = ' → '): string
    {
        $ancestors = $this->getAncestorsByPath();
        $breadcrumb = [];
        
        foreach ($ancestors as $ancestor) {
            $breadcrumb[] = $ancestor->name . ' (' . ($ancestor->level_name ?? 'No Level') . ')';
        }
        
        $breadcrumb[] = $this->name . ' (' . ($this->level_name ?? 'No Level') . ')';
        
        return implode($separator, $breadcrumb);
    }

    /**
     * Get manager's team size (total subordinates)
     */
    public function getTeamSize(): int
    {
        return $this->getSubordinatesByPath()->count();
    }

    /**
     * Get direct reports count
     */
    public function getDirectReportsCount(): int
    {
        return $this->children()->count();
    }

    /**
     * Get span of control (how many levels deep)
     */
    public function getSpanOfControl(): int
    {
        $subordinates = $this->getSubordinatesByPath();
        
        if ($subordinates->isEmpty()) {
            return 0;
        }
        
        $maxDepth = $subordinates->max('depth');
        return $maxDepth - $this->depth;
    }

    /**
     * Check if manager is at top level
     */
    public function isTopLevel(): bool
    {
        return is_null($this->parent_id);
    }

    /**
     * Check if manager is a leaf (has no subordinates)
     */
    public function isLeaf(): bool
    {
        return $this->children()->count() === 0;
    }

    /**
     * Get manager's territory (regions they can access)
     */
    public function getTerritory(): Collection
    {
        $regionIds = collect([$this->region_id]);
        
        // Add regions of all subordinates
        $subordinateRegions = $this->getSubordinatesByPath()
                                   ->pluck('region_id')
                                   ->unique()
                                   ->filter();
        
        $regionIds = $regionIds->merge($subordinateRegions)->unique();
        
        return Region::whereIn('id', $regionIds)->get();
    }

    /**
     * Update hierarchy path for this manager and all subordinates
     */
    public function updateHierarchyPath()
    {
        if ($this->isHierarchyUpdating) {
            return; // Prevent infinite recursion
        }
        
        $this->isHierarchyUpdating = true;
        
        $path = [];
        $current = $this->parent;
        $visited = [$this->id]; // Track visited nodes to prevent infinite loops
        
        while ($current && !in_array($current->id, $visited)) {
            array_unshift($path, $current->id);
            $visited[] = $current->id;
            $current = $current->parent;
        }
        
        array_push($path, $this->id);
        $hierarchy_path = '/' . implode('/', $path) . '/';
        $depth = count($path) - 1;
        
        // Update database directly to avoid triggering events
        \DB::table('managers')->where('id', $this->id)->update([
            'hierarchy_path' => $hierarchy_path,
            'depth' => $depth
        ]);
        
        // Update the model instance
        $this->hierarchy_path = $hierarchy_path;
        $this->depth = $depth;
        
        $this->isHierarchyUpdating = false;
        
        // Update all children
        foreach ($this->children as $child) {
            $child->updateHierarchyPath();
        }
    }

    /**
     * Generate unique code: MAN + First 2 letters of first name + First 2 letters of last name + Zero-padded ID
     */
    public function generateCode()
    {
        $nameParts = explode(' ', $this->name ?? '');
        $firstName = strtoupper(substr($nameParts[0] ?? '', 0, 2));
        $lastName = strtoupper(substr($nameParts[1] ?? '', 0, 2));
        $random = rand(10000, 99999);
        $code = 'MAN' . $firstName . $lastName . $random;
        if (self::where('code', $code)->exists()) {
            return $this->generateCode(); // Recursively generate a new code
        }

        return $code;
    }

    protected static function booted()
    {
        static::addGlobalScope('search', function ($builder) {
            if ($search = request()->query('q')) {
                $builder->where('name', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%');
            }
        });
    }
    
    protected static function boot(){
        parent::boot();

        static::updated(function ($manager) {
            // If parent_id changed, update hierarchy paths (but avoid infinite loops)
            if ($manager->isDirty('parent_id') && !$manager->isHierarchyUpdating) {
                $manager->updateHierarchyPath();
            }
        });

        static::creating(function ($model) {
            $password = Str::random(10);
            $model->password = bcrypt($password);
        });
        
        static::created(function ($manager) {
            // Refresh the manager to ensure we have the ID
            $manager->refresh();
            
            // Generate and save code directly to database without triggering events
            $code = $manager->generateCode();
            \DB::table('managers')->where('id', $manager->id)->update(['code' => $code]);
            
            // Update the model instance
            $manager->code = $code;
            
            // Update hierarchy path after creation
            $manager->updateHierarchyPath();
            
            // Email sending disabled temporarily to prevent timeout issues
            $password = Str::random(10);
            $manager->password = bcrypt($password);
            $manager->saveQuietly();

            // send email to manager
            Mail::to($manager->email)->send(new UserCreationMail($manager, $password, 'manager'));

        });

        static::updating(function($model) {
            if ($model->isDirty('status') && $model->status == 1) {
                // generate password
                $password = Str::random(10);
                $model->password = bcrypt($password);

                // send email to manager
                Mail::to($model->email)->send(new UserCreationMail($model, $password, 'manager'));
            }
        });
    }
}
