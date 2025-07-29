@extends('layouts.manager.master')

@section('title', 'Customers')

@section('content-header')
    Customers / <span class="text-primary">{{ auth('manager')->user()->region->name }} Region</span>
@endsection

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">Customers in {{ auth('manager')->user()->region->name }}</h5>
        <div class="d-flex gap-2">
            <div class="dropdown">
                <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="ti ti-filter me-1"></i>Filter
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{ route('manager.customers.index') }}">All Customers</a></li>
                    <li><a class="dropdown-item" href="{{ route('manager.customers.index', ['status' => 'active']) }}">Active Only</a></li>
                    <li><a class="dropdown-item" href="{{ route('manager.customers.index', ['status' => 'inactive']) }}">Inactive Only</a></li>
                </ul>
            </div>
            <a href="{{ route('manager.customers.create') }}" class="btn btn-primary">
                <i class="ti ti-plus me-1"></i>Add Customer
            </a>
        </div>
    </div>
    <div class="card-body">
        @if($customers->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Customer</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Agency</th>
                            <th>Family Members</th>
                            <th>Status</th>
                            <th>Joined</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($customers as $customer)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm me-3">
                                            <span class="avatar-initial rounded-circle bg-label-info">
                                                {{ substr($customer->name, 0, 1) }}
                                            </span>
                                        </div>
                                        <div>
                                            <h6 class="mb-0">{{ $customer->name }}</h6>
                                            <small class="text-muted">ID: {{ $customer->id }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $customer->email }}</td>
                                <td>{{ $customer->phone ?? 'N/A' }}</td>
                                <td>
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
                                        <span class="text-muted">Direct</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-label-warning">{{ $customer->family_members_count ?? 0 }}</span>
                                </td>
                                <td>
                                    <span class="badge {{ $customer->status ? 'bg-label-success' : 'bg-label-danger' }}">
                                        {{ $customer->status ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>{{ $customer->created_at->format('M d, Y') }}</td>
                                <td>
                                    <div class="dropdown">
                                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                            <i class="ti ti-dots-vertical"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item" href="{{ route('manager.customers.show', $customer) }}">
                                                <i class="ti ti-eye me-1"></i> View
                                            </a>
                                            <a class="dropdown-item" href="{{ route('manager.customers.edit', $customer) }}">
                                                <i class="ti ti-pencil me-1"></i> Edit
                                            </a>
                                            <div class="dropdown-divider"></div>
                                            <form action="{{ route('manager.customers.destroy', $customer) }}" method="POST" style="display: inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item text-danger" onclick="return confirm('Are you sure?')">
                                                    <i class="ti ti-trash me-1"></i> Delete
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($customers->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $customers->links() }}
                </div>
            @endif
        @else
            <div class="text-center py-5">
                <div class="avatar avatar-xl mx-auto mb-3">
                    <span class="avatar-initial rounded-circle bg-label-secondary">
                        <i class="ti ti-user-friends ti-lg"></i>
                    </span>
                </div>
                <h5 class="mb-1">No customers found</h5>
                <p class="text-muted">No customers are registered in your region yet.</p>
                <a href="{{ route('manager.customers.create') }}" class="btn btn-primary">
                    <i class="ti ti-plus me-1"></i>Add First Customer
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
