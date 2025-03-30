<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{Agency, Employee};
use App\Exports\AgencyExport;
use Maatwebsite\Excel\Facades\Excel;

class AgencyController extends Controller
{
    public function index()
    {
        $data = Agency::latest()
            ->with('employee')
            ->when(request()->q, function ($agencies) {
                $agencies = $agencies->where('name', 'like', '%' . request()->q . '%');
            })
            ->paginate(10);

        return view('admin.agencies.index', compact('data'));
    }


    public function create()
    {
        $employees = Employee::get();
        return view('admin.agencies.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:agencies',
            'phone' => 'required',
            'address' => 'required',
        ]);

        $agency = new Agency();
        $agency->name = $request->name;
        $agency->email = $request->email;
        $agency->phone = $request->phone;
        $agency->address = $request->address;
        $agency->status = 1;
        $agency->save();

        return redirect()->route('admin.agencies.index')->with('success', 'Agency created successfully');
    }

    public function edit(Agency $agency)
    {
        $employees = Employee::get();
        return view('admin.agencies.edit', compact('agency', 'employees'));
    }

    public function update(Request $request, Agency $agency)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:agencies,email,' . $agency->id,
            'phone' => 'required',
            'address' => 'required',
            'employee_id' => 'required|exists:employees,id',
        ]);

        $agency->name = $request->name;
        $agency->email = $request->email;
        $agency->phone = $request->phone;
        $agency->address = $request->address;
        $agency->employee_id = $request->employee_id;
        $agency->save();

        return redirect()->route('admin.agencies.index')->with('success', 'Agency updated successfully');
    }

    public function destroy(Agency $agency)
    {
        $agency->delete();

        return back()->with('success', 'Agency removed successfully');
    }

    public function updateStatus(Request $request)
    {
        $agency = Agency::find($request->id);
        $agency->status = !$agency->status;
        $agency->save();
        return back()->with('success', 'Agency status updated successfully');
    }

    public function show(Agency $agency)
    {
        return view('admin.agencies.show', compact('agency'));
    }

    public function export()
    {
        return Excel::download(new AgencyExport, 'agencies.xlsx');

    }
}
