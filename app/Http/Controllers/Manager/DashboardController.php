<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{Agency, Customer, FamilyMember, Employee, Manager};

class DashboardController extends Controller
{

    public function index()
    {
        $manager = auth()->guard('manager')->user();
        
        // Get all subordinate managers under this manager's hierarchy
        $subordinateManagers = $manager->allSubordinates();
        $subordinateManagerIds = $subordinateManagers->pluck('id')->toArray();
        $allManagerIds = array_merge([$manager->id], $subordinateManagerIds);
        
        $today = now()->toDateString();
        $startOfMonth = now()->startOfMonth()->toDateString();
        $startOfYear = now()->startOfYear()->toDateString();

        // Hierarchy-based statistics
        $stats = [
            // Hierarchy Information
            'hierarchy_info' => [
                'current_level' => $manager->level_name ?? 'Not Set',
                'hierarchy_depth' => $manager->depth ?? 0,
                'direct_subordinates' => $manager->children()->count(),
                'total_subordinates' => count($subordinateManagerIds),
                'parent_manager' => $manager->parent ? $manager->parent->name : 'Top Level',
            ],
            
            // Territory management - All entities under this manager's hierarchy
            'total_employees' => Employee::whereIn('manager_id', $allManagerIds)->count(),
            'total_agencies' => Agency::whereHas('employee', function($query) use ($allManagerIds) {
                $query->whereIn('manager_id', $allManagerIds);
            })->count(),
            'total_customers' => Customer::whereHas('agency.employee', function($query) use ($allManagerIds) {
                $query->whereIn('manager_id', $allManagerIds);
            })->count(),
            'total_family_members' => FamilyMember::whereHas('customer.agency.employee', function($query) use ($allManagerIds) {
                $query->whereIn('manager_id', $allManagerIds);
            })->count(),

            'active_counts' => [
                'employees' => Employee::whereIn('manager_id', $allManagerIds)->where('status', 1)->count(),
                'agencies' => Agency::whereHas('employee', function($query) use ($allManagerIds) {
                    $query->whereIn('manager_id', $allManagerIds);
                })->where('status', 1)->count(),
                'customers' => Customer::whereHas('agency.employee', function($query) use ($allManagerIds) {
                    $query->whereIn('manager_id', $allManagerIds);
                })->where('status', 1)->count(),
            ],

            'inactive_counts' => [
                'employees' => Employee::whereIn('manager_id', $allManagerIds)->where('status', 0)->count(),
                'agencies' => Agency::whereHas('employee', function($query) use ($allManagerIds) {
                    $query->whereIn('manager_id', $allManagerIds);
                })->where('status', 0)->count(),
                'customers' => Customer::whereHas('agency.employee', function($query) use ($allManagerIds) {
                    $query->whereIn('manager_id', $allManagerIds);
                })->where('status', 0)->count(),
            ],

            // Recent data from hierarchy
            'recent_agencies' => Agency::whereHas('employee', function($query) use ($allManagerIds) {
                $query->whereIn('manager_id', $allManagerIds);
            })->with('employee')->latest()->take(5)->get(),

            'recent_customers' => Customer::whereHas('agency.employee', function($query) use ($allManagerIds) {
                $query->whereIn('manager_id', $allManagerIds);
            })->with('agency.employee')->latest()->take(5)->get(),

            // Top performers in hierarchy
            'top_agencies' => Agency::withCount('customers')->orderByDesc('customers_count')
                ->whereHas('employee', function($query) use ($allManagerIds) {
                    $query->whereIn('manager_id', $allManagerIds);
                })->take(5)->get(),

            'top_customers' => Customer::withCount('familyMembers')->orderByDesc('family_members_count')
                ->whereHas('agency.employee', function($query) use ($allManagerIds) {
                    $query->whereIn('manager_id', $allManagerIds);
                })->take(5)->get(),

            // Trends in hierarchy
            'employee_creation_trend' => Employee::selectRaw('DATE(created_at) as date, COUNT(id) as count')
                ->whereBetween('created_at', [now()->subDays(7), now()])
                ->whereIn('manager_id', $allManagerIds)
                ->groupBy('date')
                ->orderBy('date')
                ->get(),

            'agency_creation_trend' => Agency::selectRaw('DATE(created_at) as date, COUNT(id) as count')
                ->whereBetween('created_at', [now()->subDays(7), now()])
                ->whereHas('employee', function($query) use ($allManagerIds) {
                    $query->whereIn('manager_id', $allManagerIds);
                })
                ->groupBy('date')
                ->orderBy('date')
                ->get(),

            // Subordinate manager performance
            'subordinate_performance' => $subordinateManagers->map(function($subordinate) {
                return [
                    'manager' => $subordinate,
                    'employees_count' => $subordinate->employees()->count(),
                    'agencies_count' => $subordinate->agencies()->count(),
                    'customers_count' => $subordinate->customers()->count(),
                    'active_employees' => $subordinate->employees()->where('status', 1)->count(),
                ];
            }),
        ];

        return view('manager.dashboard.index', compact('stats', 'manager'));
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|confirmed',
        ]);

        $user = auth()->guard('manager')->user();

        if (!password_verify($request->current_password, $user->password)) {
            return back()->with('error', 'Invalid current password');
        }

        $user->password = bcrypt($request->password);
        $user->save();

        return back()->with('success', 'Password changed successfully');
    }
}
