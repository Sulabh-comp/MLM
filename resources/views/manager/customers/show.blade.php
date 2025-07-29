@extends('layouts.manager.master')

@section('title', 'Customer Details')

@section('content-header')
    Customers / <span class="text-primary">{{ $customer->name }}</span>
@endsection

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Customer Information</h5>
                <div class="d-flex gap-2">
                    <a href="{{ route('manager.customers.edit', $customer) }}" class="btn btn-outline-primary btn-sm">
                        <i class="ti ti-pencil me-1"></i>Edit
                    </a>
                    <a href="{{ route('manager.customers.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="ti ti-arrow-left me-1"></i>Back
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-3"><strong>Name:</strong></div>
                    <div class="col-md-9">{{ $customer->name }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-3"><strong>Email:</strong></div>
                    <div class="col-md-9">{{ $customer->email }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-3"><strong>Phone:</strong></div>
                    <div class="col-md-9">{{ $customer->phone ?? 'N/A' }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-3"><strong>Agency:</strong></div>
                    <div class="col-md-9">
                        @if($customer->agency)
                            <div class="d-flex align-items-center">
                                <div class="avatar avatar-xs me-2">
                                    <span class="avatar-initial rounded-circle bg-label-success">
                                        {{ substr($customer->agency->name, 0, 1) }}
                                    </span>
                                </div>
                                <span>{{ $customer->agency->name }}</span>
                            </div>
                        @else
                            <span class="text-muted">Direct Customer</span>
                        @endif
                    </div>
                </div>
                @if($customer->date_of_birth)
                <div class="row mb-3">
                    <div class="col-md-3"><strong>Date of Birth:</strong></div>
                    <div class="col-md-9">{{ \Carbon\Carbon::parse($customer->date_of_birth)->format('M d, Y') }}</div>
                </div>
                @endif
                <div class="row mb-3">
                    <div class="col-md-3"><strong>Status:</strong></div>
                    <div class="col-md-9">
                        <span class="badge {{ $customer->status ? 'bg-label-success' : 'bg-label-danger' }}">
                            {{ $customer->status ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                </div>
                @if($customer->address)
                <div class="row mb-3">
                    <div class="col-md-3"><strong>Address:</strong></div>
                    <div class="col-md-9">{{ $customer->address }}</div>
                </div>
                @endif
                @if($customer->notes)
                <div class="row mb-3">
                    <div class="col-md-3"><strong>Notes:</strong></div>
                    <div class="col-md-9">{{ $customer->notes }}</div>
                </div>
                @endif
                <div class="row mb-3">
                    <div class="col-md-3"><strong>Joined:</strong></div>
                    <div class="col-md-9">{{ $customer->created_at->format('M d, Y H:i') }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0">Family Members</h6>
            </div>
            <div class="card-body">
                @if($customer->familyMembers && $customer->familyMembers->count() > 0)
                    @foreach($customer->familyMembers as $member)
                        <div class="d-flex align-items-center mb-3">
                            <div class="avatar avatar-sm me-3">
                                <span class="avatar-initial rounded-circle bg-label-primary">
                                    {{ substr($member->name, 0, 1) }}
                                </span>
                            </div>
                            <div>
                                <h6 class="mb-0">{{ $member->name }}</h6>
                                <small class="text-muted">{{ $member->relationship ?? 'Family Member' }}</small>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="text-center py-3">
                        <div class="avatar avatar-lg mx-auto mb-3">
                            <span class="avatar-initial rounded-circle bg-label-secondary">
                                <i class="ti ti-users"></i>
                            </span>
                        </div>
                        <h6 class="mb-1">No family members</h6>
                        <p class="text-muted mb-0">No family members registered</p>
                    </div>
                @endif
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <h6 class="card-title mb-0">Statistics</h6>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="avatar flex-shrink-0 me-3">
                        <span class="avatar-initial rounded bg-label-warning">
                            <i class="ti ti-users-group"></i>
                        </span>
                    </div>
                    <div>
                        <p class="mb-0">Family Members</p>
                        <h6 class="mb-0">{{ $customer->familyMembers ? $customer->familyMembers->count() : 0 }}</h6>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
