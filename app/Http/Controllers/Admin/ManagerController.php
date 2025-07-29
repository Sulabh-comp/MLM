<?php

namespace App\Http\Controllers\Admin;

use App\Models\Manager;
use App\Models\Region;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ManagerController extends Controller
{
    public function index()
    {
        $data = Manager::with('region')->paginate(10);
        return view('admin.managers.index', compact('data'));
    }

    public function create()
    {
        $regions = Region::where('status', 1)->get();
        return view('admin.managers.create', compact('regions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:managers,email',
            'phone' => 'required',
            'designation' => 'required',
            'region_id' => 'required|exists:regions,id',
        ]);

        $manager = new Manager();
        $manager->name = $request->name;
        $manager->email = $request->email;
        $manager->phone = $request->phone;
        $manager->designation = $request->designation;
        $manager->region_id = $request->region_id;
        $manager->save();

        return redirect()->route('admin.managers.index')->with('success', 'Manager created successfully');
    }

    public function edit(Manager $manager)
    {
        $regions = Region::where('status', 1)->get();
        return view('admin.managers.edit', compact('manager', 'regions'));
    }

    public function update(Request $request, Manager $manager)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:managers,email,' . $manager->id,
            'phone' => 'required',
            'designation' => 'required',
            'region_id' => 'required|exists:regions,id',
        ]);

        $manager->name = $request->name;
        $manager->email = $request->email;
        $manager->phone = $request->phone;
        $manager->designation = $request->designation;
        $manager->region_id = $request->region_id;
        $manager->save();

        return redirect()->route('admin.managers.index')->with('success', 'Manager updated successfully');
    }

    public function destroy(Manager $manager)
    {
        $manager->delete();
        return redirect()->route('admin.managers.index')->with('success', 'Manager deleted successfully');
    }

    public function updateStatus(Request $request)
    {
        $manager = Manager::find($request->id);
        $manager->status = !$manager->status;
        $manager->save();

        return back()->with('success', 'Manager status updated successfully');
    }

    public function show(Manager $manager)
    {
        $employees = $manager->employees()->with('agencies.customers')->paginate(10);
        return view('admin.managers.show', compact('manager', 'employees'));
    }
}
