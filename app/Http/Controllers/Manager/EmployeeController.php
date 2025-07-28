<?php

namespace App\Http\Controllers\Manager;

use App\Models\Employee;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class EmployeeController extends Controller
{
    public function index()
    {
        $manager = auth()->guard('manager')->user();
        $employees = $manager->employees()
                           ->with(['agencies', 'region'])
                           ->paginate(15);

        return view('manager.employees.index', compact('employees', 'manager'));
    }

    public function create()
    {
        $manager = auth()->guard('manager')->user();
        $region = $manager->region;
        
        return view('manager.employees.create', compact('region', 'manager'));
    }

    public function store(Request $request)
    {
        $manager = auth()->guard('manager')->user();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:employees,email',
            'phone' => 'required|string',
            'designation' => 'required|string',
        ]);

        $employee = Employee::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'designation' => $request->designation,
            'manager_id' => $manager->id,
            'region_id' => $manager->region_id,
            'status' => 1,
        ]);

        return redirect()->route('manager.employees')
                        ->with('success', 'Employee created successfully.');
    }

    public function show(Employee $employee)
    {
        $manager = auth()->guard('manager')->user();
        
        // Ensure the employee belongs to this manager
        if ($employee->manager_id !== $manager->id) {
            abort(403, 'Unauthorized access to this employee.');
        }

        $employee->load(['agencies.customers', 'region']);
        
        return view('manager.employees.show', compact('employee', 'manager'));
    }

    public function edit(Employee $employee)
    {
        $manager = auth()->guard('manager')->user();
        
        // Ensure the employee belongs to this manager
        if ($employee->manager_id !== $manager->id) {
            abort(403, 'Unauthorized access to this employee.');
        }

        return view('manager.employees.edit', compact('employee', 'manager'));
    }

    public function update(Request $request, Employee $employee)
    {
        $manager = auth()->guard('manager')->user();
        
        // Ensure the employee belongs to this manager
        if ($employee->manager_id !== $manager->id) {
            abort(403, 'Unauthorized access to this employee.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:employees,email,' . $employee->id,
            'phone' => 'required|string',
            'designation' => 'required|string',
        ]);

        $employee->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'designation' => $request->designation,
        ]);

        return redirect()->route('manager.employees')
                        ->with('success', 'Employee updated successfully.');
    }

    public function destroy(Employee $employee)
    {
        $manager = auth()->guard('manager')->user();
        
        // Ensure the employee belongs to this manager
        if ($employee->manager_id !== $manager->id) {
            abort(403, 'Unauthorized access to this employee.');
        }

        $employee->delete();

        return redirect()->route('manager.employees')
                        ->with('success', 'Employee deleted successfully.');
    }

    public function toggleStatus(Employee $employee)
    {
        $manager = auth()->guard('manager')->user();
        
        // Ensure the employee belongs to this manager
        if ($employee->manager_id !== $manager->id) {
            abort(403, 'Unauthorized access to this employee.');
        }

        $employee->update([
            'status' => $employee->status == 1 ? 0 : 1
        ]);

        $status = $employee->status == 1 ? 'activated' : 'deactivated';
        
        return back()->with('success', "Employee {$status} successfully.");
    }
}
