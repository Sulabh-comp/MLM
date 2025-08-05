@extends('layouts.admin.master')

@section('title', 'Customers')

@section('content-header', __('Customers'))

@section('breadcrumbs')
<li class="breadcrumb-item">
  <a href="{{ route('admin.customers.index') }}">{{ __('Customers') }}</a>
</li>
<li class="breadcrumb-item active">
  {{ __('View Customers') }}
</li>
@endsection

@section('content')
<!-- Filters Card -->
<div class="card mb-4">
  <div class="card-header">
    <h5 class="mb-0">
      <i class="ti ti-filter me-2"></i>Filters & Export
    </h5>
  </div>
  <div class="card-body">
    <form method="GET" action="{{ route('admin.customers.index') }}" id="filterForm">
      <div class="row">
        <div class="col-md-3 mb-3">
          <label class="form-label">Search</label>
          <input type="text" class="form-control" name="q" placeholder="Name, Email, Phone..." value="{{ request('q') }}">
        </div>
        <div class="col-md-3 mb-3">
          <label class="form-label">Agency</label>
          <select class="form-select" name="agency_id">
            <option value="">All Agencies</option>
            @foreach($agencies as $agency)
              <option value="{{ $agency->id }}" {{ request('agency_id') == $agency->id ? 'selected' : '' }}>
                {{ $agency->name }}
              </option>
            @endforeach
          </select>
        </div>
        <div class="col-md-3 mb-3">
          <label class="form-label">Status</label>
          <select class="form-select" name="status">
            <option value="">All Status</option>
            <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Active</option>
            <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inactive</option>
          </select>
        </div>
        <div class="col-md-3 mb-3">
          <label class="form-label">Gender</label>
          <select class="form-select" name="gender">
            <option value="">All Genders</option>
            <option value="Male" {{ request('gender') === 'Male' ? 'selected' : '' }}>Male</option>
            <option value="Female" {{ request('gender') === 'Female' ? 'selected' : '' }}>Female</option>
            <option value="Other" {{ request('gender') === 'Other' ? 'selected' : '' }}>Other</option>
          </select>
        </div>
        <div class="col-md-3 mb-3">
          <label class="form-label">State</label>
          <select class="form-select" name="state">
            <option value="">All States</option>
            @foreach($states as $state)
              <option value="{{ $state }}" {{ request('state') === $state ? 'selected' : '' }}>
                {{ $state }}
              </option>
            @endforeach
          </select>
        </div>
        <div class="col-md-3 mb-3">
          <label class="form-label">City</label>
          <select class="form-select" name="city">
            <option value="">All Cities</option>
            @foreach($cities as $city)
              <option value="{{ $city }}" {{ request('city') === $city ? 'selected' : '' }}>
                {{ $city }}
              </option>
            @endforeach
          </select>
        </div>
        <div class="col-md-3 mb-3">
          <label class="form-label">Religion</label>
          <select class="form-select" name="religion">
            <option value="">All Religions</option>
            @foreach($religions as $religion)
              <option value="{{ $religion }}" {{ request('religion') === $religion ? 'selected' : '' }}>
                {{ $religion }}
              </option>
            @endforeach
          </select>
        </div>
        <div class="col-md-3 mb-3">
          <label class="form-label">Date From</label>
          <input type="date" class="form-control" name="date_from" value="{{ request('date_from') }}">
        </div>
        <div class="col-md-3 mb-3">
          <label class="form-label">Date To</label>
          <input type="date" class="form-control" name="date_to" value="{{ request('date_to') }}">
        </div>
        <div class="col-md-3 mb-3">
          <label class="form-label">&nbsp;</label>
          <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">
              <i class="ti ti-search me-1"></i>Filter
            </button>
            <a href="{{ route('admin.customers.index') }}" class="btn btn-outline-secondary">
              <i class="ti ti-x me-1"></i>Clear
            </a>
          </div>
        </div>
      </div>
    </form>
  </div>
</div>

