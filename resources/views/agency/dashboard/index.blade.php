@extends('layouts.agency.master')

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
    <x-dashboard-card title="Total Customers" :count="$stats['total_customers']" />
    <x-dashboard-card title="Total Family Members" :count="$stats['total_family_members']" />

    {{-- Active Counts --}}
    <x-dashboard-card title="Active Customers" :count="$stats['active_counts']['customers']" />

    {{-- Customer Creation Stats --}}
    <x-dashboard-card title="Customers Created Today" :count="$stats['customer_creation']->today" />
    <x-dashboard-card title="Customers Created This Month" :count="$stats['customer_creation']->this_month" />
    <x-dashboard-card title="Customers Created This Year" :count="$stats['customer_creation']->this_year" />

</div>

@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('.data-table').DataTable();
    });
</script>
@endsection
