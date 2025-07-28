<?php

namespace App\Http\Controllers\Manager;

use App\Models\Agency;
use App\Models\Customer;
use App\Models\FamilyMember;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        $manager = auth()->guard('manager')->user();
        $today = now()->toDateString();
        $startOfMonth = now()->startOfMonth()->toDateString();
        $startOfYear = now()->startOfYear()->toDateString();

        // Get statistics for the manager's region only
        $stats = [
            'total_employees' => $manager->employees()->count(),
            'total_agencies' => $manager->agencies()->count(),
            'total_customers' => Customer::whereHas('agency.employee', function($query) use ($manager) {
                $query->where('manager_id', $manager->id);
            })->count(),
            'total_family_members' => FamilyMember::whereHas('customer.agency.employee', function($query) use ($manager) {
                $query->where('manager_id', $manager->id);
            })->count(),

            'active_counts' => [
                'employees' => $manager->employees()->where('status', 1)->count(),
                'agencies' => $manager->agencies()->where('status', 1)->count(),
                'customers' => Customer::whereHas('agency.employee', function($query) use ($manager) {
                    $query->where('manager_id', $manager->id);
                })->where('status', 1)->count(),
                'family_members' => FamilyMember::whereHas('customer.agency.employee', function($query) use ($manager) {
                    $query->where('manager_id', $manager->id);
                })->count(),
            ],

            'customer_creation' => Customer::whereHas('agency.employee', function($query) use ($manager) {
                $query->where('manager_id', $manager->id);
            })->selectRaw("
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

            'agency_creation' => $manager->agencies()->selectRaw("
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
        ];

        return view('manager.dashboard', compact('stats', 'manager'));
    }
}
