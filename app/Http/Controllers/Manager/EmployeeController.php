<?php

namespace App\Http\Controllers\Manager;

use App\Models\Employee;
use App\Models\Region;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class EmployeeController extends Controller
{
    public function index()
    {
        $manager = auth()->guard('manager')->user();
        $employees = Employee::where('region_id', $manager->region_id)
                       ->with('region')
                       ->paginate(10);

        return view('manager.employees.index', compact('employees'));
    }

    public function show(Employee $employee)
    {
        // Check if employee belongs to manager's region
        $manager = auth()->guard('manager')->user();
        if($employee->region_id !== $manager->region_id) {
            abort(403, 'Unauthorized access to employee data.');
        }

        $agencies = $employee->agencies()->with('customers')->paginate(10);
        return view('manager.employees.show', compact('employee', 'agencies'));
    }

    public function create()
    {
        $manager = auth()->guard('manager')->user();
        $regions = Region::where('id', $manager->region_id)->get(); // Only manager's region
        return view('manager.employees.create', compact('regions'));
    }

    public function store(Request $request)
    {
        $manager = auth()->guard('manager')->user();
        
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:employees,email',
            'phone' => 'required',
            'designation' => 'required',
        ]);

        $employee = new Employee();
        $employee->name = $request->name;
        $employee->email = $request->email;
        $employee->phone = $request->phone;
        $employee->designation = $request->designation;
        $employee->region_id = $manager->region_id; // Assign to manager's region
        $employee->save();

        return redirect()->route('manager.employees.index')->with('success', 'Employee created successfully');
    }

    public function edit(Employee $employee)
    {
        // Check if employee belongs to manager's region
        $manager = auth()->guard('manager')->user();
        if($employee->region_id !== $manager->region_id) {
            abort(403, 'Unauthorized access to employee data.');
        }

        $regions = Region::where('id', $manager->region_id)->get();
        return view('manager.employees.edit', compact('employee', 'regions'));
    }

    public function update(Request $request, Employee $employee)
    {
        // Check if employee belongs to manager's region
        $manager = auth()->guard('manager')->user();
        if($employee->region_id !== $manager->region_id) {
            abort(403, 'Unauthorized access to employee data.');
        }

        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:employees,email,' . $employee->id,
            'phone' => 'required',
            'designation' => 'required',
        ]);

        $employee->name = $request->name;
        $employee->email = $request->email;
        $employee->phone = $request->phone;
        $employee->designation = $request->designation;
        // Keep employee in the same region
        $employee->save();

        return redirect()->route('manager.employees.index')->with('success', 'Employee updated successfully');
    }

    public function destroy(Employee $employee)
    {
        // Check if employee belongs to manager's region
        $manager = auth()->guard('manager')->user();
        if($employee->region_id !== $manager->region_id) {
            abort(403, 'Unauthorized access to employee data.');
        }

        $employee->delete();
        return redirect()->route('manager.employees.index')->with('success', 'Employee deleted successfully');
    }

    public function updateStatus(Request $request)
    {
        $manager = auth()->guard('manager')->user();
        $employee = Employee::find($request->id);
        
        // Check if employee belongs to manager's region
        if($employee->region_id !== $manager->region_id) {
            abort(403, 'Unauthorized access to employee data.');
        }

        $employee->status = !$employee->status;
        $employee->save();
        
        return back()->with('success', 'Employee status updated successfully');
    }
}
