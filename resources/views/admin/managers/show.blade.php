@extends('layouts.admin.master')

@section('title', 'View Manager')

@section('content-header', __('View Manager'))

@section('breadcrumbs')
<li class="breadcrumb-item">
  <a href="{{route('admin.managers.index')}}">{{ __('Managers') }}</a>
</li>
<li class="breadcrumb-item active">
  {{ __('View Manager') }}
</li>
@endsection

@section('content')
<div class="row">
  <!-- Manager Details -->
  <div class="col-md-6">
    <div class="card">
      <div class="card-header">
        <h5 class="m-0">{{ __('Manager Details') }}</h5>
      </div>
      <div class="card-body">
        <table class="table table-borderless">
          <tr>
            <th width="40%">{{ __('Name') }}:</th>
            <td>{{ $manager->name }}</td>
          </tr>
          <tr>
            <th>{{ __('Email') }}:</th>
            <td>{{ $manager->email }}</td>
          </tr>
          <tr>
            <th>{{ __('Phone') }}:</th>
            <td>{{ $manager->phone }}</td>
          </tr>
          <tr>
            <th>{{ __('Region') }}:</th>
            <td>{{ $manager->region->name ?? 'N/A' }} 
              @if($manager->region)
                <small class="text-muted">({{ $manager->region->code }})</small>
              @endif
            </td>
          </tr>
          <tr>
            <th>{{ __('Status') }}:</th>
            <td>
              <span class="badge bg-label-{{$manager->status ? 'success' : 'danger'}}">
                {{$manager->status ? 'Active' : 'Inactive'}}
              </span>
            </td>
          </tr>
          <tr>
            <th>{{ __('Created') }}:</th>
            <td>{{ $manager->created_at->format('d M Y, h:i A') }}</td>
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
              <h4 class="text-primary">{{ $manager->employees->count() }}</h4>
              <small>{{ __('Employees') }}</small>
            </div>
          </div>
          <div class="col-4">
            <div class="border rounded p-3">
              <h4 class="text-info">{{ $manager->agencies->count() }}</h4>
              <small>{{ __('Agencies') }}</small>
            </div>
          </div>
          <div class="col-4">
            <div class="border rounded p-3">
              <h4 class="text-success">{{ $manager->agencies->sum(function($agency) { return $agency->customers->count(); }) }}</h4>
              <small>{{ __('Customers') }}</small>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Employees List -->
@if($manager->employees->count() > 0)
<div class="card mt-4">
  <div class="card-header">
    <h5 class="m-0">{{ __('Employees under this Manager') }}</h5>
  </div>
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-hover">
        <thead>
          <tr>
            <th>{{ __('Name') }}</th>
            <th>{{ __('Email') }}</th>
            <th>{{ __('Phone') }}</th>
            <th>{{ __('Designation') }}</th>
            <th>{{ __('Agencies') }}</th>
            <th>{{ __('Status') }}</th>
            <th>{{ __('Action') }}</th>
          </tr>
        </thead>
        <tbody>
          @foreach($manager->employees as $employee)
          <tr>
            <td>{{ $employee->name }}</td>
            <td>{{ $employee->email }}</td>
            <td>{{ $employee->phone }}</td>
            <td>{{ $employee->designation }}</td>
            <td>{{ $employee->agencies->count() }}</td>
            <td>
              <span class="badge bg-label-{{$employee->status ? 'success' : 'danger'}}">
                {{$employee->status ? 'Active' : 'Inactive'}}
              </span>
            </td>
            <td>
              <a href="{{ route('admin.employees.show', $employee) }}" class="btn btn-sm btn-info">
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

<!-- Agencies List -->
@if($manager->agencies->count() > 0)
<div class="card mt-4">
  <div class="card-header">
    <h5 class="m-0">{{ __('Agencies in this Region') }}</h5>
  </div>
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-hover">
        <thead>
          <tr>
            <th>{{ __('Name') }}</th>
            <th>{{ __('Email') }}</th>
            <th>{{ __('Employee') }}</th>
            <th>{{ __('Customers') }}</th>
            <th>{{ __('Status') }}</th>
            <th>{{ __('Action') }}</th>
          </tr>
        </thead>
        <tbody>
          @foreach($manager->agencies as $agency)
          <tr>
            <td>{{ $agency->name }}</td>
            <td>{{ $agency->email }}</td>
            <td>{{ $agency->employee->name ?? 'N/A' }}</td>
            <td>{{ $agency->customers->count() }}</td>
            <td>
              <span class="badge bg-label-{{$agency->status ? 'success' : 'danger'}}">
                {{$agency->status ? 'Active' : 'Inactive'}}
              </span>
            </td>
            <td>
              <a href="{{ route('admin.agencies.show', $agency) }}" class="btn btn-sm btn-info">
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
  <a href="{{ route('admin.managers.edit', $manager) }}" class="btn btn-primary">{{ __('Edit Manager') }}</a>
  <a href="{{ route('admin.managers.index') }}" class="btn btn-secondary">{{ __('Back to Managers') }}</a>
</div>
@endsection
