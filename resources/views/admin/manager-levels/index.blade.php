@extends('layouts.admin.master')

@section('title', 'Manager Levels')

@section('content-header', __('Manager Levels'))

@section('breadcrumbs')
<li class="breadcrumb-item">
  <a href="{{route('admin.manager-levels.index')}}">{{ __('Manager Levels') }}</a>
</li>
<li class="breadcrumb-item active">
  {{ __('View Manager Levels') }}
</li>
@endsection

@section('content')
<div class="card">
<div class="card-header header-elements d-flex justify-content-between align-items-center">
  <h5 class="m-0 me-2">{{ __('Manager Levels')}}</h5>
  <form class="form d-flex align-items-center" method="GET" action="#">
      <a href="{{route('admin.manager-levels.create')}}" class="btn btn-primary" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="tooltip-primary" title="{{__('Add New')}}"><i class="fa-solid fa-plus"></i>{{__('Add New')}}</a>
    </div>
  </form>
<div class="table-responsive">
  <table class="table table-hover data-table">
    <thead class="border-top">
      <tr>
        <th>{{ __('Level') }}</th>
        <th>{{ __('Name') }}</th>
        <th>{{ __('Code') }}</th>
        <th>{{ __('Description') }}</th>
        <th>{{ __('Type') }}</th>
        <th>{{ __('Managers Count') }}</th>
        <th>{{ __('Status') }}</th>
        <th>{{ __('Actions') }}</th>
      </tr>
    </thead>
    <tbody class="table-border-bottom-0">
      @forelse ($levels as $level)
        <tr>
          <td><span class="badge bg-primary">{{ $level->hierarchy_level }}</span></td>
          <td><i class="fa fa-sitemap fa-lg text-primary me-3"></i> <strong>{{ $level->name }}</strong></td>
          <td><span class="badge bg-warning">{{ $level->code }}</span></td>
          <td>{{ Str::limit($level->description, 50) ?? 'N/A' }}</td>
          <td>
            @if($level->is_predefined)
              <span class="badge bg-label-success">Predefined</span>
            @else
              <span class="badge bg-label-info">Custom</span>
            @endif
          </td>
          <td>
            <span class="badge bg-label-secondary">{{ $level->managers()->count() }}</span>
          </td>
          <td>
            @if($level->status)
              <span class="badge bg-label-success">Active</span>
            @else
              <span class="badge bg-label-danger">Inactive</span>
            @endif
          </td>
          <td>
            <div class="dropdown">
              <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                <i class="bx bx-dots-vertical-rounded"></i>
              </button>
              <div class="dropdown-menu">
                <a class="dropdown-item" href="{{route('admin.manager-levels.show', $level->id)}}"><i class="bx bx-show me-1"></i> {{__('Show')}}</a>
                <a class="dropdown-item" href="{{route('admin.manager-levels.edit', $level->id)}}"><i class="bx bx-edit-alt me-1"></i> {{__('Edit')}}</a>
                
                <!-- Toggle Status -->
                <form action="{{route('admin.manager-levels.toggle-status', $level->id)}}" method="POST" style="display: inline;">
                  @csrf
                  @method('PUT')
                  <button type="submit" class="dropdown-item">
                    @if($level->status)
                      <i class="bx bx-toggle-right me-1"></i> {{__('Deactivate')}}
                    @else
                      <i class="bx bx-toggle-left me-1"></i> {{__('Activate')}}
                    @endif
                  </button>
                </form>

                @if(!$level->is_predefined && $level->managers()->count() == 0)
                  <!-- Delete only if not predefined and no managers -->
                  <form action="{{route('admin.manager-levels.destroy', $level->id)}}" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="dropdown-item text-danger" onclick="return confirm('Are you sure you want to delete this manager level?')">
                      <i class="bx bx-trash me-1"></i> {{__('Delete')}}
                    </button>
                  </form>
                @endif
              </div>
            </div>
          </td>
        </tr>
      @empty
        <tr>
          <td colspan="8" class="text-center">{{ __('No manager levels found') }}</td>
        </tr>
      @endforelse
    </tbody>
  </table>
</div>

@if($levels->hasPages())
  <div class="card-footer">
    {{ $levels->links() }}
  </div>
@endif

</div>
@endsection
