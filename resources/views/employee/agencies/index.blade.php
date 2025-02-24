@extends('layouts.employee.master')

@section('title', 'Agencies')

@section('content-header', __('bank_accounts')) 

@section('breadcrumbs')
<li class="breadcrumb-item">
  <a href="{{route('employee.agencies.index')}}">{{ __('Agencies') }}</a>
</li>
<li class="breadcrumb-item active">
  {{ __('View Agencies') }}
</li>
@endsection

@section('content')
<div class="card">
<div class="card-header header-elements d-flex justify-content-between align-items-center">
  <h5 class="m-0 me-2">{{ __('Agencies')}}</h5>
  <form class="form d-flex align-items-center" method="GET" action="#">
      <a href="{{route('employee.agencies.create')}}" class="btn btn-primary" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="tooltip-primary" title="{{__('Add New')}}"><i class="fa-solid fa-plus"></i>{{__('Add New')}}</a>
    </div>
  </form>
<div class="table-responsive">
  <table class="table table-hover data-table">
    <thead class="border-top">
      <tr>
          <th>{{__('S. No')}}</th>
          <th>{{__('Name')}}</th>
          <th>{{__('Email')}}</th>
          <th>{{__('Phone')}}</th>
          <th>{{__('Address')}}</th>
          <th>{{__('Number of Customers')}}</th>
          <th>{{__('Status')}}</th>
          <th>{{__('Action')}}</th>
      </tr>
    </thead>
    <tbody class="table-border-bottom-0">
      @forelse($data as $key => $datum)
      <tr>
        <td><span class="fw-medium">{{ $key + 1 }}</span></td>
        <td>
          <a class="dropdown-item" href="{{ route('employee.agencies.show', $datum) }}" class="dropdown-item">
            {{ $datum->name }}
          </a>
        </td>
        <td>{{ $datum->email }}</td>
        <td>{{ $datum->phone }}</td>
        <td>{{ $datum->address }}</td>
        <td>{{ $datum->customers->count() }}</td>
        <td>
          <span class="badge bg-label-{{$datum->status ? 'success' : 'danger'}} me-1" id="statusText{{$datum->id}}">
            {{status_formatted($datum->status)}}
          </span>
        </td>
        <td>
          <a href="{{ route('employee.agencies.show', $datum) }}" class="btn btn-primary btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="tooltip-primary" title="{{__('View')}}"><i class="fa-solid fa-eye"></i></a>
        </td>
      </tr>
      @empty
      <tr>
        <td colspan="12" class="text-center">
        <span class="no-data-frame">
                  <img src="{{ asset('/images/no-data.png') }}" class="no-data-avater" alt="No Data">
                  <h2> {{ __('No Data Found') }}</h2>
          </span>
        </td>
      </tr>
      @endforelse
    </tbody>
  </table>
</div>
</div>
</div>
@endsection
