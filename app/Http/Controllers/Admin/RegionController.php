<?php

namespace App\Http\Controllers\Admin;

use App\Models\Region;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RegionController extends Controller
{
    public function index()
    {
        $data = Region::withCount('managers')->get()->map(function($region) {
            // Count employees through managers in this region
            $employeesCount = \App\Models\Employee::whereHas('manager', function($query) use ($region) {
                $query->where('region_id', $region->id);
            })->count();
            
            $region->employees_count = $employeesCount;
            return $region;
        });
        
        // Convert back to paginated collection
        $perPage = 10;
        $currentPage = request()->get('page', 1);
        $data = new \Illuminate\Pagination\LengthAwarePaginator(
            $data->forPage($currentPage, $perPage),
            $data->count(),
            $perPage,
            $currentPage,
            ['path' => request()->url()]
        );
        
        return view('admin.regions.index', compact('data'));
    }

    public function create()
    {
        return view('admin.regions.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:regions,name',
            'code' => 'required|unique:regions,code|max:10',
            'description' => 'nullable',
        ]);

        $region = new Region();
        $region->name = $request->name;
        $region->code = strtoupper($request->code);
        $region->description = $request->description;
        $region->save();

        return redirect()->route('admin.regions.index')->with('success', 'Region created successfully');
    }

    public function edit(Region $region)
    {
        return view('admin.regions.edit', compact('region'));
    }

    public function update(Request $request, Region $region)
    {
        $request->validate([
            'name' => 'required|unique:regions,name,' . $region->id,
            'code' => 'required|unique:regions,code,' . $region->id . '|max:10',
            'description' => 'nullable',
        ]);

        $region->name = $request->name;
        $region->code = strtoupper($request->code);
        $region->description = $request->description;
        $region->save();

        return redirect()->route('admin.regions.index')->with('success', 'Region updated successfully');
    }

    public function destroy(Region $region)
    {
        // Check if region has managers (employees are now managed through managers)
        if($region->managers()->count() > 0) {
            return back()->with('error', 'Cannot delete region that has managers assigned. Please reassign or remove managers first.');
        }

        $region->delete();
        return redirect()->route('admin.regions.index')->with('success', 'Region deleted successfully');
    }

    public function updateStatus(Request $request)
    {
        $region = Region::find($request->id);
        $region->status = !$region->status;
        $region->save();

        return back()->with('success', 'Region status updated successfully');
    }

    public function show(Region $region)
    {
        $managers = $region->managers()->with(['managerLevel', 'directEmployees'])->paginate(10, ['*'], 'managers_page');
        
        // Get all employees in this region through managers
        $employees = collect();
        foreach($region->managers as $manager) {
            $managerEmployees = $manager->allSubordinateEmployees()->with('manager')->get();
            $employees = $employees->merge($managerEmployees);
        }
        
        // Paginate the employees collection manually
        $perPage = 10;
        $currentPage = request()->get('employees_page', 1);
        $employeesSlice = $employees->slice(($currentPage - 1) * $perPage, $perPage);
        $employeesPaginated = new \Illuminate\Pagination\LengthAwarePaginator(
            $employeesSlice,
            $employees->count(),
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'pageName' => 'employees_page']
        );
        
        return view('admin.regions.show', compact('region', 'managers', 'employeesPaginated'));
    }
}
