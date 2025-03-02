@extends('layouts.employee.master')

@section('title', 'Dashboard')

@section('content-header', __('Dashboard'))

@section('breadcrumbs')
<li class="breadcrumb-item">
  <a href="{{ route('employee.dashboard') }}">{{ __('Dashboard') }}</a>
</li>
<li class="breadcrumb-item active">
  {{ __('Dashboard') }}
</li>
@endsection

@section('content')
<div class="dashboard-grid">
    {{-- Total Counts --}}
    <x-dashboard-card title="Total Agencies" :count="$stats['total_agencies']" />
    <x-dashboard-card title="Total Customers" :count="$stats['total_customers']" />
    <x-dashboard-card title="Total Family Members" :count="$stats['total_family_members']" />

    {{-- Active Counts --}}
    <x-dashboard-card title="Active Agencies" :count="$stats['active_counts']['agencies']" />
    <x-dashboard-card title="Active Customers" :count="$stats['active_counts']['customers']" />

    {{-- Customer Creation Stats --}}
    <x-dashboard-card title="Customers Created Today" :count="$stats['customer_creation']->today" />
    <x-dashboard-card title="Customers Created This Month" :count="$stats['customer_creation']->this_month" />
    <x-dashboard-card title="Customers Created This Year" :count="$stats['customer_creation']->this_year" />

    {{-- Agency Creation Stats --}}
    <x-dashboard-card title="Agencies Created Today" :count="$stats['agency_creation']->today" />
    <x-dashboard-card title="Agencies Created This Month" :count="$stats['agency_creation']->this_month" />
    <x-dashboard-card title="Agencies Created This Year" :count="$stats['agency_creation']->this_year" />
</div>

{{-- Top Agencies --}}
<div class="mt-8 bg-white rounded-lg shadow p-6">
    <h2 class="text-xl font-semibold mb-4">Top 5 Agencies by Customer Count</h2>
    <ul class="trend-list">
        @foreach ($stats['top_agencies'] as $agency)
            <li class="py-3 border-b border-gray-200 last:border-0">
                {{ $agency->name }} ({{ $agency->customers_count }} customers)
            </li>
        @endforeach
    </ul>
</div>


{{-- Employee & Agency Creation Trend --}}
<div class="mt-8 bg-white rounded-lg shadow p-6">
    <h2 class="text-xl font-semibold mb-4">Agency Creation Trend (Last 7 Days)</h2>
    <ul class="trend-list">
        @foreach ($stats['agency_creation_trend'] as $trend)
            <li class="py-3 border-b border-gray-200 last:border-0">
                {{ $trend->date }}: {{ $trend->count }} Agencies
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
