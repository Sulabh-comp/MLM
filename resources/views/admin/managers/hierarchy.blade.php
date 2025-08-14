@extends('layouts.admin.master')

@section('title', 'Manager Hierarchy')

@section('content-header', __('Manager Hierarchy'))

@section('breadcrumbs')
<li class="breadcrumb-item">
    <a href="{{route('admin.managers.index')}}">{{ __('Managers') }}</a>
</li>
<li class="breadcrumb-item active">
    {{ __('Hierarchy') }}
</li>
@endsection

@section('content')
<!-- Hierarchy Management Controls -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">{{ __('Quick Actions') }}</h5>
            </div>
            <div class="card-body">
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('admin.managers.create') }}" class="btn btn-primary">
                        <i class="fa-solid fa-plus"></i> {{ __('Add Manager') }}
                    </a>
                    <button class="btn btn-outline-info" onclick="expandAll()">
                        <i class="fa-solid fa-expand"></i> {{ __('Expand All') }}
                    </button>
                    <button class="btn btn-outline-warning" onclick="collapseAll()">
                        <i class="fa-solid fa-compress"></i> {{ __('Collapse All') }}
                    </button>
                    <button class="btn btn-outline-success" onclick="refreshHierarchy()">
                        <i class="fa-solid fa-refresh"></i> {{ __('Refresh') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">{{ __('Hierarchy Statistics') }}</h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-4">
                        <div class="h4 mb-0 text-primary">{{ $stats['total_managers'] ?? 0 }}</div>
                        <small class="text-muted">{{ __('Total Managers') }}</small>
                    </div>
                    <div class="col-4">
                        <div class="h4 mb-0 text-success">{{ $stats['active_levels'] ?? 0 }}</div>
                        <small class="text-muted">{{ __('Active Levels') }}</small>
                    </div>
                    <div class="col-4">
                        <div class="h4 mb-0 text-info">{{ $stats['max_depth'] ?? 0 }}</div>
                        <small class="text-muted">{{ __('Max Depth') }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Level Filter -->
<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0">{{ __('Filter by Level') }}</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <select class="form-select" id="levelFilter" onchange="filterByLevel()">
                    <option value="">{{ __('Show All Levels') }}</option>
                    @foreach($levels as $level)
                        <option value="{{ $level->name }}">
                            {{ $level->name }} (Level {{ $level->hierarchy_level }}) - {{ $level->managers_count ?? 0 }} managers
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-text"><i class="fa-solid fa-search"></i></span>
                    <input type="text" class="form-control" id="searchManager" placeholder="{{ __('Search managers...') }}" oninput="searchManagers()">
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Hierarchy Tree Display -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">{{ __('Hierarchy Structure') }}</h5>
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" id="compactView" onchange="toggleCompactView()">
            <label class="form-check-label" for="compactView">{{ __('Compact View') }}</label>
        </div>
    </div>
    <div class="card-body" id="hierarchy-container">
        @if($hierarchy && $hierarchy->count() > 0)
            <div class="hierarchy-tree-display">
                @foreach($hierarchy as $topManager)
                    @include('admin.partials.hierarchy-node', ['manager' => $topManager, 'level' => 0])
                @endforeach
            </div>
        @else
            <div class="text-center py-5">
                <i class="fa-solid fa-sitemap fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">{{ __('No hierarchy structure found') }}</h5>
                <p class="text-muted">{{ __('Start by creating your first manager') }}</p>
                <a href="{{ route('admin.managers.create') }}" class="btn btn-primary">
                    <i class="fa-solid fa-plus"></i> {{ __('Create First Manager') }}
                </a>
            </div>
        @endif
    </div>
</div>

<!-- Bulk Operations Modal -->
<div class="modal fade" id="bulkActionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="bulkActionForm" method="POST" action="{{ route('admin.managers.bulk-action') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('Bulk Actions') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">{{ __('Select Action') }}</label>
                        <select class="form-select" name="action" required>
                            <option value="">{{ __('Choose action...') }}</option>
                            <option value="activate">{{ __('Activate Selected') }}</option>
                            <option value="deactivate">{{ __('Deactivate Selected') }}</option>
                            <option value="change_level">{{ __('Change Level') }}</option>
                            <option value="export">{{ __('Export Data') }}</option>
                        </select>
                    </div>
                    
                    <div class="mb-3" id="newLevelSection" style="display: none;">
                        <label class="form-label">{{ __('New Level') }}</label>
                        <select class="form-select" name="new_level">
                            <option value="">{{ __('Select level...') }}</option>
                            @foreach($levels as $level)
                                <option value="{{ $level->name }}">{{ $level->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div id="selectedManagers">
                        <!-- Selected manager checkboxes will be added here by JavaScript -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('Execute Action') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

@section('scripts')
<script>
    function expandAll() {
        document.querySelectorAll('.hierarchy-node').forEach(node => {
            node.style.display = 'block';
        });
    }

    function collapseAll() {
        document.querySelectorAll('.hierarchy-node.level-1, .hierarchy-node.level-2, .hierarchy-node.level-3, .hierarchy-node.level-4, .hierarchy-node.level-5').forEach(node => {
            node.style.display = 'none';
        });
    }

    function refreshHierarchy() {
        location.reload();
    }

    function filterByLevel() {
        const selectedLevel = document.getElementById('levelFilter').value;
        const nodes = document.querySelectorAll('.hierarchy-node');
        
        nodes.forEach(node => {
            if (!selectedLevel) {
                node.style.display = 'block';
                return;
            }
            
            const levelBadge = node.querySelector('.level-badge');
            if (levelBadge && levelBadge.textContent.includes(selectedLevel)) {
                node.style.display = 'block';
                // Show parent nodes too
                let parent = node.parentElement;
                while (parent && parent.classList.contains('hierarchy-node')) {
                    parent.style.display = 'block';
                    parent = parent.parentElement;
                }
            } else {
                node.style.display = 'none';
            }
        });
    }

    function searchManagers() {
        const searchTerm = document.getElementById('searchManager').value.toLowerCase();
        const nodes = document.querySelectorAll('.hierarchy-node');
        
        nodes.forEach(node => {
            const name = node.querySelector('.node-details strong').textContent.toLowerCase();
            const email = node.querySelector('.node-details .text-muted').textContent.toLowerCase();
            
            if (!searchTerm || name.includes(searchTerm) || email.includes(searchTerm)) {
                node.style.display = 'block';
            } else {
                node.style.display = 'none';
            }
        });
    }

    function toggleCompactView() {
        const container = document.getElementById('hierarchy-container');
        const isCompact = document.getElementById('compactView').checked;
        
        if (isCompact) {
            container.classList.add('compact-view');
        } else {
            container.classList.remove('compact-view');
        }
    }

    // Bulk action functionality
    document.querySelector('select[name="action"]').addEventListener('change', function() {
        const newLevelSection = document.getElementById('newLevelSection');
        if (this.value === 'change_level') {
            newLevelSection.style.display = 'block';
        } else {
            newLevelSection.style.display = 'none';
        }
    });
</script>

<style>
.compact-view .hierarchy-node {
    padding: 4px 8px;
    margin: 2px 0;
}

.compact-view .node-details small {
    display: none;
}

.compact-view .level-badge {
    font-size: 9px;
    padding: 1px 4px;
}
</style>
@endsection

@endsection
