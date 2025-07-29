<?php

namespace App\Http\Controllers\Admin;

use App\Models\Employee;
use App\Models\Region;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class EmployeeController extends Controller
{
    public function index()
    {
        $data = Employee::with('region')->paginate(10);

        return view('admin.employees.index', compact('data'));
    }

    public function create()
    {
        $regions = Region::where('status', 1)->get();
        return view('admin.employees.create', compact('regions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:employees,email',
            'phone' => 'required',
            'designation' => 'required',
            'region_id' => 'required|exists:regions,id',
        ]);

        $employee = new Employee();
        $employee->name = $request->name;
        $employee->email = $request->email;
        $employee->phone = $request->phone;
        $employee->designation = $request->designation;
        $employee->region_id = $request->region_id;
        $employee->save();

        return redirect()->route('admin.employees.index')->with('success', 'Employee created successfully');
    }

    public function edit(Employee $employee)
    {
        $regions = Region::where('status', 1)->get();
        return view('admin.employees.edit', compact('employee', 'regions'));
    }

    public function update(Request $request, Employee $employee)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:employees,email,' . $employee->id,
            'phone' => 'required',
            'designation' => 'required',
            'region_id' => 'required|exists:regions,id',
        ]);

        $employee->name = $request->name;
        $employee->email = $request->email;
        $employee->phone = $request->phone;
        $employee->designation = $request->designation;
        $employee->region_id = $request->region_id;
        $employee->save();

        return redirect()->route('admin.employees.index')->with('success', 'Employee updated successfully');
    }

    public function destroy(Employee $employee)
    {
        $employee->delete();

        return redirect()->route('employees.index')->with('success', 'Employee deleted successfully');
    }

    public function updateStatus(Request $request)
    {
        $employee = Employee::find($request->id);
        $employee->status = !$employee->status;
        $employee->save();
        return back()->with('success', 'Employee status updated successfully');
    }

    public function show(Employee $employee)
    {
        return view('admin.employees.show', compact('employee'));
    }
}
