<?php

namespace App\Http\Controllers\Admin;

use App\Models\Manager;
use App\Models\Region;
use App\Models\ManagerLevel;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Traits\HierarchyAccessControl;
use App\Http\Requests\CreateManagerRequest;
use App\Services\HierarchyPermissionService;

class ManagerController extends Controller
{
    use HierarchyAccessControl;

    protected $permissionService;

    public function __construct()
    {
        $this->initializeHierarchyAccessControl();
        $this->permissionService = new HierarchyPermissionService();
    }

    public function index(Request $request)
    {
        // Build hierarchy tree for display
        $hierarchyTree = $this->buildHierarchyTree();
        
        // Get flat list with filtering
        $managers = Manager::with(['region', 'parent', 'managerLevel', 'children', 'employees'])
                          ->when($request->search, function($query, $search) {
                              $query->where('name', 'like', "%{$search}%")
                                   ->orWhere('email', 'like', "%{$search}%")
                                   ->orWhere('designation', 'like', "%{$search}%");
                          })
                          ->when($request->level, function($query, $level) {
                              $query->where('level_name', $level);
                          })
                          ->when($request->region, function($query, $region) {
                              $query->where('region_id', $region);
                          })
                          ->orderBy('hierarchy_path')
                          ->paginate(15);

        $managerLevels = ManagerLevel::active()->orderBy('hierarchy_level')->get();
        $regions = Region::where('status', 1)->get();
        $stats = $this->getManagerStats();

        return view('admin.managers.index', compact(
            'managers', 'hierarchyTree', 'managerLevels', 'regions', 'stats'
        ));
    }

    public function create()
    {
        $managerLevels = ManagerLevel::active()->orderBy('hierarchy_level')->get();
        $regions = Region::where('status', 1)->get();
        $potentialParents = Manager::with('managerLevel')->orderBy('hierarchy_path')->get();
        
        return view('admin.managers.create', compact('managerLevels', 'regions', 'potentialParents'));
    }

    public function store(CreateManagerRequest $request)
    {
        try {
            $manager = Manager::create($request->validated());

            // Log the creation
            \Log::info('Manager created via admin interface', [
                'manager_id' => $manager->id,
                'manager_name' => $manager->name,
                'level' => $manager->level_name,
                'parent_id' => $manager->parent_id,
                'created_by' => auth()->guard('admin')->user()->name
            ]);

            return redirect()->route('admin.managers.index')
                           ->with('success', 'Manager created successfully');
        } catch (\Exception $e) {
            \Log::error('Manager creation failed', [
                'error' => $e->getMessage(),
                'data' => $request->validated()
            ]);

            return back()->withInput()
                        ->with('error', 'Failed to create manager: ' . $e->getMessage());
        }
    }

    public function edit(Manager $manager)
    {
        $managerLevels = ManagerLevel::active()->orderBy('hierarchy_level')->get();
        $regions = Region::where('status', 1)->get();
        
        // Get potential parents (excluding self and descendants)
        $excludeIds = $manager->allSubordinates()->pluck('id')->toArray();
        $excludeIds[] = $manager->id;
        
        $potentialParents = Manager::whereNotIn('id', $excludeIds)
                                  ->with('managerLevel')
                                  ->orderBy('hierarchy_path')
                                  ->get();

        return view('admin.managers.edit', compact(
            'manager', 'managerLevels', 'regions', 'potentialParents'
        ));
    }

