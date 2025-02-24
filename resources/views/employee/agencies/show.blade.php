@extends('layouts.employee.master')

@section('title', 'Agencies')

@section('content-header', __('bank_accounts')) 

@section('breadcrumbs')
<li class="breadcrumb-item">
  <a href="{{route('employee.agencies.index')}}">{{ __('Agencies') }}</a>
</li>
<li class="breadcrumb-item active">
  {{ __('View Agency') }}
</li>
@endsection

@section('content')
<div class="card">
<div class="card-header header-elements d-flex justify-content-between align-items-center">
  <h5 class="m-0 me-2">{{ __('Agency')}}</h5>
</div>
<div class="card-body">
  <div class="row">
    <div class="col-md-6">
      <h6>{{ __('Name') }}</h6>
      <p>{{ $agency->name }}</p>
    </div>
    <div class="col-md-6">
      <h6>{{ __('Email') }}</h6>
      <p>{{ $agency->email }}</p>
    </div>
    <div class="col-md-6">
      <h6>{{ __('Phone') }}</h6>
      <p>{{ $agency->phone }}</p>
    </div>
    <div class="col-md-6">
      <h6>{{ __('Address') }}</h6>
      <p>{{ $agency->address }}</p>
    </div>
    <div class="col-md-6">
      <h6>{{ __('Status') }}</h6>
      <p>{{ $agency->status? 'Active' : 'Inactive' }}</p>
    </div>
    <div class="col-md-6">
      <h6>{{ __('Employee') }}</h6>
      <p>{{ $agency->employee->name ??  'NA' }}</p>
    </div>
    <div class="col-md-6">
      <h6>{{ __('Number of Customers') }}</h6>
      <p>{{ $agency->customers->count() }}</p>
    </div>
    <div class="col-md-6">
      <h6>{{ __('Created At') }}</h6>
      <p>{{ $agency->created_at->format('d M, Y') }}</p>
    </div>
    <div class="col-md-12">
      <h6>{{ __('Address') }}</h6>
      <p>{{ $agency->address }}</p>
    </div>
  </div>
</div>
</div>

<div class="card mt-4">
  <div class="card-header">
    <h5>{{ __('Customers') }}</h5>
  </div>
  <div class="card-body">
    @if($agency->customers->isEmpty())
      <p>{{ __('No customers found.') }}</p>
    @else
      <table class="table table-bordered">
        <thead>
          <tr>
            <th>{{ __('Name') }}</th>
            <th>{{ __('Email') }}</th>
            <th>{{ __('UUID') }}</th>
            <th>{{ __('Phone') }}</th>
            <th>{{ __('Joined At') }}</th>
            <th>{{ __('Action') }}</th>
          </tr>
        </thead>
        <tbody>
          @php
          $customers = $agency->customers()->paginate(10);
          @endphp
          @foreach($customers as $customer)
            <tr>
              <td>{{ $customer->name }}</td>
              <td>{{ $customer->email }}</td>
              <td>{{ $customer->uuid }}</td>
              <td>{{ $customer->phone }}</td>
              <td>{{ $customer->created_at->format('d M, Y') }}</td>
              <td>
                <a href="{{ route('employee.customers.show', $customer) }}" class="btn btn-info btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="tooltip-info" title="{{__('View')}}"><i class="fa-solid fa-eye"></i></a>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
      <div class="d-flex justify-content-center mt-3">
        {{ $customers->links() }}
      </div>
    @endif
  </div>
</div>
@endsection
