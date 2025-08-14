@extends('layouts.manager.master')

@section('title', 'Team Management')

@section('content-header', __('Team Management'))

@section('breadcrumbs')
<li class="breadcrumb-item">
    <a href="{{route('manager.dashboard')}}">{{ __('Dashboard') }}</a>
</li>
<li class="breadcrumb-item active">
    {{ __('Team Management') }}
</li>
@endsection

@section('content')
<!-- Team Statistics -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card">
            <div class="card-body text-center">
                <div class="avatar avatar-md mx-auto mb-3">
                    <span class="avatar-initial rounded-circle bg-label-primary">
                        <i class="ti ti-users ti-md"></i>
                    </span>
                </div>
                <h4 class="mb-0">{{ $stats['direct_subordinates'] }}</h4>
                <span class="text-muted">Direct Subordinates</span>
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
                <h4 class="mb-0">{{ $stats['total_subordinates'] }}</h4>
                <span class="text-muted">Total Team</span>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body text-center">
                <div class="avatar avatar-md mx-auto mb-3">
                    <span class="avatar-initial rounded-circle bg-label-info">
                        <i class="ti ti-layers-intersect ti-md"></i>
                    </span>
                </div>
                <h4 class="mb-0">{{ $stats['max_depth'] }}</h4>
                <span class="text-muted">Max Depth</span>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body text-center">
                <div class="avatar avatar-md mx-auto mb-3">
                    <span class="avatar-initial rounded-circle bg-label-warning">
                        <i class="ti ti-hierarchy ti-md"></i>
                    </span>
                </div>
                <h4 class="mb-0">{{ count($stats['by_level']) }}</h4>
                <span class="text-muted">Active Levels</span>
            </div>
        </div>
    </div>
</div>

<!-- Direct Subordinates -->
@if($directSubordinates->count() > 0)
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Direct Subordinates</h5>
        <a href="{{ route('manager.managers.create') }}" class="btn btn-primary">
            <i class="ti ti-plus"></i> Add Manager
        </a>
    </div>
    <div class="card-body">
        <div class="row">
            @foreach($directSubordinates as $subordinate)
                <div class="col-md-6 col-lg-4 mb-3">
                    <div class="card border">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="avatar avatar-md me-3">
                                    <span class="avatar-initial rounded-circle bg-label-primary">
                                        {{ substr($subordinate->name, 0, 1) }}
                                    </span>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-0">{{ $subordinate->name }}</h6>
                                    <small class="text-muted">{{ $subordinate->email }}</small>
                                </div>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="dropdown">
                                        <i class="ti ti-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="{{ route('manager.managers.show', $subordinate) }}">View</a></li>
                                        <li><a class="dropdown-item" href="{{ route('manager.managers.edit', $subordinate) }}">Edit</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item text-danger" href="javascript:void(0)" onclick="changeStatus({{ $subordinate->id }})">Change Status</a></li>
                                    </ul>
                                </div>
                            </div>
                            
                            <div class="mb-2">
                                <span class="badge bg-label-primary">{{ $subordinate->level_name ?? 'No Level' }}</span>
                                <span class="badge {{ $subordinate->status ? 'bg-label-success' : 'bg-label-danger' }}">
                                    {{ $subordinate->status ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                            
                            <div class="row text-center">
                                <div class="col-4">
                                    <div class="d-flex flex-column">
                                        <strong>{{ $subordinate->children()->count() }}</strong>
                                        <small class="text-muted">Team</small>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="d-flex flex-column">
                                        <strong>{{ $subordinate->employees()->count() }}</strong>
                                        <small class="text-muted">Employees</small>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="d-flex flex-column">
                                        <strong>{{ $subordinate->agencies()->count() }}</strong>
                                        <small class="text-muted">Agencies</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endif

<!-- Full Team Hierarchy -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Complete Team Hierarchy</h5>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-info btn-sm" onclick="loadHierarchyTree()">
                <i class="ti ti-refresh"></i> Refresh Tree
            </button>
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" id="compactView" onchange="toggleCompactView()">
                <label class="form-check-label" for="compactView">Compact</label>
            </div>
        </div>
    </div>
    <div class="card-body">
        @if($subordinates->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Manager</th>
                            <th>Level</th>
                            <th>Direct Team</th>
                            <th>Employees</th>
                            <th>Agencies</th>
                            <th>Customers</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($subordinates as $subordinate)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="hierarchy-indicator me-2">
                                            {{ str_repeat('—', ($subordinate->depth ?? 0) - ($currentManager->depth ?? 0)) }}
                                        </div>
                                        <div class="avatar avatar-sm me-3">
                                            <span class="avatar-initial rounded-circle bg-label-primary">
                                                {{ substr($subordinate->name, 0, 1) }}
                                            </span>
                                        </div>
                                        <div>
                                            <h6 class="mb-0">{{ $subordinate->name }}</h6>
                                            <small class="text-muted">{{ $subordinate->email }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-label-primary">{{ $subordinate->level_name ?? 'Not Set' }}</span>
                                </td>
                                <td class="text-center">{{ $subordinate->children()->count() }}</td>
                                <td class="text-center">{{ $subordinate->employees()->count() }}</td>
                                <td class="text-center">{{ $subordinate->agencies()->count() }}</td>
                                <td class="text-center">{{ $subordinate->customers()->count() }}</td>
                                <td>
                                    <span class="badge {{ $subordinate->status ? 'bg-label-success' : 'bg-label-danger' }}">
                                        {{ $subordinate->status ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="dropdown">
                                            <i class="ti ti-dots-vertical"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="{{ route('manager.managers.show', $subordinate) }}">View Details</a></li>
                                            <li><a class="dropdown-item" href="{{ route('manager.managers.edit', $subordinate) }}">Edit</a></li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li><a class="dropdown-item" href="javascript:void(0)" onclick="changeStatus({{ $subordinate->id }})">Change Status</a></li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-5">
                <div class="avatar avatar-xl mx-auto mb-3">
                    <span class="avatar-initial rounded-circle bg-label-secondary">
                        <i class="ti ti-users ti-lg"></i>
                    </span>
                </div>
                <h5 class="mb-1">No subordinates found</h5>
                <p class="text-muted">You don't have any managers in your team yet.</p>
                <a href="{{ route('manager.managers.create') }}" class="btn btn-primary">
                    <i class="ti ti-plus"></i> Add First Manager
                </a>
            </div>
        @endif
    </div>
</div>

<!-- Status Change Modal -->
<div class="modal fade" id="changeStatusModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="changeStatusForm" method="POST" action="{{ route('manager.managers.updateStatus') }}">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Change Manager Status</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="managerId">
                    <p>Are you sure you want to change the status of this manager?</p>
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
    function changeStatus(id) {
        document.getElementById('managerId').value = id;
        $('#changeStatusModal').modal('show');
    }

    function loadHierarchyTree() {
        // Refresh the page for now, can be enhanced with AJAX
        location.reload();
    }

    function toggleCompactView() {
        const table = document.querySelector('.table');
        const isCompact = document.getElementById('compactView').checked;
        
        if (isCompact) {
            table.classList.add('table-sm');
        } else {
            table.classList.remove('table-sm');
        }
    }
</script>
@endsection

@endsection