    public function update(Request $request, Manager $manager)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:managers,email,' . $manager->id,
            'phone' => 'required|string|max:20',
            'designation' => 'required|string|max:255',
            'region_id' => 'required|exists:regions,id',
            'level_name' => 'required|exists:manager_levels,name',
            'parent_id' => 'nullable|exists:managers,id',
            'territory_name' => 'nullable|string|max:255',
            'territory_description' => 'nullable|string|max:1000',
        ]);

        // Validate hierarchy rules
        if ($request->parent_id) {
            // Check for circular reference
            if ($manager->allSubordinates()->contains('id', $request->parent_id)) {
                return back()->withInput()
                           ->with('error', 'Cannot assign a subordinate as parent - this would create a circular reference.');
            }

            // Check level hierarchy
            $parentManager = Manager::find($request->parent_id);
            $targetLevel = ManagerLevel::where('name', $request->level_name)->first();
            
            if ($parentManager && $targetLevel && 
                $parentManager->managerLevel && 
                $targetLevel->hierarchy_level <= $parentManager->managerLevel->hierarchy_level) {
                return back()->withInput()
                           ->with('error', 'Child manager level must be below parent manager level.');
            }
        }

        try {
            $manager->update($request->only([
                'name', 'email', 'phone', 'designation', 'region_id', 
                'level_name', 'parent_id', 'territory_name', 'territory_description'
            ]));

            \Log::info('Manager updated via admin interface', [
                'manager_id' => $manager->id,
                'manager_name' => $manager->name,
                'updated_by' => auth()->guard('admin')->user()->name
            ]);

            return redirect()->route('admin.managers.index')
                           ->with('success', 'Manager updated successfully');
        } catch (\Exception $e) {
            return back()->withInput()
                        ->with('error', 'Failed to update manager: ' . $e->getMessage());
        }
    }

    public function destroy(Manager $manager)
    {
        // Check if manager has subordinates
        if ($manager->children()->exists()) {
            return back()->with('error', 'Cannot delete manager with subordinates. Please reassign or delete subordinates first.');
        }

        // Check if manager has employees
        if ($manager->employees()->exists()) {
            return back()->with('error', 'Cannot delete manager with assigned employees. Please reassign employees first.');
        }

        try {
            $managerName = $manager->name;
            $manager->delete();

            \Log::info('Manager deleted via admin interface', [
                'manager_name' => $managerName,
                'deleted_by' => auth()->guard('admin')->user()->name
            ]);

            return redirect()->route('admin.managers.index')
                           ->with('success', 'Manager deleted successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete manager: ' . $e->getMessage());
        }
    }

    public function show(Manager $manager)
    {
        $manager->load(['region', 'parent', 'managerLevel', 'children.managerLevel']);
        
        // Get hierarchical data
        $subordinates = $manager->allSubordinates();
        $employees = $manager->allEmployees();
        $agencies = $manager->allAgencies();
        $customers = $manager->allCustomers();
        
        // Get territorial boundaries
        $territorialBoundaries = $this->permissionService->getTerritorialBoundaries($manager);
        
        // Get permission matrix
        $permissionMatrix = $this->permissionService->getPermissionMatrix($manager);

        return view('admin.managers.show', compact(
            'manager', 'subordinates', 'employees', 'agencies', 'customers',
            'territorialBoundaries', 'permissionMatrix'
        ));
    }

    public function updateStatus(Request $request)
    {
        $manager = Manager::find($request->id);
        $manager->status = !$manager->status;
        $manager->save();

        \Log::info('Manager status updated', [
            'manager_id' => $manager->id,
            'new_status' => $manager->status,
            'updated_by' => auth()->guard('admin')->user()->name
        ]);

        return back()->with('success', 'Manager status updated successfully');
    }

    /**
     * Hierarchy tree visualization
     */
    public function hierarchyTree()
    {
        $hierarchyTree = $this->buildHierarchyTree();
        $stats = $this->getManagerStats();
        $managerLevels = ManagerLevel::active()->orderBy('hierarchy_level')->get();

        return view('admin.managers.hierarchy-tree', compact('hierarchyTree', 'stats', 'managerLevels'));
    }

    /**
     * Get manager level options for AJAX
     */
    public function getLevelOptions(Request $request)
    {
        $parentId = $request->parent_id;
        
        if ($parentId) {
            $parent = Manager::find($parentId);
            if ($parent && $parent->managerLevel) {
                $availableLevels = ManagerLevel::where('hierarchy_level', '>', $parent->managerLevel->hierarchy_level)
                                             ->active()
                                             ->orderBy('hierarchy_level')
                                             ->get();
                return response()->json($availableLevels);
            }
        }

        // If no parent, return all levels
        $allLevels = ManagerLevel::active()->orderBy('hierarchy_level')->get();
        return response()->json($allLevels);
    }

    /**
     * Get recommended parent for a given manager level
     */
    public function getRecommendedParent(Request $request)
    {
        $levelName = $request->level_name;
        
        if (!$levelName) {
            return response()->json(['parent_id' => null, 'message' => 'No level specified']);
        }

        $selectedLevel = ManagerLevel::where('name', $levelName)->first();
        if (!$selectedLevel) {
            return response()->json(['parent_id' => null, 'message' => 'Invalid level']);
        }

        $targetHierarchyLevel = $selectedLevel->hierarchy_level;

        // Level 1 doesn't need a parent
        if ($targetHierarchyLevel == 1) {
            return response()->json([
                'parent_id' => null, 
                'message' => 'Level 1 managers are top-level (no parent required)',
                'auto_assigned' => false
            ]);
        }

        // Find appropriate parent
        $recommendedParent = $this->findAppropriateParent($targetHierarchyLevel);
        
        if ($recommendedParent) {
            $parentLevel = $recommendedParent->managerLevel ? $recommendedParent->managerLevel->hierarchy_level : 'Unknown';
            $message = "Auto-assigned to {$recommendedParent->name} (Level {$parentLevel})";
            
            return response()->json([
                'parent_id' => $recommendedParent->id,
                'parent_name' => $recommendedParent->name,
                'parent_level' => $parentLevel,
                'message' => $message,
                'auto_assigned' => true
            ]);
        }

        return response()->json([
            'parent_id' => null,
            'message' => 'No suitable parent manager found',
            'auto_assigned' => false
        ]);
    }

    /**
     * Find appropriate parent manager based on hierarchy rules
     */
    private function findAppropriateParent(int $targetLevel): ?Manager
    {
        // First, try to find a manager from the immediate parent level (targetLevel - 1)
        $immediateParentLevel = $targetLevel - 1;
        
        $immediateParent = Manager::whereHas('managerLevel', function($query) use ($immediateParentLevel) {
            $query->where('hierarchy_level', $immediateParentLevel)
                  ->where('status', true); // Active level
        })
        ->where('status', 1) // Active manager
        ->first();

        if ($immediateParent) {
            return $immediateParent;
        }

        // If immediate parent level not available or inactive, fall back to level 1
        if ($targetLevel > 2) {
            $levelOneParent = Manager::whereHas('managerLevel', function($query) {
                $query->where('hierarchy_level', 1)
                      ->where('status', true); // Active level
            })
            ->where('status', 1) // Active manager
            ->first();

            if ($levelOneParent) {
                return $levelOneParent;
            }
        }

        // If no suitable parent found, return null
        return null;
    }

    /**
     * Bulk operations
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:activate,deactivate,delete,reassign',
            'manager_ids' => 'required|array',
            'manager_ids.*' => 'exists:managers,id',
            'new_parent_id' => 'sometimes|nullable|exists:managers,id'
        ]);

        $managers = Manager::whereIn('id', $request->manager_ids)->get();
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
                        if ($request->new_parent_id && !$manager->allSubordinates()->contains('id', $request->new_parent_id)) {
                            $manager->update(['parent_id' => $request->new_parent_id]);
                            $results[] = ['id' => $manager->id, 'status' => 'reassigned'];
                        } else {
                            $results[] = ['id' => $manager->id, 'status' => 'failed', 'reason' => 'Invalid parent assignment'];
                        }
                        break;
                    
                    case 'delete':
                        if (!$manager->children()->exists() && !$manager->employees()->exists()) {
                            $manager->delete();
                            $results[] = ['id' => $manager->id, 'status' => 'deleted'];
                        } else {
                            $results[] = ['id' => $manager->id, 'status' => 'failed', 'reason' => 'Has subordinates or employees'];
                        }
                        break;
                }
            } catch (\Exception $e) {
                $results[] = ['id' => $manager->id, 'status' => 'failed', 'reason' => $e->getMessage()];
            }
        }

        return back()->with('success', 'Bulk action completed')->with('bulk_results', $results);
    }

    /**
     * Show the hierarchy management page
     */
    public function hierarchy()
    {
        $hierarchy = $this->buildHierarchyTree();
        
        $levels = ManagerLevel::active()
                            ->withCount('managers')
                            ->orderBy('hierarchy_level')
                            ->get();
        
        $stats = [
            'total_managers' => Manager::count(),
            'active_levels' => ManagerLevel::active()->count(),
            'max_depth' => Manager::max('depth') ?? 0,
        ];
        
        return view('admin.managers.hierarchy', compact('hierarchy', 'levels', 'stats'));
    }

    /**
     * Build hierarchy tree for visualization
     */
    private function buildHierarchyTree()
    {
        return Manager::whereNull('parent_id')
                     ->with(['children' => function($query) {
                         $query->with(['children' => function($subQuery) {
                             $subQuery->with(['children' => function($subSubQuery) {
                                 $subSubQuery->with('children.managerLevel');
                             }]);
                         }]);
                     }, 'managerLevel'])
                     ->orderBy('hierarchy_path')
                     ->get();
    }

    /**
     * Get manager statistics
     */
    private function getManagerStats()
    {
        return [
            'total_managers' => Manager::count(),
            'active_managers' => Manager::where('status', 1)->count(),
            'by_level' => ManagerLevel::withCount(['managers' => function($query) {
                $query->where('status', 1);
            }])->orderBy('hierarchy_level')->get(),
            'hierarchy_depth' => Manager::max('depth') ?? 0,
            'without_parent' => Manager::whereNull('parent_id')->count(),
            'without_subordinates' => Manager::doesntHave('children')->count(),
        ];
    }
}
