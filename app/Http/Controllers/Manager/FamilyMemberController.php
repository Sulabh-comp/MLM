<?php

namespace App\Http\Controllers\Manager;

use App\Models\FamilyMember;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class FamilyMemberController extends Controller
{
    public function index()
    {
        $manager = auth()->guard('manager')->user();
        
        $familyMembers = FamilyMember::whereHas('customer.agency.employee', function($query) use ($manager) {
            $query->where('manager_id', $manager->id);
        })->with(['customer.agency.employee'])->paginate(15);

        return view('manager.family-members.index', compact('familyMembers', 'manager'));
    }

    public function show(FamilyMember $familyMember)
    {
        $manager = auth()->guard('manager')->user();
        
        // Ensure the family member belongs to a customer under this manager's employees
        if ($familyMember->customer->agency->employee->manager_id !== $manager->id) {
            abort(403, 'Unauthorized access to this family member.');
        }

        $familyMember->load(['customer.agency.employee']);
        
        return view('manager.family-members.show', compact('familyMember', 'manager'));
    }
}
