<?php

namespace App\Http\Controllers\Admin;

use App\Models\Agency;
use App\Models\Customer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CustomerController extends Controller
{
    public function index()
    {
        $data = Customer::latest()->paginate(10);
        return view('admin.customers.index', compact('data'));
    }

    public function create()
    {
        $customer = new Customer();
        $agencies = Agency::where('status', 1)->get();
        return view('admin.customers.create', compact('customer', 'agencies'));
    }

    public function store(Request $request)
    {
        // Validate the request data
        $request->validate([
            'agency_id' => 'required|exists:agencies,id',
            'sponcer_code' => 'nullable|string|max:255',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'address_1' => 'required|string|max:255',
            'address_2' => 'nullable|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'pin' => 'required|string|max:20',
            'country' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'mobile' => 'nullable|string|max:20',
            'email' => 'required|email|unique:customers,email',
            'religion' => 'nullable|string|max:255',
            'dob' => 'nullable|date',
            'gender' => 'nullable|in:Male,Female,Other',
            'adhar_number' => 'nullable|string|max:20',
            'status' => 'required|in:0,1',
        ]);

        // Create a new customer
        $customer = new Customer();
        $customer->agency_id = $request->agency_id;
        $customer->sponcer_code = $request->sponcer_code;
        $customer->first_name = $request->first_name;
        $customer->last_name = $request->last_name;
        $customer->address_1 = $request->address_1;
        $customer->address_2 = $request->address_2;
        $customer->city = $request->city;
        $customer->state = $request->state;
        $customer->pin = $request->pin;
        $customer->country = $request->country;
        $customer->phone = $request->phone;
        $customer->mobile = $request->mobile;
        $customer->email = $request->email;
        $customer->religion = $request->religion;
        $customer->dob = $request->dob;
        $customer->gender = $request->gender;
        $customer->adhar_number = $request->adhar_number;
        $customer->status = $request->status;
        $customer->save();

        // Redirect with success message
        return redirect()->route('admin.customers.index')->with('success', 'Customer created successfully');
    }

    public function edit(Customer $customer)
    {
        $agencies = Agency::where('status', 1)->get();
        return view('admin.customers.edit', compact('customer', 'agencies'));
    }

    public function update(Request $request, $id)
    {
        // Find the customer by ID
        $customer = Customer::findOrFail($id);

        // Validate the request data
        $request->validate([
            'agency_id' => 'required|exists:agencies,id',
            'sponcer_code' => 'nullable|string|max:255',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'address_1' => 'required|string|max:255',
            'address_2' => 'nullable|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'pin' => 'required|string|max:20',
            'country' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'mobile' => 'nullable|string|max:20',
            'email' => 'required|email|unique:customers,email,' . $customer->id, // Ignore the current customer's email
            'religion' => 'nullable|string|max:255',
            'dob' => 'nullable|date',
            'gender' => 'nullable|in:Male,Female,Other',
            'adhar_number' => 'nullable|string|max:20',
            'status' => 'required|in:0,1',
        ]);

        // Update the customer details
        $customer->agency_id = $request->agency_id;
        $customer->sponcer_code = $request->sponcer_code;
        $customer->first_name = $request->first_name;
        $customer->last_name = $request->last_name;
        $customer->address_1 = $request->address_1;
        $customer->address_2 = $request->address_2;
        $customer->city = $request->city;
        $customer->state = $request->state;
        $customer->pin = $request->pin;
        $customer->country = $request->country;
        $customer->phone = $request->phone;
        $customer->mobile = $request->mobile;
        $customer->email = $request->email;
        $customer->religion = $request->religion;
        $customer->dob = $request->dob;
        $customer->gender = $request->gender;
        $customer->adhar_number = $request->adhar_number;
        $customer->status = $request->status;
        $customer->save();

        // Redirect with success message
        return redirect()->route('admin.customers.index')->with('success', 'Customer updated successfully');
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();

        return redirect()->route('admin.customers.index')->with('success', 'Customer deleted successfully');
    }

    public function show(Customer $customer)
    {
        return view('admin.customers.show', compact('customer'));
    }

}
