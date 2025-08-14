<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{Manager, ManagerLevel};
use App\Http\Requests\CreateManagerRequest;
use App\Traits\HierarchyAccessControl;

class ManagerController extends Controller
{
    use HierarchyAccessControl;

    public function index()
    {
        $currentManager = auth()->guard('manager')->user();
        
        // Get all subordinate managers in hierarchy (already includes relationships and ordering)
        $subordinates = $currentManager->getSubordinatesByPath();

        // Get direct subordinates for quick access
        $directSubordinates = $currentManager->children()
                                           ->with('managerLevel')
                                           ->orderBy('name')
                                           ->get();

        // Hierarchy statistics
        $stats = [
            'total_subordinates' => $subordinates->count(),
            'direct_subordinates' => $directSubordinates->count(),
            'by_level' => $subordinates->groupBy('level_name')->map->count(),
            'max_depth' => $subordinates->max('depth') ?? 0,
        ];

        return view('manager.managers.index', compact('subordinates', 'directSubordinates', 'stats', 'currentManager'));
    }

    public function create()
    {
        $currentManager = auth()->guard('manager')->user();
        
        // Get available manager levels that can be created as subordinates
        $availableLevels = $this->getSubordinateLevels($currentManager);
        
        // Get potential parent managers (current manager and their subordinates)
        $potentialParents = collect([$currentManager])->merge($currentManager->allSubordinates())
                                                       ->sortBy('hierarchy_path');

        return view('manager.managers.create', compact('availableLevels', 'potentialParents', 'currentManager'));
    }

    public function store(CreateManagerRequest $request)
    {
        try {
            $currentManager = auth()->guard('manager')->user();
            
            // Validate that the new manager can be created under current manager's hierarchy
            $this->validateSubordinateCreation($currentManager, $request);

            $manager = Manager::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'designation' => $request->designation,
                'level_name' => $request->level_name,
                'parent_id' => $request->parent_id ?? $currentManager->id,
                'password' => bcrypt($request->password ?? 'password123'),
                'status' => 1,
                'region_id' => $currentManager->region_id, // Inherit region for now
            ]);

            // Update hierarchy fields
            $manager->updateHierarchyFields();

            \Log::info('Subordinate manager created', [
                'new_manager_id' => $manager->id,
                'new_manager_name' => $manager->name,
                'created_by' => $currentManager->name,
                'parent_id' => $manager->parent_id
            ]);

