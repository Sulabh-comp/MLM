<?php

namespace App\Http\Controllers\Manager;

use App\Models\Employee;
use App\Models\Manager;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Traits\HierarchyAccessControl;

class EmployeeController extends Controller
{
    use HierarchyAccessControl;

    public function index()
    {
        $manager = auth()->guard('manager')->user();
        
        // Get all employees under this manager's hierarchy
        $managersInHierarchy = collect([$manager->id])->merge($manager->allSubordinates()->pluck('id'));
        
        $employees = Employee::whereIn('manager_id', $managersInHierarchy)
                            ->with(['manager'])
                            ->orderBy('name')
                            ->paginate(10);

        // Get direct employees for quick access
        $directEmployees = Employee::where('manager_id', $manager->id)
                                 ->with('agencies')
                                 ->count();

        // Get statistics
        $stats = [
            'direct_employees' => $directEmployees,
            'total_employees' => $employees->total(),
            'active_employees' => Employee::whereIn('manager_id', $managersInHierarchy)->where('status', 1)->count(),
            'inactive_employees' => Employee::whereIn('manager_id', $managersInHierarchy)->where('status', 0)->count(),
        ];

        return view('manager.employees.index', compact('employees', 'stats', 'manager'));
    }

    public function show(Employee $employee)
    {
        $manager = auth()->guard('manager')->user();
        
        // Check if employee is accessible through hierarchy
        if (!$this->isEmployeeAccessible($manager, $employee)) {
            abort(403, 'Unauthorized access to employee data.');
        }

        $employee->load(['manager', 'agencies.customers']);
        $agencies = $employee->agencies()->with('customers')->paginate(10);
        
        // Get employee statistics
        $stats = [
            'total_agencies' => $employee->agencies()->count(),
            'active_agencies' => $employee->agencies()->where('status', 1)->count(),
            'total_customers' => $employee->customers()->count(),
            'agencies_this_month' => $employee->agencies()->whereMonth('created_at', now()->month)->count(),
        ];

        return view('manager.employees.show', compact('employee', 'agencies', 'stats', 'manager'));
    }

    public function create()
    {
        $manager = auth()->guard('manager')->user();
        
        // Get available managers for assignment (current manager and subordinates)
        $availableManagers = collect([$manager])->merge($manager->allSubordinates())
                                               ->sortBy('hierarchy_path');

        return view('manager.employees.create', compact('availableManagers', 'manager'));
    }

