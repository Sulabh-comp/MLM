@extends('layouts.manager.master')

@section('title', 'Employees')

@section('content-header')
    Employees / <span class="text-primary">{{ auth('manager')->user()->level_name ?? 'Manager' }} Team</span>
@endsection

@section('content')
<!-- Employee Statistics -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card">
            <div class="card-body text-center">
                <div class="avatar avatar-md mx-auto mb-3">
                    <span class="avatar-initial rounded-circle bg-label-primary">
                        <i class="ti ti-users ti-md"></i>
                    </span>
                </div>
                <h4 class="mb-0">{{ $stats['direct_employees'] }}</h4>
                <span class="text-muted">Direct Employees</span>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body text-center">
                <div class="avatar avatar-md mx-auto mb-3">
                    <span class="avatar-initial rounded-circle bg-label-success">
                        <i class="ti ti-sitemap ti-md"></i>
                    </span>
                </div>
                <h4 class="mb-0">{{ $stats['total_employees'] }}</h4>
                <span class="text-muted">Total in Hierarchy</span>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body text-center">
                <div class="avatar avatar-md mx-auto mb-3">
                    <span class="avatar-initial rounded-circle bg-label-info">
                        <i class="ti ti-user-check ti-md"></i>
                    </span>
                </div>
                <h4 class="mb-0">{{ $stats['active_employees'] }}</h4>
                <span class="text-muted">Active</span>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body text-center">
                <div class="avatar avatar-md mx-auto mb-3">
                    <span class="avatar-initial rounded-circle bg-label-warning">
                        <i class="ti ti-user-x ti-md"></i>
                    </span>
                </div>
                <h4 class="mb-0">{{ $stats['inactive_employees'] }}</h4>
                <span class="text-muted">Inactive</span>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">Employees in Your Hierarchy</h5>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-secondary btn-sm" onclick="toggleView()">
                <i class="ti ti-layout-grid" id="viewIcon"></i> <span id="viewText">Hierarchy View</span>
            </button>
            <a href="{{ route('manager.employees.create') }}" class="btn btn-primary">
                <i class="ti ti-plus me-1"></i>Add Employee
            </a>
        </div>
    </div>
    <div class="card-body">
        @if($employees->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover" id="employeeTable">
                    <thead>
                        <tr>
                            <th>Employee</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Manager</th>
                            <th>Agencies</th>
                            <th>Status</th>
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
                                            <small class="text-muted">{{ $employee->designation ?? 'N/A' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $employee->email }}</td>
                                <td>{{ $employee->phone ?? 'N/A' }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if($employee->manager)
                                            <div class="hierarchy-indicator me-2">
                                                @if($employee->manager_id !== $manager->id)
                                                    {{ str_repeat('—', ($employee->manager->depth ?? 0) - ($manager->depth ?? 0)) }}
                                                @endif
                                            </div>
                                            <div>
                                                <span class="fw-semibold">{{ $employee->manager->name }}</span>
                                                @if($employee->manager->level_name)
                                                    <br><small class="text-muted">{{ $employee->manager->level_name }}</small>
                                                @endif
                                            </div>
                                        @else
                                            <span class="text-muted">Not Assigned</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-label-info">{{ $employee->agencies()->count() }}</span>
                                </td>
                                <td>
                                    <span class="badge {{ $employee->status ? 'bg-label-success' : 'bg-label-danger' }}">
                                        {{ $employee->status ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>{{ $employee->created_at->format('M d, Y') }}</td>
                                <td>
                                    <div class="dropdown">
                                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                            <i class="ti ti-dots-vertical"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item" href="{{ route('manager.employees.show', $employee) }}">
                                                <i class="ti ti-eye me-1"></i> View Details
                                            </a>
                                            <a class="dropdown-item" href="{{ route('manager.employees.edit', $employee) }}">
                                                <i class="ti ti-pencil me-1"></i> Edit
                                            </a>
                                            <div class="dropdown-divider"></div>
                                            <a class="dropdown-item" href="javascript:void(0)" onclick="changeStatus({{ $employee->id }})">
                                                <i class="ti ti-power me-1"></i> Change Status
                                            </a>
                                            <div class="dropdown-divider"></div>
                                            <form action="{{ route('manager.employees.destroy', $employee) }}" method="POST" style="display: inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item text-danger" onclick="return confirm('Are you sure? This will also affect their agencies.')">
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
                <p class="text-muted">No employees are assigned to your hierarchy yet.</p>
                <a href="{{ route('manager.employees.create') }}" class="btn btn-primary">
                    <i class="ti ti-plus me-1"></i>Add First Employee
                </a>
            </div>
        @endif
    </div>
</div>

<!-- Status Change Modal -->
<div class="modal fade" id="changeStatusModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="changeStatusForm" method="POST" action="{{ route('manager.employees.updateStatus') }}">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Change Employee Status</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="employeeId">
                    <p>Are you sure you want to change the status of this employee?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Change Status</button>
                </div>
            </form>
        </div>
    </div>
</div>

@section('scripts')
<script>
    let isHierarchyView = false;

    function toggleView() {
        const table = document.getElementById('employeeTable');
        const viewIcon = document.getElementById('viewIcon');
        const viewText = document.getElementById('viewText');
        
        isHierarchyView = !isHierarchyView;
        
        if (isHierarchyView) {
            // Show hierarchy view
            table.classList.add('hierarchy-view');
            viewIcon.className = 'ti ti-list';
            viewText.textContent = 'List View';
        } else {
            // Show normal view
            table.classList.remove('hierarchy-view');
            viewIcon.className = 'ti ti-layout-grid';
            viewText.textContent = 'Hierarchy View';
        }
    }

    function changeStatus(id) {
        document.getElementById('employeeId').value = id;
        $('#changeStatusModal').modal('show');
    }
</script>

<style>
.hierarchy-view .hierarchy-indicator {
    font-weight: bold;
    color: #666;
}
</style>
@endsection

@endsection
