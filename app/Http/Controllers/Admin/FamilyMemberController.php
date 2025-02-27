<?php

namespace App\Http\Controllers\Admin;

use App\Models\Customer;
use App\Models\FamilyMember;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class FamilyMemberController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $customer = Customer::find(request('customer_id'));
        if (!$customer) {
            return redirect()->route('customers.index')->with('error', 'Customer not found!');
        }

        $familyMember = new FamilyMember();
        return view('admin.customers.family_members.create', compact('customer', 'familyMember'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'name' => 'required|string|max:255',
            'position' => 'nullable|string|max:255',
            'age' => 'nullable|integer',
            'gender' => 'nullable|string|max:50',
            'occupation' => 'nullable|string|max:255',
            'contact_number' => 'nullable|string|max:20',
            'monthly_income' => 'nullable|numeric',
            'health_status' => 'nullable|boolean',
            'disease_name' => 'nullable|string|max:255',
            'medicine_expenses' => 'nullable|numeric',
            'medicine_name' => 'nullable|string|max:255',
            'doctor_name' => 'nullable|string|max:255',
            'skill_knowledge' => 'nullable|boolean',
            'skill_name' => 'nullable|string|max:255',
            'institute_certified' => 'nullable|string|max:255',
            'year_of_passing' => 'nullable|integer',
            'degree_course' => 'nullable|string|max:255',
            'professional_courses' => 'nullable|string|max:255',
            'course_name' => 'nullable|string|max:255',
            'institute_name' => 'nullable|string|max:255',
            'work_city' => 'nullable|string|max:255',
            'looking_for_opportunity' => 'nullable|boolean',
            'mlm' => 'nullable|boolean',
            'sales_marketing' => 'nullable|boolean',
            'partner_commission_work' => 'nullable|boolean',
            'manufacturing_work' => 'nullable|boolean',
            'commission_work' => 'nullable|boolean',
        ]);

        $validated = $request->all();

        unset($validated['_token'], $validated['_method']);
        FamilyMember::create($validated);

        return redirect()->route('admin.family-members.index')->with('success', 'Family member created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $familyMember = FamilyMember::with('customer')->findOrFail($id); // Fetch family member with customer details
        return view('admin.customers.family_members.show', compact('familyMember'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $familyMember = FamilyMember::findOrFail($id);
        $customers = Customer::where('status', 1);
        return view('admin.customers.family_members.edit', compact('familyMember', 'customers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'name' => 'required|string|max:255',
            'position' => 'nullable|string|max:255',
            'age' => 'nullable|integer',
            'gender' => 'nullable|string|max:50',
            'occupation' => 'nullable|string|max:255',
            'contact_number' => 'nullable|string|max:20',
            'monthly_income' => 'nullable|numeric',
            'health_status' => 'nullable|boolean',
            'disease_name' => 'nullable|string|max:255',
            'medicine_expenses' => 'nullable|numeric',
            'medicine_name' => 'nullable|string|max:255',
            'doctor_name' => 'nullable|string|max:255',
            'skill_knowledge' => 'nullable|boolean',
            'skill_name' => 'nullable|string|max:255',
            'institute_certified' => 'nullable|string|max:255',
            'year_of_passing' => 'nullable|integer',
            'degree_course' => 'nullable|string|max:255',
            'professional_courses' => 'nullable|string|max:255',
            'course_name' => 'nullable|string|max:255',
            'institute_name' => 'nullable|string|max:255',
            'work_city' => 'nullable|string|max:255',
            'looking_for_opportunity' => 'nullable|boolean',
            'mlm' => 'nullable|boolean',
            'sales_marketing' => 'nullable|boolean',
            'partner_commission_work' => 'nullable|boolean',
            'manufacturing_work' => 'nullable|boolean',
            'commission_work' => 'nullable|boolean',
        ]);

        $validated = $request->all();

        unset($validated['_token'], $validated['_method']);

        $familyMember = FamilyMember::findOrFail($id);
        $familyMember->update($validated);

        return redirect()->route('admin.family-members.show', $familyMember->customer_id)->with('success', 'Family member updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $familyMember = FamilyMember::findOrFail($id);
        $customer_id = $familyMember->customer_id;
        $familyMember->delete();

        return redirect()->route('admin.customers.show', $customer_id)->with('success', 'Family member deleted successfully.');
    }
    /**
     * Update the status of the specified resource.
     */
    public function updateStatus(Request $request)
    {
        $familyMember = FamilyMember::findOrFail($request->id);
        $familyMember->status = !$familyMember->status; // Toggle status
        $familyMember->save();

        return response()->json(['success' => true, 'message' => 'Status updated successfully.']);
    }
}
