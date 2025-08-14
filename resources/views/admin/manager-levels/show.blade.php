@extends('layouts.admin.master')

@section('title', 'Manager Level Details')

@section('content-header', __('Manager Level Details'))

@section('breadcrumbs')
<li class="breadcrumb-item">
  <a href="{{route('admin.manager-levels.index')}}">{{ __('Manager Levels') }}</a>
</li>
<li class="breadcrumb-item active">
  {{ __('Manager Level Details') }}
</li>
@endsection

@section('content')
<div class="row">
  <div class="col-md-8 offset-md-2">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">{{ $managerLevel->name }}</h5>
        <div>
          @if($managerLevel->is_predefined)
            <span class="badge bg-success me-2">Predefined</span>
          @else
            <span class="badge bg-info me-2">Custom</span>
          @endif
          @if($managerLevel->status)
            <span class="badge bg-success">Active</span>
          @else
            <span class="badge bg-danger">Inactive</span>
          @endif
        </div>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-6 mb-3">
            <label class="form-label fw-bold">{{ __('Level Name') }}</label>
            <p class="mb-0">{{ $managerLevel->name }}</p>
          </div>
          <div class="col-md-6 mb-3">
            <label class="form-label fw-bold">{{ __('Level Code') }}</label>
            <p class="mb-0"><span class="badge bg-warning">{{ $managerLevel->code }}</span></p>
          </div>
        </div>

        <div class="row">
          <div class="col-md-6 mb-3">
            <label class="form-label fw-bold">{{ __('Hierarchy Level') }}</label>
            <p class="mb-0"><span class="badge bg-primary">{{ $managerLevel->hierarchy_level }}</span></p>
          </div>
          <div class="col-md-6 mb-3">
            <label class="form-label fw-bold">{{ __('Type') }}</label>
            <p class="mb-0">
              @if($managerLevel->is_predefined)
                <span class="badge bg-success">Predefined</span>
              @else
                <span class="badge bg-info">Custom</span>
              @endif
            </p>
          </div>
        </div>

        <div class="mb-3">
          <label class="form-label fw-bold">{{ __('Description') }}</label>
          <p class="mb-0">{{ $managerLevel->description ?? 'No description provided' }}</p>
        </div>

        <div class="row">
          <div class="col-md-6 mb-3">
            <label class="form-label fw-bold">{{ __('Managers Count') }}</label>
            <p class="mb-0">
              <span class="badge bg-secondary">{{ $managerLevel->managers()->count() }}</span>
              {{ __('manager(s) using this level') }}
            </p>
          </div>
          <div class="col-md-6 mb-3">
            <label class="form-label fw-bold">{{ __('Created At') }}</label>
            <p class="mb-0">{{ $managerLevel->created_at->format('d M Y, h:i A') }}</p>
          </div>
        </div>

        @if($managerLevel->managers()->count() > 0)
          <div class="card mt-4">
            <div class="card-header">
              <h6 class="mb-0">{{ __('Managers with this Level') }}</h6>
            </div>
            <div class="table-responsive">
              <table class="table table-sm">
                <thead>
                  <tr>
                    <th>{{ __('Name') }}</th>
                    <th>{{ __('Email') }}</th>
                    <th>{{ __('Phone') }}</th>
                    <th>{{ __('Status') }}</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($managerLevel->managers as $manager)
                    <tr>
                      <td>{{ $manager->name }}</td>
                      <td>{{ $manager->email }}</td>
                      <td>{{ $manager->phone ?? 'N/A' }}</td>
                      <td>
                        @if($manager->status)
                          <span class="badge bg-success">Active</span>
                        @else
                          <span class="badge bg-danger">Inactive</span>
                        @endif
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>
        @endif

        <div class="d-flex justify-content-between mt-4">
          <a href="{{ route('admin.manager-levels.index') }}" class="btn btn-secondary">
            <i class="bx bx-arrow-back me-1"></i> {{ __('Back') }}
          </a>
          <div>
            <a href="{{ route('admin.manager-levels.edit', $managerLevel) }}" class="btn btn-primary me-2">
              <i class="bx bx-edit me-1"></i> {{ __('Edit') }}
            </a>
            @if(!$managerLevel->is_predefined && $managerLevel->managers()->count() == 0)
              <form action="{{ route('admin.manager-levels.destroy', $managerLevel) }}" method="POST" style="display: inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this manager level?')">
                  <i class="bx bx-trash me-1"></i> {{ __('Delete') }}
                </button>
              </form>
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
