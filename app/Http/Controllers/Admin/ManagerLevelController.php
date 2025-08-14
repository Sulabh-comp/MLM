<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ManagerLevel;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ManagerLevelController extends Controller
{
    /**
     * Display a listing of manager levels.
     */
    public function index()
    {
        $levels = ManagerLevel::orderBy('hierarchy_level')->paginate(10);
        return view('admin.manager-levels.index', compact('levels'));
    }

    /**
     * Show the form for creating a new manager level.
     */
    public function create()
    {
        return view('admin.manager-levels.create');
    }

    /**
     * Store a newly created manager level in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:manager_levels,name',
            'code' => 'required|string|max:10|unique:manager_levels,code',
            'description' => 'nullable|string|max:500',
            'hierarchy_level' => 'required|integer|min:1|unique:manager_levels,hierarchy_level',
            'permissions' => 'nullable|array',
        ]);

        ManagerLevel::create([
            'name' => $request->name,
            'code' => strtoupper($request->code),
            'description' => $request->description,
            'hierarchy_level' => $request->hierarchy_level,
            'is_predefined' => false,
            'status' => $request->has('status'),
            'permissions' => $request->permissions,
        ]);

        return redirect()->route('admin.manager-levels.index')
            ->with('success', 'Manager level created successfully.');
    }

    /**
     * Display the specified manager level.
     */
    public function show(ManagerLevel $managerLevel)
    {
        $managerLevel->load('managers');
        return view('admin.manager-levels.show', compact('managerLevel'));
    }

    /**
     * Show the form for editing the specified manager level.
     */
    public function edit(ManagerLevel $managerLevel)
    {
        return view('admin.manager-levels.edit', compact('managerLevel'));
    }

    /**
     * Update the specified manager level in storage.
     */
    public function update(Request $request, ManagerLevel $managerLevel)
    {
        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('manager_levels', 'name')->ignore($managerLevel->id),
            ],
            'code' => [
                'required',
                'string',
                'max:10',
                Rule::unique('manager_levels', 'code')->ignore($managerLevel->id),
            ],
            'description' => 'nullable|string|max:500',
            'hierarchy_level' => [
                'required',
                'integer',
                'min:1',
                Rule::unique('manager_levels', 'hierarchy_level')->ignore($managerLevel->id),
            ],
            'status' => 'boolean',
            'permissions' => 'nullable|array',
        ]);

        $managerLevel->update([
            'name' => $request->name,
            'code' => strtoupper($request->code),
            'description' => $request->description,
            'hierarchy_level' => $request->hierarchy_level,
            'status' => $request->has('status'),
            'permissions' => $request->permissions,
        ]);

        return redirect()->route('admin.manager-levels.index')
            ->with('success', 'Manager level updated successfully.');
    }

    /**
     * Remove the specified manager level from storage.
     */
    public function destroy(ManagerLevel $managerLevel)
    {
        // Prevent deletion of predefined levels
        if ($managerLevel->is_predefined) {
            return redirect()->route('admin.manager-levels.index')
                ->with('error', 'Cannot delete predefined manager levels.');
        }

        // Check if any managers are using this level
        if ($managerLevel->managers()->count() > 0) {
            return redirect()->route('admin.manager-levels.index')
                ->with('error', 'Cannot delete manager level that is currently in use.');
        }

        $managerLevel->delete();

        return redirect()->route('admin.manager-levels.index')
            ->with('success', 'Manager level deleted successfully.');
    }

    /**
     * Toggle the status of a manager level
     */
    public function toggleStatus(ManagerLevel $managerLevel)
    {
        $managerLevel->update(['status' => !$managerLevel->status]);

        $status = $managerLevel->status ? 'activated' : 'deactivated';
        return redirect()->route('admin.manager-levels.index')
            ->with('success', "Manager level {$status} successfully.");
    }
}
