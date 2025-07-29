@extends('layouts.manager.master')

@section('title', 'Agencies')

@section('content-header')
    Agencies / <span class="text-primary">{{ auth('manager')->user()->region->name }} Region</span>
@endsection

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">Agencies in {{ auth('manager')->user()->region->name }}</h5>
        <a href="{{ route('manager.agencies.create') }}" class="btn btn-primary">
            <i class="ti ti-plus me-1"></i>Add Agency
        </a>
    </div>
    <div class="card-body">
        @if($agencies->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Agency</th>
                            <th>Email</th>
                            <th>Employee</th>
                            <th>Customers</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($agencies as $agency)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm me-3">
                                            <span class="avatar-initial rounded-circle bg-label-success">
                                                {{ substr($agency->name, 0, 1) }}
                                            </span>
                                        </div>
                                        <div>
                                            <h6 class="mb-0">{{ $agency->name }}</h6>
                                            <small class="text-muted">ID: {{ $agency->id }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $agency->email }}</td>
                                <td>
                                    @if($agency->employee)
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-xs me-2">
                                                <span class="avatar-initial rounded-circle bg-label-primary">
                                                    {{ substr($agency->employee->name, 0, 1) }}
                                                </span>
                                            </div>
                                            <span>{{ $agency->employee->name }}</span>
                                        </div>
                                    @else
                                        <span class="text-muted">Not assigned</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-label-info">{{ $agency->customers_count ?? 0 }} customers</span>
                                </td>
                                <td>
                                    <span class="badge {{ $agency->status ? 'bg-label-success' : 'bg-label-danger' }}">
                                        {{ $agency->status ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>{{ $agency->created_at->format('M d, Y') }}</td>
                                <td>
                                    <div class="dropdown">
                                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                            <i class="ti ti-dots-vertical"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item" href="{{ route('manager.agencies.show', $agency) }}">
                                                <i class="ti ti-eye me-1"></i> View
                                            </a>
                                            <a class="dropdown-item" href="{{ route('manager.agencies.edit', $agency) }}">
                                                <i class="ti ti-pencil me-1"></i> Edit
                                            </a>
                                            <div class="dropdown-divider"></div>
                                            <form action="{{ route('manager.agencies.destroy', $agency) }}" method="POST" style="display: inline;">
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
            @if($agencies->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $agencies->links() }}
                </div>
            @endif
        @else
            <div class="text-center py-5">
                <div class="avatar avatar-xl mx-auto mb-3">
                    <span class="avatar-initial rounded-circle bg-label-secondary">
                        <i class="ti ti-building ti-lg"></i>
                    </span>
                </div>
                <h5 class="mb-1">No agencies found</h5>
                <p class="text-muted">No agencies are registered in your region yet.</p>
                <a href="{{ route('manager.agencies.create') }}" class="btn btn-primary">
                    <i class="ti ti-plus me-1"></i>Add First Agency
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
