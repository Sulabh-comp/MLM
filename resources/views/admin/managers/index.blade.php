@extends('layouts.admin.master')

@section('title', 'Managers')

@section('content-header', __('Managers'))

@section('breadcrumbs')
<li class="breadcrumb-item">
  <a href="{{route('admin.managers.index')}}">{{ __('Managers') }}</a>
</li>
<li class="breadcrumb-item active">
  {{ __('View Managers') }}
</li>
@endsection

@section('content')
<!-- Hierarchy Tree View -->
<div class="card mb-4">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h5 class="mb-0">{{ __('Hierarchy Tree View') }}</h5>
    <button class="btn btn-outline-primary btn-sm" type="button" data-bs-toggle="collapse" data-bs-target="#hierarchyTree">
      <i class="fa-solid fa-tree"></i> {{ __('Toggle Tree') }}
    </button>
  </div>
  <div class="collapse" id="hierarchyTree">
    <div class="card-body">
      <div id="hierarchy-tree-container">
        <button class="btn btn-sm btn-primary mb-3" onclick="loadHierarchyTree()">
          <i class="fa-solid fa-refresh"></i> {{ __('Load Hierarchy Tree') }}
        </button>
        <div id="hierarchy-tree-content">
          <!-- Tree content will be loaded here -->
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Managers Table -->
<div class="card">
<div class="card-header header-elements d-flex justify-content-between align-items-center">
  <h5 class="m-0 me-2">{{ __('Managers')}}</h5>
  <div class="d-flex gap-2">
    <a href="{{ route('admin.managers.hierarchy') }}" class="btn btn-outline-info btn-sm">
      <i class="fa-solid fa-sitemap"></i> {{ __('Hierarchy View') }}
    </a>
    <select class="form-select form-select-sm" id="levelFilter" onchange="filterByLevel()">
      <option value="">{{ __('All Levels') }}</option>
      @foreach(\App\Models\ManagerLevel::active()->orderBy('hierarchy_level')->get() as $level)
        <option value="{{ $level->name }}">{{ $level->name }} (Level {{ $level->hierarchy_level }})</option>
      @endforeach
    </select>
    <a href="{{route('admin.managers.create')}}" class="btn btn-primary" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="tooltip-primary" title="{{__('Add New')}}"><i class="fa-solid fa-plus"></i>{{__('Add New')}}</a>
  </div>
</div>
<div class="table-responsive">
  <table class="table table-hover data-table">
    <thead class="border-top">
      <tr>
        <th>{{ __('Code') }}</th>
        <th>{{ __('Name') }}</th>
        <th>{{ __('Email') }}</th>
        <th>{{ __('Phone') }}</th>
        <th>{{ __('Level') }}</th>
        <th>{{ __('Parent') }}</th>
        <th>{{ __('Descendants') }}</th>
        <th>{{ __('Status') }}</th>
        <th>{{ __('Actions') }}</th>
      </tr>
    </thead>
    <tbody class="table-border-bottom-0">
      @forelse ($managers as $manager)
        <tr>
          <td><span class="badge bg-warning">{{ $manager->code ?? 'N/A' }}</span></td>
          <td>
            <i class="fab fa-angular fa-lg text-danger me-3"></i> 
            {{ str_repeat('—', $manager->depth ?? 0) }}
            <strong>{{ $manager->name }}</strong>
            @if($manager->designation)
              <br><small class="text-muted">{{ $manager->designation }}</small>
            @endif
          </td>
          <td>{{ $manager->email }}</td>
          <td>{{ $manager->phone }}</td>
          <td>
            @if($manager->level_name)
              <span class="badge bg-label-primary">{{ $manager->level_name }}</span>
              @if($manager->managerLevel)
                <br><small class="text-muted">Level {{ $manager->managerLevel->hierarchy_level }}</small>
              @endif
            @else
              <span class="badge bg-label-secondary">Not Set</span>
            @endif
          </td>
          <td>
            @if($manager->parent)
              <strong>{{ $manager->parent->name }}</strong>
              @if($manager->parent->level_name)
                <br><small class="text-muted">{{ $manager->parent->level_name }}</small>
              @endif
            @else
              <span class="text-muted">Top Level</span>
            @endif
          </td>
          <td>
            <span class="badge bg-label-info">{{ $manager->children->count() }} Direct</span>
            @if($manager->allDescendants && $manager->allDescendants->count() > $manager->children->count())
              <br><small class="text-muted">{{ $manager->allDescendants->count() }} Total</small>
            @endif
          </td>
          <td>
            @if($manager->status)
              <span class="badge bg-label-success me-1">Active</span>
            @else
              <span class="badge bg-label-danger me-1">Inactive</span>
            @endif
          </td>
          <td>
            <a href="{{ route('admin.managers.show', $manager) }}" class="btn btn-info btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="tooltip-info" title="{{__('View')}}"><i class="fa-solid fa-eye"></i></a>
            <a href="{{ route('admin.managers.edit', $manager) }}" class="btn btn-primary btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="tooltip-primary" title="{{__('Edit')}}"><i class="fa-solid fa-pen-to-square"></i></a>
            <a href="javascript:void(0)" class="btn btn-warning btn-sm" onclick="changeStatus({{ $manager->id }})" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="tooltip-warning" title="{{__('Change Status')}}"><i class="fa-solid fa-power-off"></i></a>
            <a href="javascript:void(0)" class="btn btn-danger btn-sm" onclick="deleteManager({{ $manager->id }})" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="tooltip-danger" title="{{__('Delete')}}"><i class="fa-solid fa-trash"></i></a>
          </td>
        </tr>
      @empty
        <tr>
          <td colspan="9" class="text-center">{{ __('No managers found') }}</td>
        </tr>
      @endforelse
    </tbody>
  </table>
  <div class="d-flex justify-content-center mt-3">
    {{ $managers->appends(request()->query())->render() }}
  </div>
