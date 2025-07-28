<?php

namespace App\Http\Controllers\Admin;

use App\Models\Region;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RegionController extends Controller
{
    public function index()
    {
        $regions = Region::withCount(['managers', 'agencies'])->paginate(15);
        return view('admin.regions.index', compact('regions'));
    }

    public function create()
    {
        return view('admin.regions.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:regions,code',
            'description' => 'nullable|string',
        ]);

        Region::create([
            'name' => $request->name,
            'code' => $request->code,
            'description' => $request->description,
            'status' => 1,
        ]);

        return redirect()->route('admin.regions')
                        ->with('success', 'Region created successfully.');
    }

    public function show(Region $region)
    {
        $region->load(['managers.agencies', 'agencies.customers']);
        return view('admin.regions.show', compact('region'));
    }

    public function edit(Region $region)
    {
        return view('admin.regions.edit', compact('region'));
    }

    public function update(Request $request, Region $region)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:regions,code,' . $region->id,
            'description' => 'nullable|string',
        ]);

        $region->update([
            'name' => $request->name,
            'code' => $request->code,
            'description' => $request->description,
        ]);

        return redirect()->route('admin.regions')
                        ->with('success', 'Region updated successfully.');
    }

    public function destroy(Region $region)
    {
        // Check if region has managers or agencies
        if ($region->managers()->count() > 0 || $region->agencies()->count() > 0) {
            return back()->with('error', 'Cannot delete region with associated managers or agencies.');
        }

        $region->delete();

        return redirect()->route('admin.regions')
                        ->with('success', 'Region deleted successfully.');
    }

    public function toggleStatus(Region $region)
    {
        $region->update([
            'status' => $region->status == 1 ? 0 : 1
        ]);

        $status = $region->status == 1 ? 'activated' : 'deactivated';
        
        return back()->with('success', "Region {$status} successfully.");
    }
}
