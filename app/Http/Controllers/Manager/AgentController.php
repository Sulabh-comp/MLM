<?php

namespace App\Http\Controllers\Manager;

use App\Models\Agency;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AgentController extends Controller
{
    public function index()
    {
        $manager = auth()->guard('manager')->user();
        $agencies = $manager->agencies()
                           ->with(['employee', 'region', 'customers'])
                           ->paginate(15);

        return view('manager.agencies.index', compact('agencies', 'manager'));
    }

    public function show(Agency $agency)
    {
        $manager = auth()->guard('manager')->user();
        
        // Ensure the agency belongs to an employee under this manager
        if ($agency->employee->manager_id !== $manager->id) {
            abort(403, 'Unauthorized access to this agency.');
        }

        $agency->load(['customers', 'employee', 'region']);
        
        return view('manager.agencies.show', compact('agency', 'manager'));
    }
}
