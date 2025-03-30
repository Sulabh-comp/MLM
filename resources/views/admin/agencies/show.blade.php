@extends('layouts.admin.master')

@section('title', 'Agencies')

@section('content-header', __('bank_accounts')) 

@section('breadcrumbs')
<li class="breadcrumb-item">
  <a href="{{route('admin.agencies.index')}}">{{ __('Agencies') }}</a>
</li>
<li class="breadcrumb-item active">
  {{ __('View Agency') }}
</li>
@endsection

@section('content')
<div class="card">
<div class="card-header header-elements d-flex justify-content-between align-items-center">
  <h5 class="m-0 me-2">{{ __('Agency')}}</h5>

  <div class="form d-flex align-items-center">
    <a href="{{ route('admin.agencies.edit', $agency) }}" class="btn btn-primary" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="tooltip-primary" title="{{__('Edit')}}"><i class="fa-solid fa-pen-to-square"></i>{{__('Edit')}}</a>
    <a href="javascript:void(0)" class="btn btn-danger mr-3" onclick="deleteData({{$agency->id}})" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="tooltip-danger" title="{{__('Delete')}}"><i class="fa-solid fa-trash"></i>{{__('Delete')}}</a>
  </div>
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
      <p>{{ $agency->status? 'Active' : 'Inactive' }}
        <a href="javascript:void(0)" class="change-status" data-id="{{$agency->id}}" data-bs-toggle="modal" data-status="{{ $agency->status }}" data-bs-target="#changeStatusModal">
          <i class="fa-solid fa-pen-to-square"></i>
        </a>
      </p>
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
                <a href="{{ route('admin.customers.show', $customer) }}" class="btn btn-info btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="tooltip-info" title="{{__('View')}}"><i class="fa-solid fa-eye"></i></a>
                <a href="{{ route('admin.customers.edit', $customer) }}" class="btn btn-primary btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="tooltip-primary" title="{{__('Edit')}}"><i class="fa-solid fa-pen-to-square"></i></a>
                <a href="javascript:void(0)" class="btn btn-danger btn-sm" onclick="deleteCustomer({{ $customer->id }})" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="tooltip-danger" title="{{__('Delete')}}"><i class="fa-solid fa-trash"></i></a>
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

<div class="modal fade" id="changeStatusModal" tabindex="-1" aria-labelledby="changeStatusModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="changeStatusForm" method="POST" action="{{ route('admin.agencies.updateStatus') }}">
        @csrf
        @method('PUT')
        <div class="modal-header">
          <h5 class="modal-title" id="changeStatusModalLabel">{{__('Change Status')}}</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <p>{{__('Are you sure you want to change the status to ')}} <span id="status"> </span>?</p>
          <input type="hidden" name="id" id="id">
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{__('Close')}}</button>
          <button type="submit" class="btn btn-primary">{{__('Save changes')}}</button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- delete modal --}}

<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="deleteForm" method="POST" action="">
        @csrf
        @method('DELETE')
        <div class="modal-header">
          <h5 class="modal-title" id="deleteModalLabel">{{__('Delete')}}</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body
        ">
          <p>{{__('Are you sure you want to delete?')}}</p>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{__('Close')}}</button>
          <button type="submit" class="btn btn-danger">{{__('Delete')}}</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
  document.querySelectorAll('.change-status').forEach(item => {
    item.addEventListener('click', event => {
      document.getElementById('id').value = item.getAttribute('data-id');
      document.getElementById('status').innerText = item.getAttribute('data-status') == 1 ? 'Inactive' : 'Active';
    })
  })

  function deleteData(id) {
    document.getElementById('deleteForm').action = `agencies/${id}`;
    $('#deleteModal').modal('show');
  }

  function deleteCustomer(id) {
    document.getElementById('deleteForm').action = `customers/${id}`;
    $('#deleteModal').modal('show');
  }

</script>

@endsection
