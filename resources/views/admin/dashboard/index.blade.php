@extends('layouts.admin.master')

@section('title', 'Dashboard')

@section('content-header', __('Dashboard'))

@section('breadcrumbs')
<li class="breadcrumb-item active">
  {{ __('Dashboard') }}
</li>
@endsection

@section('content')
<div class="dashboard-grid">
    {{-- Total Counts --}}
    <x-dashboard-card title="Total Agencies" :count="$stats['total_agencies']" color="#D3D3D3"/>
    <x-dashboard-card title="Total Customers" :count="$stats['total_customers']" color="#F5F5DC"/>
    <x-dashboard-card title="Total Family Members" :count="$stats['total_family_members']" color="#ADD8E6"/>
    <x-dashboard-card title="Total Employees" :count="$stats['total_employees']" color="#FFDAB9"/>

    {{-- Active Counts --}}
    <x-dashboard-card title="Active Agencies" :count="$stats['active_counts']['agencies']" color="#98FF98"/>
    <x-dashboard-card title="Active Customers" :count="$stats['active_counts']['customers']" color="#98FF98"/>
    <x-dashboard-card title="Active Employees" :count="$stats['active_counts']['employees']" color="#98FF98"/>

    {{-- Customer Creation Stats --}}
    <x-dashboard-card title="Customers Created Today" :count="$stats['customer_creation']->today" color="#ADD8E6"/>
    <x-dashboard-card title="Customers Created This Month" :count="$stats['customer_creation']->this_month" color="#87CEEB"/>
    <x-dashboard-card title="Customers Created This Year" :count="$stats['customer_creation']->this_year" color="#FFDAB9"/>

    {{-- Agency Creation Stats --}}
    <x-dashboard-card title="Agencies Created Today" :count="$stats['agency_creation']->today" color="#ADD8E6"/>
    <x-dashboard-card title="Agencies Created This Month" :count="$stats['agency_creation']->this_month" color="#87CEEB"/>
    <x-dashboard-card title="Agencies Created This Year" :count="$stats['agency_creation']->this_year" color="#FFDAB9"/>
</div>

{{-- Top Agencies --}}
<div class="mt-3 bg-white rounded-lg shadow p-3">
    <h2 class="text-xl font-semibold mb-4">Top 5 Agencies by Customer Count</h2>
    <ul class="trend-list">
        @foreach ($stats['top_agencies'] as $agency)
            <li class="py-3 border-b border-gray-200 last:border-0">
                {{ $agency->name }} ({{ $agency->customers_count }} customers)
            </li>
        @endforeach
    </ul>
</div>

{{-- Top Employees --}}
<div class="mt-3 bg-white rounded-lg shadow p-3">
    <h2 class="text-xl font-semibold mb-4">Top 5 Employees by Agency Count</h2>
    <ul class="trend-list">
        @foreach ($stats['top_employees'] as $employee)
            <li class="py-3 border-b border-gray-200 last:border-0">
                {{ $employee->name }} ({{ $employee->agencies_count }} agencies)
            </li>
        @endforeach
    </ul>
</div>

{{-- Employee & Agency Creation Trend --}}
<div class="mt-3 bg-white rounded-lg shadow p-3">
    <h2 class="text-xl font-semibold mb-4">Employee Creation Trend (Last 7 Days)</h2>
    <ul class="trend-list">
        @foreach ($stats['employee_creation_trend'] as $trend)
            <li class="py-3 border-b border-gray-200 last:border-0">
                {{ $trend->date }}: {{ $trend->count }} employees
            </li>
        @endforeach
    </ul>
</div>
<div class="mt-3 bg-white rounded-lg shadow p-3">
    <h2 class="text-xl font-semibold mb-4">Agency Creation Trend (Last 7 Days)</h2>
    <ul class="trend-list">
        @foreach ($stats['agency_creation_trend'] as $trend)
            <li class="py-3 border-b border-gray-200 last:border-0">
                {{ $trend->date }}: {{ $trend->count }} employees
            </li>
        @endforeach
    </ul>
</div>

@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('.data-table').DataTable();
    });
</script>
@endsection
