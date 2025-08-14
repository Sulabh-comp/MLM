@extends('layouts.admin.master')

@section('title', 'Create Manager Level')

@section('content-header', __('Create Manager Level'))

@section('breadcrumbs')
<li class="breadcrumb-item">
  <a href="{{route('admin.manager-levels.index')}}">{{ __('Manager Levels') }}</a>
</li>
<li class="breadcrumb-item active">
  {{ __('Create Manager Level') }}
</li>
@endsection

@section('content')
<div class="row">
  <div class="col-md-8 offset-md-2">
    <div class="card">
      <div class="card-header">
        <h5 class="mb-0">{{ __('Create New Manager Level') }}</h5>
      </div>
      <div class="card-body">
        <form action="{{ route('admin.manager-levels.store') }}" method="POST">
          @csrf
          
          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="name" class="form-label">{{ __('Level Name') }} <span class="text-danger">*</span></label>
              <input type="text" class="form-control @error('name') is-invalid @enderror" 
                     id="name" name="name" value="{{ old('name') }}" 
                     placeholder="e.g., Regional Manager">
              @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-md-6 mb-3">
              <label for="code" class="form-label">{{ __('Level Code') }} <span class="text-danger">*</span></label>
              <input type="text" class="form-control @error('code') is-invalid @enderror" 
                     id="code" name="code" value="{{ old('code') }}" 
                     placeholder="e.g., RM" maxlength="10">
              @error('code')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
              <small class="form-text text-muted">Short code for this level (max 10 characters)</small>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="hierarchy_level" class="form-label">{{ __('Hierarchy Level') }} <span class="text-danger">*</span></label>
              <input type="number" class="form-control @error('hierarchy_level') is-invalid @enderror" 
                     id="hierarchy_level" name="hierarchy_level" value="{{ old('hierarchy_level') }}" 
                     min="1" placeholder="e.g., 7">
              @error('hierarchy_level')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
              <small class="form-text text-muted">Lower numbers = higher hierarchy (1 = CEO level)</small>
            </div>

            <div class="col-md-6 mb-3">
              <div class="form-check mt-4">
                <input class="form-check-input" type="checkbox" id="status" name="status" 
                       {{ old('status') ? 'checked' : '' }}>
                <label class="form-check-label" for="status">
                  {{ __('Active') }}
                </label>
              </div>
            </div>
          </div>

          <div class="mb-3">
            <label for="description" class="form-label">{{ __('Description') }}</label>
            <textarea class="form-control @error('description') is-invalid @enderror" 
                      id="description" name="description" rows="3" 
                      placeholder="Describe the responsibilities and scope of this level">{{ old('description') }}</textarea>
            @error('description')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="card mt-4">
            <div class="card-body bg-light">
              <h6 class="card-title">{{ __('Current Hierarchy Levels') }}</h6>
              <div class="row">
                @php
                  $existingLevels = \App\Models\ManagerLevel::orderBy('hierarchy_level')->get();
                @endphp
                @foreach($existingLevels as $level)
                  <div class="col-md-6 mb-2">
                    <span class="badge bg-primary me-2">{{ $level->hierarchy_level }}</span>
                    <strong>{{ $level->name }}</strong> ({{ $level->code }})
                  </div>
                @endforeach
              </div>
            </div>
          </div>

          <div class="d-flex justify-content-between mt-4">
            <a href="{{ route('admin.manager-levels.index') }}" class="btn btn-secondary">
              <i class="bx bx-arrow-back me-1"></i> {{ __('Back') }}
            </a>
            <button type="submit" class="btn btn-primary">
              <i class="bx bx-save me-1"></i> {{ __('Create Level') }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
