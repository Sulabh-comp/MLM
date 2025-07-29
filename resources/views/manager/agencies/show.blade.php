@extends('layouts.manager.master')

@section('title', 'Agency Details')

@section('content-header')
    Agencies / <span class="text-primary">{{ $agency->name }}</span>
@endsection

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Agency Information</h5>
                <div class="d-flex gap-2">
                    <a href="{{ route('manager.agencies.edit', $agency) }}" class="btn btn-outline-primary btn-sm">
                        <i class="ti ti-pencil me-1"></i>Edit
                    </a>
                    <a href="{{ route('manager.agencies.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="ti ti-arrow-left me-1"></i>Back
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-3"><strong>Name:</strong></div>
                    <div class="col-md-9">{{ $agency->name }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-3"><strong>Email:</strong></div>
                    <div class="col-md-9">{{ $agency->email }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-3"><strong>Phone:</strong></div>
                    <div class="col-md-9">{{ $agency->phone ?? 'N/A' }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-3"><strong>Employee:</strong></div>
                    <div class="col-md-9">
                        @if($agency->employee)
                            <div class="d-flex align-items-center">
                                <div class="avatar avatar-xs me-2">
                                    <span class="avatar-initial rounded-circle bg-label-primary">
                                        {{ substr($agency->employee->name, 0, 1) }}
                                    </span>
                                </div>
                                <span>{{ $agency->employee->name }} ({{ $agency->employee->designation }})</span>
                            </div>
                        @else
                            Not assigned
                        @endif
                    </div>
                </div>
                @if($agency->license_number)
                <div class="row mb-3">
                    <div class="col-md-3"><strong>License Number:</strong></div>
                    <div class="col-md-9">{{ $agency->license_number }}</div>
                </div>
                @endif
                <div class="row mb-3">
                    <div class="col-md-3"><strong>Status:</strong></div>
                    <div class="col-md-9">
                        <span class="badge {{ $agency->status ? 'bg-label-success' : 'bg-label-danger' }}">
                            {{ $agency->status ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                </div>
                @if($agency->address)
                <div class="row mb-3">
                    <div class="col-md-3"><strong>Address:</strong></div>
                    <div class="col-md-9">{{ $agency->address }}</div>
                </div>
                @endif
                @if($agency->description)
                <div class="row mb-3">
                    <div class="col-md-3"><strong>Description:</strong></div>
                    <div class="col-md-9">{{ $agency->description }}</div>
                </div>
                @endif
                <div class="row mb-3">
                    <div class="col-md-3"><strong>Created:</strong></div>
                    <div class="col-md-9">{{ $agency->created_at->format('M d, Y H:i') }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0">Statistics</h6>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="avatar flex-shrink-0 me-3">
                        <span class="avatar-initial rounded bg-label-info">
                            <i class="ti ti-user-friends"></i>
                        </span>
                    </div>
                    <div>
                        <p class="mb-0">Total Customers</p>
                        <h6 class="mb-0">{{ $agency->customers()->count() }}</h6>
                    </div>
                </div>
                
                <div class="d-flex align-items-center mb-3">
                    <div class="avatar flex-shrink-0 me-3">
                        <span class="avatar-initial rounded bg-label-success">
                            <i class="ti ti-user-check"></i>
                        </span>
                    </div>
                    <div>
                        <p class="mb-0">Active Customers</p>
                        <h6 class="mb-0">{{ $agency->customers()->where('status', 1)->count() }}</h6>
                    </div>
                </div>

                <div class="d-flex align-items-center mb-3">
                    <div class="avatar flex-shrink-0 me-3">
                        <span class="avatar-initial rounded bg-label-warning">
                            <i class="ti ti-users-group"></i>
                        </span>
                    </div>
                    <div>
                        <p class="mb-0">Family Members</p>
                        <h6 class="mb-0">{{ $agency->customers()->withCount('familyMembers')->get()->sum('family_members_count') }}</h6>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
