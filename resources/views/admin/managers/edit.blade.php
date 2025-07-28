@extends('layouts.admin.master')

@section('title', 'Edit Manager')

@section('content-header', __('Edit Manager'))

@section('breadcrumbs')
<li class="breadcrumb-item">
  <a href="{{route('admin.managers.index')}}">{{ __('Managers') }}</a>
</li>
<li class="breadcrumb-item active">
  {{ __('Edit Manager') }}
</li>
@endsection

@section('content')
<div class="card">
  <div class="card-header">
    <h5 class="m-0">{{ __('Edit Manager') }}: {{ $manager->name }}</h5>
  </div>
  <div class="card-body">
    <form action="{{ route('admin.managers.update', $manager) }}" method="POST">
      @csrf
      @method('PUT')
      
      <div class="row mb-3">
        <div class="col-md-6">
          <label for="name" class="form-label">{{ __('Manager Name') }} <span class="text-danger">*</span></label>
          <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $manager->name) }}" required>
          @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
        
        <div class="col-md-6">
          <label for="email" class="form-label">{{ __('Email') }} <span class="text-danger">*</span></label>
          <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $manager->email) }}" required>
          @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
      </div>
      
      <div class="row mb-3">
        <div class="col-md-6">
          <label for="phone" class="form-label">{{ __('Phone') }} <span class="text-danger">*</span></label>
          <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone', $manager->phone) }}" required>
          @error('phone')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
        
        <div class="col-md-6">
          <label for="region_id" class="form-label">{{ __('Region') }} <span class="text-danger">*</span></label>
          <select class="form-select @error('region_id') is-invalid @enderror" id="region_id" name="region_id" required>
            <option value="">{{ __('Select Region') }}</option>
            @foreach($regions as $region)
              <option value="{{ $region->id }}" {{ old('region_id', $manager->region_id) == $region->id ? 'selected' : '' }}>
                {{ $region->name }} ({{ $region->code }})
              </option>
            @endforeach
          </select>
          @error('region_id')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
      </div>
      
      <div class="row">
        <div class="col-12">
          <button type="submit" class="btn btn-primary">{{ __('Update Manager') }}</button>
          <a href="{{ route('admin.managers.index') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
        </div>
      </div>
    </form>
  </div>
</div>
@endsection
