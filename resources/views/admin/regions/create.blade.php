@extends('layouts.admin.master')

@section('title', 'Create Region')

@section('content-header', __('Create Region'))

@section('breadcrumbs')
<li class="breadcrumb-item">
  <a href="{{route('admin.regions.index')}}">{{ __('Regions') }}</a>
</li>
<li class="breadcrumb-item active">
  {{ __('Create Region') }}
</li>
@endsection

@section('content')
<div class="card">
  <div class="card-header">
    <h5 class="m-0">{{ __('Create New Region') }}</h5>
  </div>
  <div class="card-body">
    <form action="{{ route('admin.regions.store') }}" method="POST">
      @csrf
      
      <div class="row mb-3">
        <div class="col-md-6">
          <label for="name" class="form-label">{{ __('Region Name') }} <span class="text-danger">*</span></label>
          <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
          @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
        
        <div class="col-md-6">
          <label for="code" class="form-label">{{ __('Region Code') }} <span class="text-danger">*</span></label>
          <input type="text" class="form-control @error('code') is-invalid @enderror" id="code" name="code" value="{{ old('code') }}" required>
          @error('code')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
      </div>
      
      <div class="row mb-3">
        <div class="col-12">
          <label for="description" class="form-label">{{ __('Description') }}</label>
          <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description') }}</textarea>
          @error('description')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
      </div>
      
      <div class="row">
        <div class="col-12">
          <button type="submit" class="btn btn-primary">{{ __('Create Region') }}</button>
          <a href="{{ route('admin.regions.index') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
        </div>
      </div>
    </form>
  </div>
</div>
@endsection
