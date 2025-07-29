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
        $regionId = $manager->region_id;
        
        $today = now()->toDateString();
        $startOfMonth = now()->startOfMonth()->toDateString();
        $startOfYear = now()->startOfYear()->toDateString();

        $stats = [
            'total_employees' => Employee::where('region_id', $regionId)->count(),
            'total_agencies' => Agency::whereHas('employee', function($query) use ($regionId) {
                $query->where('region_id', $regionId);
            })->count(),
            'total_customers' => Customer::whereHas('agency.employee', function($query) use ($regionId) {
                $query->where('region_id', $regionId);
            })->count(),
            'total_family_members' => FamilyMember::whereHas('customer.agency.employee', function($query) use ($regionId) {
                $query->where('region_id', $regionId);
            })->count(),

            'active_counts' => [
                'employees' => Employee::where('region_id', $regionId)->where('status', 1)->count(),
                'agencies' => Agency::whereHas('employee', function($query) use ($regionId) {
                    $query->where('region_id', $regionId);
                })->where('status', 1)->count(),
                'customers' => Customer::whereHas('agency.employee', function($query) use ($regionId) {
                    $query->where('region_id', $regionId);
                })->where('status', 1)->count(),
            ],

            'inactive_counts' => [
                'employees' => Employee::where('region_id', $regionId)->where('status', 0)->count(),
                'agencies' => Agency::whereHas('employee', function($query) use ($regionId) {
                    $query->where('region_id', $regionId);
                })->where('status', 0)->count(),
                'customers' => Customer::whereHas('agency.employee', function($query) use ($regionId) {
                    $query->where('region_id', $regionId);
                })->where('status', 0)->count(),
            ],

            'recent_agencies' => Agency::whereHas('employee', function($query) use ($regionId) {
                $query->where('region_id', $regionId);
            })->latest()->take(5)->get(),

            'recent_customers' => Customer::whereHas('agency.employee', function($query) use ($regionId) {
                $query->where('region_id', $regionId);
            })->latest()->take(5)->get(),

            'top_agencies' => Agency::withCount('customers')->orderByDesc('customers_count')
                ->whereHas('employee', function($query) use ($regionId) {
                    $query->where('region_id', $regionId);
                })->take(5)->get(),

            'top_customers' => Customer::withCount('familyMembers')->orderByDesc('family_members_count')
                ->whereHas('agency.employee', function($query) use ($regionId) {
                    $query->where('region_id', $regionId);
                })->take(5)->get(),

            'employee_creation_trend' => Employee::selectRaw('DATE(created_at) as date, COUNT(id) as count')
                ->whereBetween('created_at', [now()->subDays(7), now()])
                ->where('region_id', $regionId)
                ->groupBy('date')
                ->orderBy('date')
                ->get(),

            'agency_creation_trend' => Agency::selectRaw('DATE(created_at) as date, COUNT(id) as count')
                ->whereBetween('created_at', [now()->subDays(7), now()])
                ->whereHas('employee', function($query) use ($regionId) {
                    $query->where('region_id', $regionId);
                })
                ->groupBy('date')
                ->orderBy('date')
                ->get(),
        ];

        return view('manager.dashboard.index', compact('stats'));
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
