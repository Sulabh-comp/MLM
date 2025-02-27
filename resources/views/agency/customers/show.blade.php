@extends('layouts.agency.master')

@section('title', 'Customers')

@section('content-header', __('Customers'))

@section('breadcrumbs')
<li class="breadcrumb-item">
  <a href="{{ route('agency.customers.index') }}">{{ __('Customers') }}</a>
</li>
<li class="breadcrumb-item active">
  {{ __('View Customer') }}
</li>
@endsection

@section('content')
<div class="card">
  <div class="card-header header-elements d-flex justify-content-between align-items-center">
    <h5 class="m-0 me-2">{{ __('Customer') }}</h5>
    <div class="form d-flex align-items-center">
      <a href="{{ route('agency.customers.edit', $customer) }}" class="btn btn-primary" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="tooltip-primary" title="{{ __('Edit') }}">
        <i class="fa-solid fa-pen-to-square"></i>{{ __('Edit') }}
      </a>
      <a href="javascript:void(0)" class="btn btn-danger ml-3" onclick="deleteData({{ $customer->id }})" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="tooltip-danger" title="{{ __('Delete') }}">
        <i class="fa-solid fa-trash"></i>{{ __('Delete') }}
      </a>
    </div>
  </div>
  <div class="card-body">
    <div class="row">
      <div class="col-md-6">
        <h6>{{ __('First Name') }}</h6>
        <p>{{ $customer->first_name }}</p>
      </div>
      <div class="col-md-6">
        <h6>{{ __('Last Name') }}</h6>
        <p>{{ $customer->last_name }}</p>
      </div>
      <div class="col-md-6">
        <h6>{{ __('Email') }}</h6>
        <p>{{ $customer->email }}</p>
      </div>
      <div class="col-md-6">
        <h6>{{ __('Phone') }}</h6>
        <p>{{ $customer->phone }}</p>
      </div>
      <div class="col-md-6">
        <h6>{{ __('Address Line 1') }}</h6>
        <p>{{ $customer->address_1 }}</p>
      </div>
      <div class="col-md-6">
        <h6>{{ __('Address Line 2') }}</h6>
        <p>{{ $customer->address_2 }}</p>
      </div>
      <div class="col-md-6">
        <h6>{{ __('City') }}</h6>
        <p>{{ $customer->city }}</p>
      </div>
      <div class="col-md-6">
        <h6>{{ __('State') }}</h6>
        <p>{{ $customer->state }}</p>
      </div>
      <div class="col-md-6">
        <h6>{{ __('PIN Code') }}</h6>
        <p>{{ $customer->pin }}</p>
      </div>
      <div class="col-md-6">
        <h6>{{ __('Country') }}</h6>
        <p>{{ $customer->country }}</p>
      </div>
      <div class="col-md-6">
        <h6>{{ __('Gender') }}</h6>
        <p>{{ $customer->gender }}</p>
      </div>
      <div class="col-md-6">
        <h6>{{ __('Date of Birth') }}</h6>
        <p>{{ $customer->dob ? $customer->dob : 'N/A' }}</p>
      </div>
      <div class="col-md-6">
        <h6>{{ __('Aadhar Number') }}</h6>
        <p>{{ $customer->adhar_number }}</p>
      </div>
      <div class="col-md-6">
        <h6>{{ __('Status') }}</h6>
        <p>
          <span class="badge bg-label-{{ $customer->status ? 'success' : 'danger' }}">
            {{ $customer->status ? 'Active' : 'Inactive' }}
          </span>
          <a href="javascript:void(0)" class="change-status" data-id="{{ $customer->id }}" data-bs-toggle="modal" data-bs-target="#changeStatusModal">
            <i class="fa-solid fa-pen-to-square"></i>
          </a>
        </p>
      </div>
      <div class="col-md-6">
        <h6>{{ __('Agency') }}</h6>
        <p>{{ $customer->agency->name ?? 'N/A' }}</p>
      </div>
      <div class="col-md-6">
        <h6>{{ __('Created At') }}</h6>
        <p>{{ $customer->created_at->format('d M, Y') }}</p>
      </div>
    </div>
  </div>
</div>

<div class="card mt-4">
  <div class="card-header">
    <h5>{{ __('Family Members') }}</h5>
    <a href="{{ route('agency.family-members.create', ['customer_id' => $customer->id]) }}" class="btn btn-primary">
        <i class="fa-solid fa-plus"></i> {{ __('Add New Family Member') }}
    </a>
  </div>
  <div class="card-body">
    @if($customer->familyMembers->isEmpty())
      <p>{{ __('No family members found.') }}</p>
    @else
      <table class="table table-bordered">
        <thead>
          <tr>
            <th>{{ __('Name') }}</th>
            <th>{{ __('Position') }}</th>
            <th>{{ __('Age') }}</th>
            <th>{{ __('Gender') }}</th>
            <th>{{ __('Occupation') }}</th>
            <th>{{ __('Contact Number') }}</th>
            <th>{{ __('Action') }}</th>
          </tr>
        </thead>
        <tbody>
          @foreach($customer->familyMembers as $familyMember)
            <tr>
              <td>{{ $familyMember->name }}</td>
              <td>{{ $familyMember->position }}</td>
              <td>{{ $familyMember->age }}</td>
              <td>{{ $familyMember->gender }}</td>
              <td>{{ $familyMember->occupation }}</td>
              <td>{{ $familyMember->contact_number }}</td>
              <td>
                <a href="{{ route('agency.family-members.show', $familyMember) }}" class="btn btn-info btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="tooltip-info" title="{{ __('View') }}">
                  <i class="fa-solid fa-eye"></i>
                </a>
                <a href="{{ route('agency.family-members.edit', $familyMember) }}" class="btn btn-primary btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="tooltip-primary" title="{{ __('Edit') }}">
                  <i class="fa-solid fa-pen-to-square"></i>
                </a>
                <a href="javascript:void(0)" class="btn btn-danger btn-sm" onclick="deleteFamilyMember({{ $familyMember->id }})" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="tooltip-danger" title="{{ __('Delete') }}">
                  <i class="fa-solid fa-trash"></i>
                </a>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    @endif
  </div>
</div>

<!-- Change Status Modal -->
<div class="modal fade" id="changeStatusModal" tabindex="-1" aria-labelledby="changeStatusModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="changeStatusForm" method="POST" action="{{ route('agency.customers.updateStatus') }}">
        @csrf
        @method('PUT')
        <div class="modal-header">
          <h5 class="modal-title" id="changeStatusModalLabel">{{ __('Change Status') }}</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <p>{{ __('Are you sure you want to change the status?') }}</p>
          <input type="hidden" name="id" id="id">
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
          <button type="submit" class="btn btn-primary">{{ __('Save changes') }}</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="deleteForm" method="POST" action="">
        @csrf
        @method('DELETE')
        <div class="modal-header">
          <h5 class="modal-title" id="deleteModalLabel">{{ __('Delete') }}</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <p>{{ __('Are you sure you want to delete?') }}</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
          <button type="submit" class="btn btn-danger">{{ __('Delete') }}</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
  document.querySelectorAll('.change-status').forEach(item => {
    item.addEventListener('click', event => {
      document.getElementById('id').value = item.getAttribute('data-id');
    });
  });

  function deleteData(id) {
    document.getElementById('deleteForm').action = `${id}`;
    $('#deleteModal').modal('show');
  }

  function deleteFamilyMember(id) {
    document.getElementById('deleteForm').action = `/admin/family-members/${id}`;
    $('#deleteModal').modal('show');
  }
</script>
@endsection
