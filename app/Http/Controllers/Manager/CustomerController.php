<?php

namespace App\Http\Controllers\Manager;

use App\Models\Customer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CustomerController extends Controller
{
    public function index()
    {
        $manager = auth()->guard('manager')->user();
        
        $customers = Customer::whereHas('agency.employee', function($query) use ($manager) {
            $query->where('manager_id', $manager->id);
        })->with(['agency.employee', 'familyMembers'])->paginate(15);

        return view('manager.customers.index', compact('customers', 'manager'));
    }

    public function show(Customer $customer)
    {
        $manager = auth()->guard('manager')->user();
        
        // Ensure the customer belongs to an agency under this manager's employees
        if ($customer->agency->employee->manager_id !== $manager->id) {
            abort(403, 'Unauthorized access to this customer.');
        }

        $customer->load(['agency.employee', 'familyMembers']);
        
        return view('manager.customers.show', compact('customer', 'manager'));
    }
}
