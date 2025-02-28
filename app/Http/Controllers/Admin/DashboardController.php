<?php

namespace App\Http\Controllers\Admin;

use App\Models\Agency;
use App\Models\Customer;
use App\Models\Employee;
use App\Models\FamilyMember;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        $today = now()->toDateString();
        $startOfMonth = now()->startOfMonth()->toDateString();
        $startOfYear = now()->startOfYear()->toDateString();

        $stats = [
            'total_agencies' => Agency::count(),
            'total_customers' => Customer::count(),
            'total_family_members' => FamilyMember::count(),
            'total_employees' => Employee::count(),

            'active_counts' => [
                'agencies' => Agency::where('status', 1)->count(),
                'customers' => Customer::where('status', 1)->count(),
                'family_members' => FamilyMember::count(),
                'employees' => Employee::where('status', 1)->count(),
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
            ])->first(),

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
            ])->first(),

            'top_agencies' => Agency::withCount('customers')->orderByDesc('customers_count')->take(5)->get(),
            'top_employees' => Employee::withCount('agencies')->orderByDesc('agencies_count')->take(5)->get(),

            'employee_creation_trend' => Employee::selectRaw('DATE(created_at) as date, COUNT(id) as count')
                ->whereBetween('created_at', [now()->subDays(7), now()])
                ->groupBy('date')
                ->orderBy('date')
                ->get(),

            'agency_creation_trend' => Agency::selectRaw('DATE(created_at) as date, COUNT(id) as count')
                ->whereBetween('created_at', [now()->subDays(7), now()])
                ->groupBy('date')
                ->orderBy('date')
                ->get(),
        ];

        return view('admin.dashboard.index', compact('stats'));
    }
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|confirmed',
        ]);

        $user = auth()->guard('admin')->user();

        if (!password_verify($request->current_password, $user->password)) {
            return back()->with('error', 'Invalid current password');
        }

        $user->password = bcrypt($request->password);
        $user->save();

        return back()->with('success', 'Password changed successfully');
    }
}
