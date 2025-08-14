@extends('layouts.manager.master')

@section('title', 'Manager Dashboard')

@section('content-header')
    Dashboard / <span class="text-primary">{{ $stats['hierarchy_info']['current_level'] }} - {{ auth('manager')->user()->name }}</span>
@endsection

@section('content')
<!-- Hierarchy Overview -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Hierarchy Overview</h5>
                <a href="{{ route('manager.managers.index') }}" class="btn btn-outline-primary btn-sm">
                    <i class="ti ti-users"></i> Manage Team
                </a>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="d-flex align-items-center">
                            <div class="avatar me-3">
                                <span class="avatar-initial rounded bg-label-primary">
                                    <i class="ti ti-crown"></i>
                                </span>
                            </div>
                            <div>
                                <p class="mb-0">Current Level</p>
                                <h6 class="mb-0">{{ $stats['hierarchy_info']['current_level'] }}</h6>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="d-flex align-items-center">
                            <div class="avatar me-3">
                                <span class="avatar-initial rounded bg-label-success">
                                    <i class="ti ti-users"></i>
                                </span>
                            </div>
                            <div>
                                <p class="mb-0">Direct Subordinates</p>
                                <h6 class="mb-0">{{ $stats['hierarchy_info']['direct_subordinates'] }}</h6>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="d-flex align-items-center">
                            <div class="avatar me-3">
                                <span class="avatar-initial rounded bg-label-info">
                                    <i class="ti ti-sitemap"></i>
                                </span>
                            </div>
                            <div>
                                <p class="mb-0">Total Team</p>
                                <h6 class="mb-0">{{ $stats['hierarchy_info']['total_subordinates'] }}</h6>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="d-flex align-items-center">
                            <div class="avatar me-3">
                                <span class="avatar-initial rounded bg-label-warning">
                                    <i class="ti ti-arrow-up"></i>
                                </span>
                            </div>
                            <div>
                                <p class="mb-0">Reports To</p>
                                <h6 class="mb-0">{{ $stats['hierarchy_info']['parent_manager'] }}</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row">
    <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 mb-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div class="card-info">
                        <p class="card-text">Total Employees</p>
                        <div class="d-flex align-items-end mb-2">
                            <h4 class="card-title mb-0 me-2">{{ $stats['total_employees'] }}</h4>
                        </div>
                        <small class="text-muted">In your hierarchy</small>
                    </div>
                    <div class="card-icon">
                        <span class="badge bg-label-primary rounded p-2">
                            <i class="ti ti-users ti-sm"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

                        <small class="text-muted">In your hierarchy</small>
                    </div>
                    <div class="card-icon">
                        <span class="badge bg-label-primary rounded p-2">
                            <i class="ti ti-users ti-sm"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 mb-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div class="card-info">
                        <p class="card-text">Total Agencies</p>
                        <div class="d-flex align-items-end mb-2">
                            <h4 class="card-title mb-0 me-2">{{ $stats['total_agencies'] }}</h4>
                        </div>
                        <small class="text-muted">Under your management</small>
                    </div>
                    <div class="card-icon">
                        <span class="badge bg-label-success rounded p-2">
                            <i class="ti ti-building ti-sm"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 mb-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div class="card-info">
                        <p class="card-text">Total Customers</p>
                        <div class="d-flex align-items-end mb-2">
                            <h4 class="card-title mb-0 me-2">{{ $stats['total_customers'] }}</h4>
                        </div>
                        <small class="text-muted">Across your territory</small>
                    </div>
                    <div class="card-icon">
                        <span class="badge bg-label-info rounded p-2">
                            <i class="ti ti-user-friends ti-sm"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 mb-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div class="card-info">
                        <p class="card-text">Family Members</p>
                        <div class="d-flex align-items-end mb-2">
                            <h4 class="card-title mb-0 me-2">{{ $stats['total_family_members'] }}</h4>
                        </div>
                        <small class="text-muted">Total reach</small>
                    </div>
                    <div class="card-icon">
                        <span class="badge bg-label-warning rounded p-2">
                            <i class="ti ti-users-group ti-sm"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Subordinate Performance -->
