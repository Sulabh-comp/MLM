@extends('layouts.manager.master')

@section('title', __('Manager Details'))

@section('content-header', __('Manager Details'))

@section('breadcrumbs')
<li class="breadcrumb-item">
    <a href="{{route('manager.dashboard')}}">{{ __('Dashboard') }}</a>
</li>
<li class="breadcrumb-item">
    <a href="{{route('manager.managers.index')}}">{{ __('Team Management') }}</a>
</li>
<li class="breadcrumb-item active">
    {{ $manager->name }}
</li>
@endsection

@section('content')
<!-- Manager Profile -->
<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-body text-center">
                <div class="avatar avatar-xl mx-auto mb-3">
                    <span class="avatar-initial rounded-circle bg-label-primary">
                        {{ substr($manager->name, 0, 2) }}
                    </span>
                </div>
                <h5 class="mb-1">{{ $manager->name }}</h5>
                <p class="text-muted mb-3">{{ $manager->designation }}</p>
                
                <div class="mb-3">
                    <span class="badge bg-label-primary fs-6">{{ $manager->level_name ?? 'No Level Set' }}</span>
                </div>
                
                <div class="mb-3">
                    <span class="badge {{ $manager->status ? 'bg-label-success' : 'bg-label-danger' }} fs-6">
                        {{ $manager->status ? 'Active' : 'Inactive' }}
                    </span>
                </div>

                <div class="d-flex justify-content-center gap-2">
                    <a href="{{ route('manager.managers.edit', $manager) }}" class="btn btn-primary">
                        <i class="ti ti-edit"></i> Edit
                    </a>
                    <button class="btn btn-outline-secondary" onclick="changeStatus({{ $manager->id }})">
                        <i class="ti ti-power"></i> {{ $manager->status ? 'Deactivate' : 'Activate' }}
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Contact Information -->
        <div class="card mt-4">
            <div class="card-header">
                <h6 class="mb-0">Contact Information</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <small class="text-muted d-block">Email</small>
                    <span>{{ $manager->email }}</span>
                </div>
                <div class="mb-3">
                    <small class="text-muted d-block">Phone</small>
                    <span>{{ $manager->phone }}</span>
                </div>
                <div class="mb-0">
                    <small class="text-muted d-block">Joined</small>
                    <span>{{ $manager->created_at->format('M d, Y') }}</span>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <!-- Performance Statistics -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0">Performance Overview</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-2">
                        <div class="text-center">
                            <h4 class="mb-0 text-primary">{{ $stats['direct_subordinates'] }}</h4>
                            <small class="text-muted">Direct Team</small>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="text-center">
                            <h4 class="mb-0 text-success">{{ $stats['total_subordinates'] }}</h4>
                            <small class="text-muted">Total Team</small>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="text-center">
                            <h4 class="mb-0 text-info">{{ $stats['employees_count'] }}</h4>
                            <small class="text-muted">Employees</small>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="text-center">
                            <h4 class="mb-0 text-warning">{{ $stats['agencies_count'] }}</h4>
                            <small class="text-muted">Agencies</small>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="text-center">
                            <h4 class="mb-0 text-secondary">{{ $stats['customers_count'] }}</h4>
                            <small class="text-muted">Customers</small>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="text-center">
                            @php
                                $activeRate = $stats['employees_count'] > 0 ? ($stats['active_employees'] / $stats['employees_count']) * 100 : 0;
                            @endphp
                            <h4 class="mb-0 text-{{ $activeRate >= 80 ? 'success' : ($activeRate >= 60 ? 'warning' : 'danger') }}">
                                {{ number_format($activeRate, 0) }}%
                            </h4>
                            <small class="text-muted">Active Rate</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Hierarchy Information -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0">Hierarchy Information</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <small class="text-muted d-block">Current Level</small>
                            <span class="badge bg-label-primary">{{ $manager->level_name ?? 'Not Set' }}</span>
                            @if($manager->managerLevel)
                                <span class="text-muted">(Level {{ $manager->managerLevel->hierarchy_level }})</span>
                            @endif
                        </div>
                        <div class="mb-3">
                            <small class="text-muted d-block">Reports To</small>
                            @if($manager->parent)
                                <a href="{{ route('manager.managers.show', $manager->parent) }}" class="text-decoration-none">
                                    {{ $manager->parent->name }}
                                    @if($manager->parent->level_name)
                                        <span class="badge bg-label-secondary">{{ $manager->parent->level_name }}</span>
                                    @endif
                                </a>
                            @else
                                <span class="text-muted">Top Level</span>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <small class="text-muted d-block">Hierarchy Depth</small>
                            <span>{{ $manager->depth ?? 0 }}</span>
                        </div>
                        <div class="mb-3">
                            <small class="text-muted d-block">Hierarchy Path</small>
                            <span class="text-muted">{{ $manager->hierarchy_path ? str_replace(',', ' → ', $manager->hierarchy_path) : 'No path' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Direct Subordinates -->
        @if($manager->children && $manager->children->count() > 0)
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Direct Subordinates ({{ $manager->children->count() }})</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($manager->children as $subordinate)
                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-center p-3 border rounded">
                                <div class="avatar avatar-sm me-3">
                                    <span class="avatar-initial rounded-circle bg-label-secondary">
                                        {{ substr($subordinate->name, 0, 1) }}
                                    </span>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-0">{{ $subordinate->name }}</h6>
                                    <small class="text-muted">{{ $subordinate->level_name ?? 'No Level' }}</small>
                                </div>
                                <div>
                                    <a href="{{ route('manager.managers.show', $subordinate) }}" class="btn btn-sm btn-outline-primary">
                                        View
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Recent Activity (Future Enhancement) -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Recent Activity</h6>
            </div>
            <div class="card-body">
                <div class="text-center py-4">
                    <div class="avatar avatar-lg mx-auto mb-3">
                        <span class="avatar-initial rounded-circle bg-label-secondary">
                            <i class="ti ti-activity"></i>
                        </span>
                    </div>
                    <h6 class="mb-1">Activity Tracking</h6>
                    <p class="text-muted">Activity tracking feature will be available in future updates.</p>
                </div>
            </div>
        </div>
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
                    <p>Are you sure you want to {{ $manager->status ? 'deactivate' : 'activate' }} <strong>{{ $manager->name }}</strong>?</p>
                    @if($manager->status && $manager->children->count() > 0)
                        <div class="alert alert-warning">
                            <strong>Warning:</strong> This manager has {{ $manager->children->count() }} direct subordinate(s). 
                            Deactivating may affect their access to team management features.
                        </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">{{ $manager->status ? 'Deactivate' : 'Activate' }}</button>
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
</script>
@endsection

@endsection
