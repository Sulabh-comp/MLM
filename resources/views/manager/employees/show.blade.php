@extends('layouts.manager.master')

@section('title', 'Employee Details')

@section('content-header')
    Employees / <span class="text-primary">{{ $employee->name }}</span>
@endsection

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Employee Information</h5>
                <div class="d-flex gap-2">
                    <a href="{{ route('manager.employees.edit', $employee) }}" class="btn btn-outline-primary btn-sm">
                        <i class="ti ti-pencil me-1"></i>Edit
                    </a>
                    <a href="{{ route('manager.employees.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="ti ti-arrow-left me-1"></i>Back
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-3"><strong>Name:</strong></div>
                    <div class="col-md-9">{{ $employee->name }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-3"><strong>Email:</strong></div>
                    <div class="col-md-9">{{ $employee->email }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-3"><strong>Phone:</strong></div>
                    <div class="col-md-9">{{ $employee->phone ?? 'N/A' }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-3"><strong>Designation:</strong></div>
                    <div class="col-md-9">{{ $employee->designation ?? 'N/A' }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-3"><strong>Manager:</strong></div>
                    <div class="col-md-9">
                        @if($employee->manager)
                            <div class="d-flex align-items-center">
                                <div class="avatar avatar-sm me-2">
                                    <span class="avatar-initial rounded-circle bg-label-primary">
                                        {{ substr($employee->manager->name, 0, 1) }}
                                    </span>
                                </div>
                                <div>
                                    <span class="fw-semibold">{{ $employee->manager->name }}</span>
                                    @if($employee->manager->level_name)
                                        <br><small class="text-muted">{{ $employee->manager->level_name }}</small>
                                    @endif
                                </div>
                            </div>
                        @else
                            <span class="text-muted">Not Assigned</span>
                        @endif
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-3"><strong>Status:</strong></div>
                    <div class="col-md-9">
                        <span class="badge {{ $employee->status ? 'bg-label-success' : 'bg-label-danger' }}">
                            {{ $employee->status ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                </div>
                @if($employee->address)
                <div class="row mb-3">
                    <div class="col-md-3"><strong>Address:</strong></div>
                    <div class="col-md-9">{{ $employee->address }}</div>
                </div>
                @endif
                <div class="row mb-3">
                    <div class="col-md-3"><strong>Joined:</strong></div>
                    <div class="col-md-9">{{ $employee->created_at->format('M d, Y') }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0">Hierarchy & Statistics</h6>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="avatar flex-shrink-0 me-3">
                        <span class="avatar-initial rounded bg-label-primary">
                            <i class="ti ti-user-check"></i>
                        </span>
                    </div>
                    <div>
                        <p class="mb-0">Direct Manager</p>
                        <h6 class="mb-0">
                            {{ $employee->manager ? $employee->manager->name : 'Not Assigned' }}
                        </h6>
                        @if($employee->manager && $employee->manager->level_name)
                            <small class="text-muted">{{ $employee->manager->level_name }}</small>
                        @endif
                    </div>
                </div>
                
                <div class="d-flex align-items-center mb-3">
                    <div class="avatar flex-shrink-0 me-3">
                        <span class="avatar-initial rounded bg-label-success">
                            <i class="ti ti-building"></i>
                        </span>
                    </div>
                    <div>
                        <p class="mb-0">Agencies Managed</p>
                        <h6 class="mb-0">{{ $employee->agencies()->count() }}</h6>
                    </div>
                </div>
                
                <div class="d-flex align-items-center mb-3">
                    <div class="avatar flex-shrink-0 me-3">
                        <span class="avatar-initial rounded bg-label-info">
                            <i class="ti ti-user-friends"></i>
                        </span>
                    </div>
                    <div>
                        <p class="mb-0">Total Customers</p>
                        <h6 class="mb-0">{{ $employee->agencies()->withCount('customers')->get()->sum('customers_count') }}</h6>
                    </div>
                </div>

                @if($employee->manager)
                <hr>
                <div class="d-flex align-items-center mb-3">
                    <div class="avatar flex-shrink-0 me-3">
                        <span class="avatar-initial rounded bg-label-warning">
                            <i class="ti ti-sitemap"></i>
                        </span>
                    </div>
                    <div>
                        <p class="mb-0">Hierarchy Colleagues</p>
                        <h6 class="mb-0">{{ $employee->colleagues()->count() }}</h6>
                    </div>
                </div>
                @endif
            </div>
        </div>

        @if($employee->agencies()->count() > 0)
        <div class="card mt-3">
            <div class="card-header">
                <h6 class="card-title mb-0">Assigned Agencies</h6>
            </div>
            <div class="card-body">
                @foreach($employee->agencies()->limit(5)->get() as $agency)
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar avatar-sm me-2">
                            <span class="avatar-initial rounded-circle bg-label-info">
                                {{ substr($agency->name, 0, 1) }}
                            </span>
                        </div>
                        <div>
                            <h6 class="mb-0">{{ $agency->name }}</h6>
                            <small class="text-muted">{{ $agency->customers()->count() }} customers</small>
                        </div>
                    </div>
                @endforeach
                
                @if($employee->agencies()->count() > 5)
                    <small class="text-muted">And {{ $employee->agencies()->count() - 5 }} more agencies...</small>
                @endif
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