@if($stats['subordinate_performance']->count() > 0)
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Subordinate Manager Performance</h5>
                <a href="{{ route('manager.managers.create') }}" class="btn btn-primary btn-sm">
                    <i class="ti ti-plus"></i> Add Manager
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-borderless">
                        <thead>
                            <tr>
                                <th>Manager</th>
                                <th>Level</th>
                                <th>Employees</th>
                                <th>Agencies</th>
                                <th>Customers</th>
                                <th>Performance</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($stats['subordinate_performance'] as $performance)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm me-3">
                                                <span class="avatar-initial rounded-circle bg-label-primary">
                                                    {{ substr($performance['manager']->name, 0, 1) }}
                                                </span>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">{{ $performance['manager']->name }}</h6>
                                                <small class="text-muted">{{ $performance['manager']->email }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-label-primary">{{ $performance['manager']->level_name ?? 'Not Set' }}</span>
                                    </td>
                                    <td>
                                        <div class="text-center">
                                            <strong>{{ $performance['employees_count'] }}</strong>
                                            <br><small class="text-success">{{ $performance['active_employees'] }} active</small>
                                        </div>
                                    </td>
                                    <td class="text-center">{{ $performance['agencies_count'] }}</td>
                                    <td class="text-center">{{ $performance['customers_count'] }}</td>
                                    <td>
                                        @php
                                            $total = $performance['employees_count'] + $performance['agencies_count'] + $performance['customers_count'];
                                            $performance_score = $total > 0 ? ($performance['active_employees'] / max($performance['employees_count'], 1)) * 100 : 0;
                                        @endphp
                                        <div class="d-flex align-items-center">
                                            <div class="progress flex-grow-1 me-2" style="height: 6px">
                                                <div class="progress-bar" style="width: {{ $performance_score }}%"></div>
                                            </div>
                                            <small>{{ number_format($performance_score, 0) }}%</small>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<div class="row">
    <!-- Active Status Card -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title m-0 me-2">Active Status</h5>
                <div class="dropdown">
                    <button class="btn p-0" type="button" id="activeStats" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="ti ti-dots-vertical"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-6">
                        <div class="d-flex align-items-center mb-3">
                            <div class="avatar flex-shrink-0 me-3">
                                <span class="avatar-initial rounded bg-label-success">
                                    <i class="ti ti-users"></i>
                                </span>
                            </div>
                            <div>
                                <p class="mb-0">Active Employees</p>
                                <h5 class="mb-0">{{ $stats['active_counts']['employees'] }}</h5>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="d-flex align-items-center">
                            <div class="avatar flex-shrink-0 me-3">
                                <span class="avatar-initial rounded bg-label-success">
                                    <i class="ti ti-building"></i>
                                </span>
                            </div>
                            <div>
                                <p class="mb-0">Active Agencies</p>
                                <h5 class="mb-0">{{ $stats['active_counts']['agencies'] }}</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Inactive Status Card -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title m-0 me-2">Inactive Status</h5>
                <div class="dropdown">
                    <button class="btn p-0" type="button" id="inactiveStats" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="ti ti-dots-vertical"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-6">
                        <div class="d-flex align-items-center mb-3">
                            <div class="avatar flex-shrink-0 me-3">
                                <span class="avatar-initial rounded bg-label-danger">
                                    <i class="ti ti-users"></i>
                                </span>
                            </div>
                            <div>
                                <p class="mb-0">Inactive Employees</p>
                                <h5 class="mb-0">{{ $stats['inactive_counts']['employees'] }}</h5>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="d-flex align-items-center">
                            <div class="avatar flex-shrink-0 me-3">
                                <span class="avatar-initial rounded bg-label-danger">
                                    <i class="ti ti-building"></i>
                                </span>
                            </div>
                            <div>
                                <p class="mb-0">Inactive Agencies</p>
                                <h5 class="mb-0">{{ $stats['inactive_counts']['agencies'] }}</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Recent Agencies -->
    <div class="col-md-8 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Recent Agencies</h5>
                <a href="{{ route('manager.agencies.index') }}" class="btn btn-sm btn-primary">View All</a>
            </div>
            <div class="card-body">
                @if($stats['recent_agencies']->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-borderless">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Employee</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($stats['recent_agencies'] as $agency)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar avatar-sm me-3">
                                                    <span class="avatar-initial rounded-circle bg-label-primary">
                                                        {{ substr($agency->name, 0, 1) }}
                                                    </span>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0">{{ $agency->name }}</h6>
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ $agency->email }}</td>
                                        <td>{{ $agency->employee->name ?? 'N/A' }}</td>
                                        <td>
                                            <span class="badge {{ $agency->status ? 'bg-label-success' : 'bg-label-danger' }}">
                                                {{ $agency->status ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        <td>{{ $agency->created_at->format('M d, Y') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <div class="avatar avatar-xl mx-auto mb-3">
                            <span class="avatar-initial rounded-circle bg-label-secondary">
                                <i class="ti ti-building ti-lg"></i>
                            </span>
                        </div>
                        <h6 class="mb-1">No agencies found</h6>
                        <p class="text-muted">No agencies found in your region yet.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Top Agencies -->
    <div class="col-md-4 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Top Agencies</h5>
                <small class="text-muted">By customer count</small>
            </div>
            <div class="card-body">
                @if($stats['top_agencies']->count() > 0)
                    @foreach($stats['top_agencies'] as $agency)
                        <div class="d-flex align-items-center mb-3">
                            <div class="avatar avatar-sm me-3">
                                <span class="avatar-initial rounded-circle bg-label-primary">
                                    {{ substr($agency->name, 0, 1) }}
                                </span>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-0">{{ $agency->name }}</h6>
                                <small class="text-muted">{{ $agency->email }}</small>
                            </div>
                            <div class="text-end">
                                <span class="badge bg-label-primary">{{ $agency->customers_count }}</span>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="text-center py-4">
                        <div class="avatar avatar-lg mx-auto mb-3">
                            <span class="avatar-initial rounded-circle bg-label-secondary">
                                <i class="ti ti-trophy"></i>
                            </span>
                        </div>
                        <h6 class="mb-1">No data</h6>
                        <p class="text-muted mb-0">No agencies found.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