    public function store(Request $request)
    {
        $manager = auth()->guard('manager')->user();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:employees,email',
            'phone' => 'required|string|max:20',
            'designation' => 'required|string|max:255',
            'manager_id' => 'required|exists:managers,id',
        ]);

        // Validate that the selected manager is accessible
        $selectedManager = Manager::findOrFail($request->manager_id);
        if (!$this->isManagerAccessible($manager, $selectedManager)) {
            return back()->withInput()->with('error', 'You cannot assign employees to this manager.');
        }

        $employee = Employee::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'designation' => $request->designation,
            'manager_id' => $request->manager_id,
            'status' => 1,
        ]);

        \Log::info('Employee created', [
            'employee_id' => $employee->id,
            'employee_name' => $employee->name,
            'assigned_manager' => $selectedManager->name,
            'created_by' => $manager->name
        ]);

        return redirect()->route('manager.employees.index')
                        ->with('success', 'Employee created successfully and assigned to ' . $selectedManager->name);
    }

    public function edit(Employee $employee)
    {
        $manager = auth()->guard('manager')->user();
        
        // Check if employee is accessible through hierarchy
        if (!$this->isEmployeeAccessible($manager, $employee)) {
            abort(403, 'Unauthorized access to employee data.');
        }

        // Get available managers for assignment (current manager and subordinates)
        $availableManagers = collect([$manager])->merge($manager->allSubordinates())
                                               ->sortBy('hierarchy_path');

        return view('manager.employees.edit', compact('employee', 'availableManagers', 'manager'));
    }

    public function update(Request $request, Employee $employee)
    {
        $manager = auth()->guard('manager')->user();
        
        // Check if employee is accessible through hierarchy
        if (!$this->isEmployeeAccessible($manager, $employee)) {
            abort(403, 'Unauthorized access to employee data.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:employees,email,' . $employee->id,
            'phone' => 'required|string|max:20',
            'designation' => 'required|string|max:255',
            'manager_id' => 'required|exists:managers,id',
        ]);

        // Validate that the selected manager is accessible
        $selectedManager = Manager::findOrFail($request->manager_id);
        if (!$this->isManagerAccessible($manager, $selectedManager)) {
            return back()->withInput()->with('error', 'You cannot assign employees to this manager.');
        }

        $oldManager = $employee->manager;
        $employee->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'designation' => $request->designation,
            'manager_id' => $request->manager_id,
        ]);

        \Log::info('Employee updated', [
            'employee_id' => $employee->id,
            'employee_name' => $employee->name,
            'old_manager' => $oldManager ? $oldManager->name : 'None',
            'new_manager' => $selectedManager->name,
            'updated_by' => $manager->name
        ]);

        return redirect()->route('manager.employees.index')
                        ->with('success', 'Employee updated successfully');
    }

    public function destroy(Employee $employee)
    {
        $manager = auth()->guard('manager')->user();
        
        // Check if employee is accessible through hierarchy
        if (!$this->isEmployeeAccessible($manager, $employee)) {
            abort(403, 'Unauthorized access to employee data.');
        }

        // Check if employee has agencies
        if ($employee->agencies()->exists()) {
            return back()->with('error', 'Cannot delete employee with existing agencies. Please reassign or delete agencies first.');
        }

        $employeeName = $employee->name;
        $employee->delete();

        \Log::info('Employee deleted', [
            'employee_name' => $employeeName,
            'deleted_by' => $manager->name
        ]);

        return redirect()->route('manager.employees.index')
                        ->with('success', 'Employee deleted successfully');
    }

    public function updateStatus(Request $request)
    {
        $manager = auth()->guard('manager')->user();
        $employee = Employee::findOrFail($request->id);
        
        // Check if employee is accessible through hierarchy
        if (!$this->isEmployeeAccessible($manager, $employee)) {
            abort(403, 'Unauthorized access to employee data.');
        }

        $employee->status = !$employee->status;
        $employee->save();

        \Log::info('Employee status updated', [
            'employee_id' => $employee->id,
            'employee_name' => $employee->name,
            'new_status' => $employee->status ? 'Active' : 'Inactive',
            'updated_by' => $manager->name
        ]);
        
        return back()->with('success', 'Employee status updated successfully');
    }

    /**
     * Check if an employee is accessible to the current manager
     */
    private function isEmployeeAccessible($manager, $employee)
    {
        if (!$employee->manager_id) {
            return false; // Employee not assigned to any manager
        }

        // Check if employee's manager is the current manager or a subordinate
        if ($employee->manager_id === $manager->id) {
            return true; // Direct employee
        }

        // Check if employee's manager is a subordinate
        return $manager->allSubordinates()->contains('id', $employee->manager_id);
    }

    /**
     * Check if a manager is accessible to the current manager
     */
    private function isManagerAccessible($currentManager, $targetManager)
    {
        if ($currentManager->id === $targetManager->id) {
            return true; // Can assign to self
        }

        // Check if target is a subordinate
        return $currentManager->allSubordinates()->contains('id', $targetManager->id);
    }

    /**
     * Reassign employees from one manager to another
     */
    public function reassignEmployees(Request $request)
    {
        $manager = auth()->guard('manager')->user();
        
        $request->validate([
            'from_manager_id' => 'required|exists:managers,id',
            'to_manager_id' => 'required|exists:managers,id',
            'employee_ids' => 'array',
            'employee_ids.*' => 'exists:employees,id',
        ]);

        $fromManager = Manager::findOrFail($request->from_manager_id);
        $toManager = Manager::findOrFail($request->to_manager_id);

        // Validate accessibility
        if (!$this->isManagerAccessible($manager, $fromManager) || !$this->isManagerAccessible($manager, $toManager)) {
            return back()->with('error', 'Unauthorized manager access for reassignment.');
        }

        $employeeIds = $request->employee_ids ?? [];
        if (empty($employeeIds)) {
            // Reassign all employees from the source manager
            $employeeIds = Employee::where('manager_id', $fromManager->id)->pluck('id')->toArray();
        }

        $employees = Employee::whereIn('id', $employeeIds)
                            ->where('manager_id', $fromManager->id)
                            ->get();

        foreach ($employees as $employee) {
            $employee->update(['manager_id' => $toManager->id]);
        }

        \Log::info('Employees reassigned', [
            'from_manager' => $fromManager->name,
            'to_manager' => $toManager->name,
            'employee_count' => $employees->count(),
            'reassigned_by' => $manager->name
        ]);

        return back()->with('success', "Successfully reassigned {$employees->count()} employees from {$fromManager->name} to {$toManager->name}");
    }
}
