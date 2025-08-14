<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{Agency, Employee};

class AgencyController extends Controller
{
    public function index()
    {
        $manager = auth()->guard('manager')->user();
        
        // Get agencies through manager's territorial employees (including subordinates)
        $agencies = Agency::whereHas('employee', function($query) use ($manager) {
                    $query->where('manager_id', $manager->id)
                          ->orWhereIn('manager_id', $manager->getSubordinatesByPath()->pluck('id'));
                })
                ->with('employee')
                ->withCount('customers')
                ->when(request()->q, function ($agencies) {
                    $agencies = $agencies->where('name', 'like', '%' . request()->q . '%');
                })
                ->paginate(10);

        return view('manager.agencies.index', compact('agencies'));
    }

    public function show(Agency $agency)
    {
        $manager = auth()->guard('manager')->user();
        
        // Check if agency belongs to manager's territory (direct or through subordinates)
        if (!$manager->canAccessAgency($agency)) {
            abort(403, 'Unauthorized access to agency data.');
        }

        return view('manager.agencies.show', compact('agency'));
    }

    public function create()
    {
        $manager = auth()->guard('manager')->user();
        
        // Get employees in manager's territory (direct reports and subordinates' employees)
        $employees = Employee::where('manager_id', $manager->id)
                            ->orWhereIn('manager_id', $manager->getSubordinatesByPath()->pluck('id'))
                            ->where('status', 1)
                            ->get();
                            
        return view('manager.agencies.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $manager = auth()->guard('manager')->user();
        
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:agencies',
            'phone' => 'required',
            'address' => 'required',
            'employee_id' => 'required|exists:employees,id',
        ]);

        // Verify employee belongs to manager's territory
        $employee = Employee::find($request->employee_id);
        if (!$manager->canAccessEmployee($employee)) {
            return back()->with('error', 'You can only assign agencies to employees in your territory.');
        }

        $agency = new Agency();
        $agency->name = $request->name;
        $agency->email = $request->email;
        $agency->phone = $request->phone;
        $agency->address = $request->address;
        $agency->employee_id = $request->employee_id;
        $agency->status = 1;
        $agency->save();

        return redirect()->route('manager.agencies.index')->with('success', 'Agency created successfully');
    }

    public function edit(Agency $agency)
    {
        $manager = auth()->guard('manager')->user();
        
        // Check if agency belongs to manager's territory
        if (!$manager->canAccessAgency($agency)) {
            abort(403, 'Unauthorized access to agency data.');
        }

        $employees = Employee::where('manager_id', $manager->id)
                            ->orWhereIn('manager_id', $manager->getSubordinatesByPath()->pluck('id'))
                            ->where('status', 1)
                            ->get();
                            
        return view('manager.agencies.edit', compact('agency', 'employees'));
    }

    public function update(Request $request, Agency $agency)
    {
        $manager = auth()->guard('manager')->user();
        
        // Check if agency belongs to manager's territory
        if (!$manager->canAccessAgency($agency)) {
            abort(403, 'Unauthorized access to agency data.');
        }

        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:agencies,email,' . $agency->id,
            'phone' => 'required',
            'address' => 'required',
            'employee_id' => 'required|exists:employees,id',
        ]);

        // Verify employee belongs to manager's territory
        $employee = Employee::find($request->employee_id);
        if (!$manager->canAccessEmployee($employee)) {
            return back()->with('error', 'You can only assign agencies to employees in your territory.');
        }

        $agency->name = $request->name;
        $agency->email = $request->email;
        $agency->phone = $request->phone;
        $agency->address = $request->address;
        $agency->employee_id = $request->employee_id;
        $agency->save();

        return redirect()->route('manager.agencies.index')->with('success', 'Agency updated successfully');
    }

    public function destroy(Agency $agency)
    {
        $manager = auth()->guard('manager')->user();
        
        // Check if agency belongs to manager's territory
        if (!$manager->canAccessAgency($agency)) {
            abort(403, 'Unauthorized access to agency data.');
        }

        $agency->delete();
        return back()->with('success', 'Agency deleted successfully');
    }

    public function updateStatus(Request $request)
    {
        $manager = auth()->guard('manager')->user();
        $agency = Agency::find($request->id);
        
        // Check if agency belongs to manager's territory
        if (!$manager->canAccessAgency($agency)) {
            abort(403, 'Unauthorized access to agency data.');
        }

        $agency->status = !$agency->status;
        $agency->save();
        
        return back()->with('success', 'Agency status updated successfully');
    }
}
