@extends('layouts.admin.master')

@section('title', 'Employees')

@section('content-header', __('Employees'))

@section('breadcrumbs')
<li class="breadcrumb-item">
  <a href="{{route('admin.employees.index')}}">{{ __('Employees') }}</a>
</li>
<li class="breadcrumb-item active">
  {{ __('View Employees') }}
</li>
@endsection

@section('content')
<div class="card">
<div class="card-header header-elements d-flex justify-content-between align-items-center">
  <h5 class="m-0 me-2">{{ __('Employees')}}</h5>
  <form class="form d-flex align-items-center" method="GET" action="#">
      <a href="{{route('admin.employees.create')}}" class="btn btn-primary" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="tooltip-primary" title="{{__('Add New')}}"><i class="fa-solid fa-plus"></i>{{__('Add New')}}</a>
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
          <th>{{__('Designation')}}</th>
          <th>{{__('Number of Agencies')}}</th>
          <th>{{__('Status')}}</th>
          <th>{{__('Action')}}</th>
      </tr>
    </thead>
    <tbody class="table-border-bottom-0">
      @forelse($data as $key => $datum)
      <tr>
        <td><span class="fw-medium">{{ $key + 1 }}</span></td>
        <td>
          <a class="dropdown-item" href="{{ route('admin.employees.show', $datum) }}" class="dropdown-item">
            {{ $datum->name }}
          </a>
        </td>
        <td>{{ $datum->email }}</td>
        <td>{{ $datum->phone }}</td>
        <td>{{ $datum->designation }}</td>
        <td>{{ $datum->agencies->count() }}</td>
        <td>
          <span class="badge bg-label-{{$datum->status ? 'success' : 'danger'}} me-1" id="statusText{{$datum->id}}">
            {{status_formatted($datum->status)}}
          </span>
          <a href="javascript:void(0)" class="change-status" data-id="{{$datum->id}}" data-bs-toggle="modal" data-bs-target="#changeStatusModal" data-status="{{ $datum->status }}">
            <i class="fa-solid fa-pen-to-square"></i>
          </a>
        </td>
        <td>
          <a href="{{ route('admin.employees.edit', $datum) }}" class="btn btn-primary btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="tooltip-primary" title="{{__('Edit')}}"><i class="fa-solid fa-pen-to-square"></i></a>
          <a href="{{ route('admin.employees.show', $datum) }}" class="btn btn-info btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="tooltip-info" title="{{__('View')}}"><i class="fa-solid fa-eye"></i></a>
          <a href="javascript:void(0)" class="btn btn-danger btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="tooltip-danger" title="{{__('Delete')}}" onclick="deleteData({{$datum->id}})"><i class="fa-solid fa-trash"></i></a>
          <form id="delete-form-{{$datum->id}}" action="{{ route('admin.employees.destroy', $datum) }}" method="POST" style="display: none;">
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
</div>
<div class="card-footer d-flex justify-content-center">
    {{ $data->withQueryString()->links() }}
</div>
</div>
</div>

<!-- Change Status Modal -->

<div class="modal fade" id="changeStatusModal" tabindex="-1" aria-labelledby="changeStatusModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="changeStatusForm" method="POST" action="{{ route('admin.employees.updateStatus') }}">
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
    document.getElementById('deleteForm').action = `employees/${id}`;
    $('#deleteModal').modal('show');
  }

</script>

@endsection