<div class="card">
  <div class="card-header header-elements d-flex justify-content-between align-items-center">
    <h5 class="m-0 me-2">{{ __('Customers') }} ({{ $data->total() }} total)</h5>
    <div class="d-flex gap-2">
      <button type="button" class="btn btn-success" onclick="exportCustomers()">
        <i class="ti ti-download me-1"></i>Export Filtered Data
      </button>
      <button type="button" class="btn btn-warning" onclick="exportAllFamilyMembers()">
        <i class="ti ti-users me-1"></i>Export All Family Members
      </button>
      <a href="{{ route('admin.customers.create') }}" class="btn btn-primary">
        <i class="fa-solid fa-plus me-1"></i>{{ __('Add New') }}
      </a>
    </div>
  </div>
  <div class="table-responsive">
    <table class="table table-hover data-table">
      <thead class="border-top">
        <tr>
          <th>{{ __('S. No') }}</th>
          <th>{{ __('Name') }}</th>
          <th>{{ __('Email') }}</th>
          <th>{{ __('Phone') }}</th>
          <th>{{ __('Address') }}</th>
          <th>{{ __('Agency') }}</th>
          <th>{{ __('Family Members') }}</th>
          <th>{{ __('Status') }}</th>
          <th>{{ __('Action') }}</th>
        </tr>
      </thead>
      <tbody class="table-border-bottom-0">
        @forelse($data as $key => $datum)
        <tr>
          <td><span class="fw-medium">{{ $key + 1 }}</span></td>
          <td>
            <a class="dropdown-item" href="{{ route('admin.customers.show', $datum) }}" class="dropdown-item">
              {{ $datum->first_name }} {{ $datum->last_name }}
            </a>
          </td>
          <td>{{ $datum->email }}</td>
          <td>{{ $datum->phone }}</td>
          <td>{{ $datum->address_1 }}, {{ $datum->city }}, {{ $datum->state }}</td>
          <td>{{ $datum->agency->name }}</td>
          <td>
            <span class="badge bg-info">{{ $datum->familyMembers->count() }} Members</span>
            @if($datum->familyMembers->count() > 0)
              <a href="{{ route('admin.customers.export-family-members', $datum) }}" class="btn btn-outline-success btn-sm ms-1" title="Export Family Members">
                <i class="ti ti-download"></i>
              </a>
            @endif
          </td>
          <td>
            <span class="badge bg-label-{{ $datum->status ? 'success' : 'danger' }} me-1" id="statusText{{ $datum->id }}">
              {{ status_formatted($datum->status) }}
            </span>
            <a href="javascript:void(0)" class="change-status" data-id="{{ $datum->id }}" data-bs-toggle="modal" data-status="{{ $datum->status }}" data-bs-target="#changeStatusModal">
              <i class="fa-solid fa-pen-to-square"></i>
            </a>
          </td>
          <td>
            <a href="{{ route('admin.customers.edit', $datum) }}" class="btn btn-primary btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="tooltip-primary" title="{{ __('Edit') }}">
              <i class="fa-solid fa-pen-to-square"></i>
            </a>
            <a href="{{ route('admin.customers.show', $datum) }}" class="btn btn-info btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="tooltip-info" title="{{ __('View') }}">
              <i class="fa-solid fa-eye"></i>
            </a>
            <a href="javascript:void(0)" class="btn btn-danger btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="tooltip-danger" title="{{ __('Delete') }}" onclick="deleteData({{ $datum->id }})">
              <i class="fa-solid fa-trash"></i>
            </a>
            <form id="delete-form-{{ $datum->id }}" action="{{ route('admin.customers.destroy', $datum) }}" method="POST" style="display: none;">
              @csrf
              @method('DELETE')
            </form>
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
    <div class="card-footer d-flex justify-content-center">
      @if($data->hasPages())
        {{ $data->render() }}
      @endif
    </div>
</div>

<!-- Change Status Modal -->
<div class="modal fade" id="changeStatusModal" tabindex="-1" aria-labelledby="changeStatusModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="changeStatusForm" method="POST" action="{{ route('admin.customers.updateStatus') }}">
        @csrf
        @method('PUT')
        <div class="modal-header">
          <h5 class="modal-title" id="changeStatusModalLabel">{{ __('Change Status') }}</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <p>{{__('Are you sure you want to change the status to ')}} <span id="status"> </span>?</p>
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
      document.getElementById('status').innerText = item.getAttribute('data-status') == 1 ? 'Inactive' : 'Active';
    });
  });

  function deleteData(id) {
    document.getElementById('deleteForm').action = `/admin/customers/${id}`;
    $('#deleteModal').modal('show');
  }

  function exportCustomers() {
    // Get all current filter parameters
    const params = new URLSearchParams();
    
    // Add all form data from the filter form
    const formData = new FormData(document.getElementById('filterForm'));
    for (let [key, value] of formData.entries()) {
      if (value) {
        params.append(key, value);
      }
    }
    
    // Navigate to export URL with filters
    window.location.href = `{{ route('admin.customers.export') }}?${params.toString()}`;
  }

  function exportAllFamilyMembers() {
    // Get all current filter parameters
    const params = new URLSearchParams();
    
    // Add all form data from the filter form
    const formData = new FormData(document.getElementById('filterForm'));
    for (let [key, value] of formData.entries()) {
      if (value) {
        params.append(key, value);
      }
    }
    
    // Navigate to export URL with filters
    window.location.href = `{{ route('admin.customers.export-all-family-members') }}?${params.toString()}`;
  }
</script>

  function deleteData(id) {
    document.getElementById('deleteForm').action = `customers/${id}`;
    $('#deleteModal').modal('show');
  }
</script>
@endsection
