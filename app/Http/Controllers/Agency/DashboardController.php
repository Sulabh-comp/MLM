<?php

namespace App\Http\Controllers\Agency;

use App\Models\Customer;
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
            'total_customers' => Customer::where('agency_id', auth()->guard('agency')->id())->count(),
            'total_family_members' => FamilyMember::whereHas('customer', function ($query) {
                $query->where('agency_id', auth()->guard('agency')->id());
            })->count(),

            'active_counts' => [
                'customers' => Customer::where('agency_id', auth()->guard('agency')->id())->where('status', 1)->count(),
                'family_members' => FamilyMember::whereHas('customer', function ($query) {
                    $query->where('agency_id', auth()->guard('agency')->id());
                })->count(),
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
            ])->where('agency_id', auth()->guard('agency')->id())->first(),
        ];

        return view('agency.dashboard.index', compact('stats'));
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|confirmed',
        ]);

        $user = auth()->guard('agency')->user();

        if (!password_verify($request->current_password, $user->password)) {
            return back()->with('error', 'Invalid current password');
        }

        $user->password = bcrypt($request->password);
        $user->save();

        return back()->with('success', 'Password changed successfully');
    }
}
