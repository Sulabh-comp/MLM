<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{Agency, Customer, FamilyMember};

class DashboardController extends Controller
{
    public function index()
    {
        $today = now()->toDateString();
        $startOfMonth = now()->startOfMonth()->toDateString();
        $startOfYear = now()->startOfYear()->toDateString();

        $stats = [
            'total_agencies' => Agency::where('employee_id', auth()->guard('employee')->id())->count(),
            'total_customers' => Customer::whereHas("agency.employee", function($query) {
                $query->where('id', auth()->guard('employee')->id());
            })->count(),
            'total_family_members' => FamilyMember::whereHas('customer.agency.employee', function($query) {
                $query->where('id', auth()->guard('employee')->id());
            })->count(),

            'active_counts' => [
                'agencies' => Agency::where('employee_id', auth()->guard('employee')->id())
                    ->where('status', 1)->count(),
                'customers' => Customer::whereHas("agency.employee", function($query) {
                    $query->where('id', auth()->guard('employee')->id());
                })->where('status', 1)->count(),
            ],

            'customer_creation' => Customer::selectRaw("
                COUNT(CASE WHEN DATE(created_at) = ? THEN 1 END) as today,
                COUNT(CASE WHEN created_at >= ? THEN 1 END) as this_month,
                COUNT(CASE WHEN created_at >= ? THEN 1 END) as this_year,
                COUNT(CASE WHEN created_at BETWEEN ? AND ? THEN 1 END) as last_month,
                COUNT(CASE WHEN created_at BETWEEN ? AND ? THEN 1 END) as last_year
            ", [
                $today, $startOfMonth, $startOfYear,
                now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth(),
                now()->subYear()->startOfYear(), now()->subYear()->endOfYear(),
            ])->whereHas("agency.employee", function($query) {
                $query->where('id', auth()->guard('employee')->id());
            })->first(),
            'agency_creation' => Agency::selectRaw("
                COUNT(CASE WHEN DATE(created_at) = ? THEN 1 END) as today,
                COUNT(CASE WHEN created_at >= ? THEN 1 END) as this_month,
                COUNT(CASE WHEN created_at >= ? THEN 1 END) as this_year,
                COUNT(CASE WHEN created_at BETWEEN ? AND ? THEN 1 END) as last_month,
                COUNT(CASE WHEN created_at BETWEEN ? AND ? THEN 1 END) as last_year
            ", [
                $today, $startOfMonth, $startOfYear,
                now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth(),
                now()->subYear()->startOfYear(), now()->subYear()->endOfYear(),
            ])->where("employee_id",auth()->guard("employee")->id())->first(),

            'top_agencies' => Agency::withCount('customers')->orderByDesc('customers_count')->where('employee_id', auth()->guard('employee')->id())->take(5)->get(),
            'top_customers' => Customer::withCount('familyMembers')->orderByDesc('family_members_count')->whereHas("agency.employee", function($query) {
                $query->where('id', auth()->guard('employee')->id());
            })->take(5)->get(),

            'agency_creation_trend' => Agency::selectRaw('DATE(created_at) as date, COUNT(id) as count')
                ->whereBetween('created_at', [now()->subDays(7), now()])
                ->groupBy('date')
                ->orderBy('date')
                ->where('employee_id', auth()->guard('employee')->id())
                ->get(),
        ];

        return view('employee.dashboard.index', compact('stats'));
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|confirmed',
        ]);

        $user = auth()->guard('employee')->user();

        if (!password_verify($request->current_password, $user->password)) {
            return back()->with('error', 'Invalid current password');
        }

        $user->password = bcrypt($request->password);
        $user->save();

        return back()->with('success', 'Password changed successfully');
    }
}