</div>

<!-- Status Change Modal -->
<div class="modal fade" id="changeStatusModal" tabindex="-1" aria-labelledby="changeStatusModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="changeStatusForm" method="POST" action="{{ route('admin.managers.updateStatus') }}">
        @csrf
        @method('PUT')
        <div class="modal-header">
          <h5 class="modal-title" id="changeStatusModalLabel">{{__('Change Status')}}</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="id" id="managerId">
          <p>{{__('Are you sure you want to change the status of this manager?')}}</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{__('Close')}}</button>
          <button type="submit" class="btn btn-primary">{{__('Change Status')}}</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="deleteForm" method="POST" action="">
        @csrf
        @method('DELETE')
        <div class="modal-header">
          <h5 class="modal-title" id="deleteModalLabel">{{__('Delete Manager')}}</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <p>{{__('Are you sure you want to delete this manager? This action cannot be undone.')}}</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{__('Close')}}</button>
          <button type="submit" class="btn btn-danger">{{__('Delete')}}</button>
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

  function deleteManager(id) {
    document.getElementById('deleteForm').action = `managers/${id}`;
    $('#deleteModal').modal('show');
  }

  function loadHierarchyTree() {
    const container = document.getElementById('hierarchy-tree-content');
    container.innerHTML = '<div class="text-center"><i class="fa-solid fa-spinner fa-spin"></i> Loading...</div>';
    
    fetch('{{ route("admin.managers.hierarchy-tree") }}')
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          container.innerHTML = buildTreeHTML(data.tree);
        } else {
          container.innerHTML = '<div class="alert alert-danger">Failed to load hierarchy tree</div>';
        }
      })
      .catch(error => {
        console.error('Error:', error);
        container.innerHTML = '<div class="alert alert-danger">Error loading hierarchy tree</div>';
      });
  }

  function buildTreeHTML(nodes, level = 0) {
    if (!nodes || nodes.length === 0) return '';
    
    let html = '<ul class="list-unstyled' + (level === 0 ? ' ms-3' : '') + '">';
    
    nodes.forEach(node => {
      html += '<li class="mb-2">';
      html += '<div class="d-flex align-items-center">';
      html += '<i class="fa-solid fa-user me-2 text-primary"></i>';
      html += '<strong>' + node.name + '</strong>';
      
      if (node.level_name) {
        html += ' <span class="badge bg-label-primary ms-2">' + node.level_name + '</span>';
      }
      
      if (node.children && node.children.length > 0) {
        html += ' <span class="badge bg-label-info ms-2">' + node.children.length + ' direct</span>';
      }
      
      html += '</div>';
      
      if (node.children && node.children.length > 0) {
        html += buildTreeHTML(node.children, level + 1);
      }
      
      html += '</li>';
    });
    
    html += '</ul>';
    return html;
  }

  function filterByLevel() {
    const levelFilter = document.getElementById('levelFilter').value;
    const rows = document.querySelectorAll('tbody tr');
    
    rows.forEach(row => {
      if (!levelFilter) {
        row.style.display = '';
        return;
      }
      
      const levelBadge = row.querySelector('td:nth-child(5) .badge');
      if (levelBadge && levelBadge.textContent.includes(levelFilter)) {
        row.style.display = '';
      } else {
        row.style.display = 'none';
      }
    });
  }
</script>
@endsection

@endsection
