<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{Agency, Notification, Admin};
use Str;

class AgencyController extends Controller
{
    public function index()
    {
        $data = Agency::where('employee_id', auth()->guard('employee')->id())->paginate(10);

        return view('employee.agencies.index', compact('data'));
    }

    public function create()
    {
        return view('employee.agencies.create');
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
        $agency->employee_id = auth()->guard('employee')->id();
        $agency->status = 0;
        $agency->save();

        // generate notifications to all admins
        $admins = Admin::all();
        foreach ($admins as $admin) {
            $notification = new Notification();
            $notification->user_id = $admin->id;
            $notification->model = Admin::class;
            $notification->title = 'New Agency Created by ' . auth()->guard('employee')->user()->name;
            $notification->message = 'Agency ' . $agency->name . ' has been created by ' . auth()->guard('employee')->user()->name;
            $notification->url = route('admin.agencies.show', $agency->id);
            $notification->save();
        }

        return redirect()->route('employee.agencies.index')->with('success', 'Agency created successfully');
    }

    public function edit(Agency $agency)
    {
        return view('employee.agencies.edit', compact('agency'));
    }

    public function update(Request $request, Agency $agency)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:agencies,email,' . $agency->id,
            'phone' => 'required',
            'address' => 'required',
        ]);

        $agency->name = $request->name;
        $agency->email = $request->email;
        $agency->phone = $request->phone;
        $agency->address = $request->address;
        $agency->save();

        return redirect()->route('agencies.index')->with('success', 'Agency updated successfully');
    }

    public function destroy(Agency $agency)
    {
        $agency->delete();

        return redirect()->route('agencies.index')->with('success', 'Agency deleted successfully');
    }

    public function show(Agency $agency)
    {
        return view('employee.agencies.show', compact('agency'));
    }
}
