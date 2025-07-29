@extends('layouts.manager.master')

@section('title', 'Employees')

@section('content-header')
    Employees / <span class="text-primary">{{ auth('manager')->user()->region->name }} Region</span>
@endsection

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">Employees in {{ auth('manager')->user()->region->name }}</h5>
        <a href="{{ route('manager.employees.create') }}" class="btn btn-primary">
            <i class="ti ti-plus me-1"></i>Add Employee
        </a>
    </div>
    <div class="card-body">
        @if($employees->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Employee</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Status</th>
                            <th>Region</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($employees as $employee)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm me-3">
                                            <span class="avatar-initial rounded-circle bg-label-primary">
                                                {{ substr($employee->name, 0, 1) }}
                                            </span>
                                        </div>
                                        <div>
                                            <h6 class="mb-0">{{ $employee->name }}</h6>
                                            <small class="text-muted">ID: {{ $employee->id }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $employee->email }}</td>
                                <td>{{ $employee->phone ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge {{ $employee->status ? 'bg-label-success' : 'bg-label-danger' }}">
                                        {{ $employee->status ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-label-info">{{ $employee->region->name }}</span>
                                </td>
                                <td>{{ $employee->created_at->format('M d, Y') }}</td>
                                <td>
                                    <div class="dropdown">
                                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                            <i class="ti ti-dots-vertical"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item" href="{{ route('manager.employees.show', $employee) }}">
                                                <i class="ti ti-eye me-1"></i> View
                                            </a>
                                            <a class="dropdown-item" href="{{ route('manager.employees.edit', $employee) }}">
                                                <i class="ti ti-pencil me-1"></i> Edit
                                            </a>
                                            <div class="dropdown-divider"></div>
                                            <form action="{{ route('manager.employees.destroy', $employee) }}" method="POST" style="display: inline;">
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
            @if($employees->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $employees->links() }}
                </div>
            @endif
        @else
            <div class="text-center py-5">
                <div class="avatar avatar-xl mx-auto mb-3">
                    <span class="avatar-initial rounded-circle bg-label-secondary">
                        <i class="ti ti-users ti-lg"></i>
                    </span>
                </div>
                <h5 class="mb-1">No employees found</h5>
                <p class="text-muted">No employees are assigned to your region yet.</p>
                <a href="{{ route('manager.employees.create') }}" class="btn btn-primary">
                    <i class="ti ti-plus me-1"></i>Add First Employee
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
