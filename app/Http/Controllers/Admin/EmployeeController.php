<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Employee;

class EmployeeController extends Controller
{
    public function index()
    {
        $data = Employee::all();

        return view('admin.employees.index', compact('data'));
    }

    public function create()
    {
        return view('admin.employees.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:employees',
            'password' => 'required|confirmed',
        ]);

        $employee = new Employee();
        $employee->name = $request->name;
        $employee->email = $request->email;
        $employee->password = bcrypt($request->password);
        $employee->save();

        return redirect()->route('admin.employees.index')->with('success', 'Employee created successfully');
    }

    public function edit(Employee $employee)
    {
        return view('admin.employees.edit', compact('employee'));
    }

    public function update(Request $request, Employee $employee)
    {
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