            return redirect()->route('manager.managers.index')
                           ->with('success', 'Subordinate manager created successfully');
        } catch (\Exception $e) {
            return back()->withInput()
                        ->with('error', 'Failed to create manager: ' . $e->getMessage());
        }
    }

    public function show(Manager $manager)
    {
        $currentManager = auth()->guard('manager')->user();
        
        // Check if the manager is accessible (subordinate or self)
        if (!$this->isManagerAccessible($currentManager, $manager)) {
            abort(403, 'Unauthorized to view this manager');
        }

        $manager->load(['managerLevel', 'parent', 'children.managerLevel', 'employees', 'agencies']);
        
        // Get performance statistics
        $stats = [
            'direct_subordinates' => $manager->children()->count(),
            'total_subordinates' => $manager->allSubordinates()->count(),
            'employees_count' => $manager->employees()->count(),
            'agencies_count' => $manager->agencies()->count(),
            'customers_count' => $manager->customers()->count(),
            'active_employees' => $manager->employees()->where('status', 1)->count(),
            'active_agencies' => $manager->agencies()->where('status', 1)->count(),
        ];

        return view('manager.managers.show', compact('manager', 'stats', 'currentManager'));
    }

    public function edit(Manager $manager)
    {
        $currentManager = auth()->guard('manager')->user();
        
        // Check if the manager is accessible and editable
        if (!$this->isManagerAccessible($currentManager, $manager)) {
            abort(403, 'Unauthorized to edit this manager');
        }

        $availableLevels = $this->getSubordinateLevels($currentManager);
        $potentialParents = collect([$currentManager])->merge($currentManager->allSubordinates())
                                                       ->where('id', '!=', $manager->id)
                                                       ->sortBy('hierarchy_path');

        return view('manager.managers.edit', compact('manager', 'availableLevels', 'potentialParents', 'currentManager'));
    }

    public function update(CreateManagerRequest $request, Manager $manager)
    {
        try {
            $currentManager = auth()->guard('manager')->user();
            
            // Check if the manager is accessible and editable
            if (!$this->isManagerAccessible($currentManager, $manager)) {
                abort(403, 'Unauthorized to edit this manager');
            }

            $manager->update([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'designation' => $request->designation,
                'level_name' => $request->level_name,
                'parent_id' => $request->parent_id,
            ]);

            // Update hierarchy fields if parent changed
            if ($manager->wasChanged('parent_id')) {
                $manager->updateHierarchyFields();
            }

            \Log::info('Subordinate manager updated', [
                'manager_id' => $manager->id,
                'manager_name' => $manager->name,
                'updated_by' => $currentManager->name
            ]);

            return redirect()->route('manager.managers.index')
                           ->with('success', 'Manager updated successfully');
        } catch (\Exception $e) {
            return back()->withInput()
                        ->with('error', 'Failed to update manager: ' . $e->getMessage());
        }
    }

    public function destroy(Manager $manager)
    {
        $currentManager = auth()->guard('manager')->user();
        
        // Check if the manager is accessible and deletable
        if (!$this->isManagerAccessible($currentManager, $manager)) {
            abort(403, 'Unauthorized to delete this manager');
        }

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

            \Log::info('Subordinate manager deleted', [
                'manager_name' => $managerName,
                'deleted_by' => $currentManager->name
            ]);

            return redirect()->route('manager.managers.index')
                           ->with('success', 'Manager deleted successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete manager: ' . $e->getMessage());
        }
    }

    /**
     * Get hierarchy tree for current manager's subordinates
     */
    public function hierarchyTree()
    {
        $currentManager = auth()->guard('manager')->user();
        
        $tree = $this->buildSubordinateTree($currentManager);
        
        return response()->json([
            'success' => true,
            'tree' => $tree,
            'stats' => [
                'total_subordinates' => $currentManager->allSubordinates()->count(),
                'direct_subordinates' => $currentManager->children()->count(),
            ]
        ]);
    }

    /**
     * Update status of subordinate manager
     */
    public function updateStatus(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:managers,id',
        ]);

        $currentManager = auth()->guard('manager')->user();
        $manager = Manager::findOrFail($request->id);

        // Check if the manager is accessible
        if (!$this->isManagerAccessible($currentManager, $manager)) {
            return back()->with('error', 'Unauthorized to update this manager');
        }

        $manager->status = !$manager->status;
        $manager->save();

        return back()->with('success', 'Manager status updated successfully');
    }

    /**
     * Get levels that can be assigned as subordinates
     */
    private function getSubordinateLevels($manager)
    {
        $currentLevel = $manager->managerLevel;
        
        if (!$currentLevel) {
            return ManagerLevel::active()->orderBy('hierarchy_level')->get();
        }

        // Get levels with higher hierarchy_level (lower in hierarchy)
        return ManagerLevel::active()
                          ->where('hierarchy_level', '>', $currentLevel->hierarchy_level)
                          ->orderBy('hierarchy_level')
                          ->get();
    }

    /**
     * Validate that subordinate can be created under current manager
     */
    private function validateSubordinateCreation($currentManager, $request)
    {
        // Check if the level can be assigned as subordinate
        $level = ManagerLevel::where('name', $request->level_name)->first();
        $currentLevel = $currentManager->managerLevel;

        if ($currentLevel && $level && $level->hierarchy_level <= $currentLevel->hierarchy_level) {
            throw new \Exception('Cannot create a manager with equal or higher level than yourself');
        }

        // Check if parent is accessible
        if ($request->parent_id) {
            $parent = Manager::find($request->parent_id);
            if (!$this->isManagerAccessible($currentManager, $parent)) {
                throw new \Exception('Invalid parent manager selected');
            }
        }
    }

    /**
     * Check if a manager is accessible to current manager
     */
    private function isManagerAccessible($currentManager, $targetManager)
    {
        if ($currentManager->id === $targetManager->id) {
            return true; // Can access self
        }

        // Check if target is a subordinate
        return $currentManager->allSubordinates()->contains('id', $targetManager->id);
    }

    /**
     * Build hierarchy tree for subordinates
     */
    private function buildSubordinateTree($manager)
    {
        return $manager->children->map(function ($child) {
            return [
                'id' => $child->id,
                'name' => $child->name,
                'level_name' => $child->level_name,
                'email' => $child->email,
                'status' => $child->status,
                'children' => $this->buildSubordinateTree($child),
            ];
        });
    }
}
