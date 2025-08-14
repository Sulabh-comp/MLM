<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{Customer, Agency};

class CustomerController extends Controller
{
    public function index()
    {
        $manager = auth()->guard('manager')->user();
        
        // Get customers through manager's territorial employees (including subordinates)
        $customers = Customer::whereHas('agency.employee', function($query) use ($manager) {
                    $query->where('manager_id', $manager->id)
                          ->orWhereIn('manager_id', $manager->getSubordinatesByPath()->pluck('id'));
                })
                ->with(['agency', 'familyMembers'])
                ->withCount('familyMembers')
                ->when(request()->status, function($query) {
                    if(request()->status === 'active') {
                        $query->where('status', 1);
                    } elseif(request()->status === 'inactive') {
                        $query->where('status', 0);
                    }
                })
                ->latest()
                ->paginate(10);

        return view('manager.customers.index', compact('customers'));
    }

    public function show(Customer $customer)
    {
        $manager = auth()->guard('manager')->user();
        
        // Check if customer belongs to manager's territory
        if (!$manager->canAccessCustomer($customer)) {
            abort(403, 'Unauthorized access to customer data.');
        }

        return view('manager.customers.show', compact('customer'));
    }

    public function create()
    {
        $manager = auth()->guard('manager')->user();
        $customer = new Customer();
        
        // Get agencies in manager's territory (through territorial employees)
        $agencies = Agency::whereHas('employee', function($query) use ($manager) {
                        $query->where('manager_id', $manager->id)
                              ->orWhereIn('manager_id', $manager->getSubordinatesByPath()->pluck('id'));
                    })
                    ->where('status', 1)
                    ->get();
                    
        return view('manager.customers.create', compact('customer', 'agencies'));
    }

    public function store(Request $request)
    {
        $manager = auth()->guard('manager')->user();
        
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

        // Verify agency belongs to manager's territory
        $agency = Agency::find($request->agency_id);
        if (!$manager->canAccessAgency($agency)) {
            return back()->with('error', 'You can only create customers for agencies in your territory.');
        }

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

        return redirect()->route('manager.customers.index')->with('success', 'Customer created successfully.');
    }

    public function edit(Customer $customer)
    {
        $manager = auth()->guard('manager')->user();
        
        // Check if customer belongs to manager's territory
        if (!$manager->canAccessCustomer($customer)) {
            abort(403, 'Unauthorized access to customer data.');
        }

        // Get agencies in manager's territory
        $agencies = Agency::whereHas('employee', function($query) use ($manager) {
                        $query->where('manager_id', $manager->id)
                              ->orWhereIn('manager_id', $manager->getSubordinatesByPath()->pluck('id'));
                    })
                    ->where('status', 1)
                    ->get();
                    
        return view('manager.customers.edit', compact('customer', 'agencies'));
    }

    public function update(Request $request, Customer $customer)
    {
        $manager = auth()->guard('manager')->user();
        
        // Check if customer belongs to manager's territory
        if (!$manager->canAccessCustomer($customer)) {
            abort(403, 'Unauthorized access to customer data.');
        }

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
            'email' => 'required|email|unique:customers,email,' . $customer->id,
            'religion' => 'nullable|string|max:255',
            'dob' => 'nullable|date',
            'gender' => 'nullable|in:Male,Female,Other',
            'adhar_number' => 'nullable|string|max:20',
            'status' => 'required|in:0,1',
        ]);

        // Verify agency belongs to manager's territory
        $agency = Agency::find($request->agency_id);
        if (!$manager->canAccessAgency($agency)) {
            return back()->with('error', 'You can only assign customers to agencies in your territory.');
        }

        // Update the customer
        $customer->update($request->all());

        return redirect()->route('manager.customers.index')->with('success', 'Customer updated successfully.');
    }

    public function destroy(Customer $customer)
    {
        $manager = auth()->guard('manager')->user();
        
        // Check if customer belongs to manager's territory
        if (!$manager->canAccessCustomer($customer)) {
            abort(403, 'Unauthorized access to customer data.');
        }

        $customer->delete();

        return redirect()->route('manager.customers.index')->with('success', 'Customer deleted successfully.');
    }

    public function updateStatus(Request $request)
    {
        $manager = auth()->guard('manager')->user();
        $customer = Customer::findOrFail($request->id);
        
        // Check if customer belongs to manager's territory
        if (!$manager->canAccessCustomer($customer)) {
            abort(403, 'Unauthorized access to customer data.');
        }

        $customer->status = !$customer->status;
        $customer->save();

        return back()->with('success', 'Customer status updated successfully.');
    }
}
