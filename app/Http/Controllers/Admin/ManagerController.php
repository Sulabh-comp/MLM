<?php

namespace App\Http\Controllers\Admin;

use App\Models\Manager;
use App\Models\Region;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ManagerController extends Controller
{
    public function index()
    {
        $managers = Manager::with('region')->paginate(15);
        return view('admin.managers.index', compact('managers'));
    }

    public function create()
    {
        $regions = Region::where('status', 1)->get();
        return view('admin.managers.create', compact('regions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:managers,email',
            'phone' => 'required|string',
            'region_id' => 'required|exists:regions,id',
        ]);

        Manager::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'region_id' => $request->region_id,
        ]);

        return redirect()->route('admin.managers')
                        ->with('success', 'Manager created successfully.');
    }

    public function show(Manager $manager)
    {
        $manager->load(['region', 'agencies.customers']);
        return view('admin.managers.show', compact('manager'));
    }

    public function edit(Manager $manager)
    {
        $regions = Region::where('status', 1)->get();
        return view('admin.managers.edit', compact('manager', 'regions'));
    }

    public function update(Request $request, Manager $manager)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:managers,email,' . $manager->id,
            'phone' => 'required|string',
            'region_id' => 'required|exists:regions,id',
        ]);

        $manager->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'region_id' => $request->region_id,
        ]);

        return redirect()->route('admin.managers')
                        ->with('success', 'Manager updated successfully.');
    }

    public function destroy(Manager $manager)
    {
        $manager->delete();

        return redirect()->route('admin.managers')
                        ->with('success', 'Manager deleted successfully.');
    }

    public function toggleStatus(Manager $manager)
    {
        $manager->update([
            'status' => $manager->status == 1 ? 0 : 1
        ]);

        $status = $manager->status == 1 ? 'activated' : 'deactivated';
        
        return back()->with('success', "Manager {$status} successfully.");
    }
}
