<?php

namespace App\Http\Controllers\Admin;

use App\Models\Region;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RegionController extends Controller
{
    public function index()
    {
        $data = Region::withCount(['managers', 'employees'])->paginate(10);
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
            'states' => 'nullable|string',
        ]);

        $region = new Region();
        $region->name = $request->name;
        $region->code = strtoupper($request->code);
        $region->description = $request->description;
        $region->states = explode(',', $request->states ?? '');
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
            'states' => 'nullable|string',
        ]);

        $region->name = $request->name;
        $region->code = strtoupper($request->code);
        $region->description = $request->description;
        $region->states = explode(',', $request->states ?? '');
        $region->save();

        return redirect()->route('admin.regions.index')->with('success', 'Region updated successfully');
    }

    public function destroy(Region $region)
    {
        // Check if region has managers or employees
        if($region->managers()->count() > 0 || $region->employees()->count() > 0) {
            return back()->with('error', 'Cannot delete region that has managers or employees assigned.');
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
        $managers = $region->managers()->paginate(10, ['*'], 'managers_page');
        $employees = $region->employees()->paginate(10, ['*'], 'employees_page');
        
        return view('admin.regions.show', compact('region', 'managers', 'employees'));
    }
}
