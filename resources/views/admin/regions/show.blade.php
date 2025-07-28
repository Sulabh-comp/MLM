@extends('layouts.admin.master')

@section('title', 'View Region')

@section('content-header', __('View Region'))

@section('breadcrumbs')
<li class="breadcrumb-item">
  <a href="{{route('admin.regions.index')}}">{{ __('Regions') }}</a>
</li>
<li class="breadcrumb-item active">
  {{ __('View Region') }}
</li>
@endsection

@section('content')
<div class="row">
  <!-- Region Details -->
  <div class="col-md-6">
    <div class="card">
      <div class="card-header">
        <h5 class="m-0">{{ __('Region Details') }}</h5>
      </div>
      <div class="card-body">
        <table class="table table-borderless">
          <tr>
            <th width="40%">{{ __('Name') }}:</th>
            <td>{{ $region->name }}</td>
          </tr>
          <tr>
            <th>{{ __('Code') }}:</th>
            <td>{{ $region->code }}</td>
          </tr>
          <tr>
            <th>{{ __('Description') }}:</th>
            <td>{{ $region->description ?? 'N/A' }}</td>
          </tr>
          <tr>
            <th>{{ __('Status') }}:</th>
            <td>
              <span class="badge bg-label-{{$region->status ? 'success' : 'danger'}}">
                {{$region->status ? 'Active' : 'Inactive'}}
              </span>
            </td>
          </tr>
          <tr>
            <th>{{ __('Created') }}:</th>
            <td>{{ $region->created_at->format('d M Y, h:i A') }}</td>
          </tr>
        </table>
      </div>
    </div>
  </div>
  
  <!-- Statistics -->
  <div class="col-md-6">
    <div class="card">
      <div class="card-header">
        <h5 class="m-0">{{ __('Statistics') }}</h5>
      </div>
      <div class="card-body">
        <div class="row text-center">
          <div class="col-4">
            <div class="border rounded p-3">
              <h4 class="text-primary">{{ $region->managers->count() }}</h4>
              <small>{{ __('Managers') }}</small>
            </div>
          </div>
          <div class="col-4">
            <div class="border rounded p-3">
              <h4 class="text-info">{{ $region->employees->count() }}</h4>
              <small>{{ __('Employees') }}</small>
            </div>
          </div>
          <div class="col-4">
            <div class="border rounded p-3">
              <h4 class="text-success">{{ $region->agencies->count() }}</h4>
              <small>{{ __('Agencies') }}</small>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Managers List -->
@if($region->managers->count() > 0)
<div class="card mt-4">
  <div class="card-header">
    <h5 class="m-0">{{ __('Managers in this Region') }}</h5>
  </div>
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-hover">
        <thead>
          <tr>
            <th>{{ __('Name') }}</th>
            <th>{{ __('Email') }}</th>
            <th>{{ __('Phone') }}</th>
            <th>{{ __('Employees') }}</th>
            <th>{{ __('Agencies') }}</th>
            <th>{{ __('Status') }}</th>
            <th>{{ __('Action') }}</th>
          </tr>
        </thead>
        <tbody>
          @foreach($region->managers as $manager)
          <tr>
            <td>{{ $manager->name }}</td>
            <td>{{ $manager->email }}</td>
            <td>{{ $manager->phone }}</td>
            <td>{{ $manager->employees->count() }}</td>
            <td>{{ $manager->agencies->count() }}</td>
            <td>
              <span class="badge bg-label-{{$manager->status ? 'success' : 'danger'}}">
                {{$manager->status ? 'Active' : 'Inactive'}}
              </span>
            </td>
            <td>
              <a href="{{ route('admin.managers.show', $manager) }}" class="btn btn-sm btn-info">
                <i class="fa-solid fa-eye"></i>
              </a>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>
@endif

<div class="mt-3">
  <a href="{{ route('admin.regions.edit', $region) }}" class="btn btn-primary">{{ __('Edit Region') }}</a>
  <a href="{{ route('admin.regions.index') }}" class="btn btn-secondary">{{ __('Back to Regions') }}</a>
</div>
@endsection
