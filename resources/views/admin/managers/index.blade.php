@extends('layouts.admin.master')

@section('title', 'Managers')

@section('content-header', __('Managers'))

@section('breadcrumbs')
<li class="breadcrumb-item">
  <a href="{{route('admin.managers.index')}}">{{ __('Managers') }}</a>
</li>
<li class="breadcrumb-item active">
  {{ __('View Managers') }}
</li>
@endsection

@section('content')
<div class="card">
<div class="card-header header-elements d-flex justify-content-between align-items-center">
  <h5 class="m-0 me-2">{{ __('Managers')}}</h5>
  <form class="form d-flex align-items-center" method="GET" action="#">
      <a href="{{route('admin.managers.create')}}" class="btn btn-primary" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="tooltip-primary" title="{{__('Add New')}}"><i class="fa-solid fa-plus"></i>{{__('Add New')}}</a>
    </div>
  </form>
<div class="table-responsive">
  <table class="table table-hover data-table">
    <thead class="border-top">
      <tr>
        <th>{{ __('Name') }}</th>
        <th>{{ __('Email') }}</th>
        <th>{{ __('Phone') }}</th>
        <th>{{ __('Designation') }}</th>
        <th>{{ __('Region') }}</th>
        <th>{{ __('Status') }}</th>
        <th>{{ __('Created At') }}</th>
        <th>{{ __('Actions') }}</th>
      </tr>
    </thead>
    <tbody class="table-border-bottom-0">
      @forelse ($data as $manager)
        <tr>
          <td><i class="fab fa-angular fa-lg text-danger me-3"></i> <strong>{{ $manager->name }}</strong></td>
          <td>{{ $manager->email }}</td>
          <td>{{ $manager->phone }}</td>
          <td>{{ $manager->designation }}</td>
          <td>
            <span class="badge bg-label-info">{{ $manager->region->name ?? 'N/A' }}</span>
          </td>
          <td>
            @if($manager->status)
              <span class="badge bg-label-success me-1">Active</span>
            @else
              <span class="badge bg-label-danger me-1">Inactive</span>
            @endif
          </td>
          <td>{{ $manager->created_at->format('d M, Y') }}</td>
          <td>
            <a href="{{ route('admin.managers.show', $manager) }}" class="btn btn-info btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="tooltip-info" title="{{__('View')}}"><i class="fa-solid fa-eye"></i></a>
            <a href="{{ route('admin.managers.edit', $manager) }}" class="btn btn-primary btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="tooltip-primary" title="{{__('Edit')}}"><i class="fa-solid fa-pen-to-square"></i></a>
            <a href="javascript:void(0)" class="btn btn-warning btn-sm" onclick="changeStatus({{ $manager->id }})" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="tooltip-warning" title="{{__('Change Status')}}"><i class="fa-solid fa-power-off"></i></a>
            <a href="javascript:void(0)" class="btn btn-danger btn-sm" onclick="deleteManager({{ $manager->id }})" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="tooltip-danger" title="{{__('Delete')}}"><i class="fa-solid fa-trash"></i></a>
          </td>
        </tr>
      @empty
        <tr>
          <td colspan="8" class="text-center">{{ __('No managers found') }}</td>
        </tr>
      @endforelse
    </tbody>
  </table>
  <div class="d-flex justify-content-center mt-3">
    {{ $data->appends(request()->query())->links() }}
  </div>
</div>

<!-- Status Change Modal -->
<div class="modal fade" id="changeStatusModal" tabindex="-1" aria-labelledby="changeStatusModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="changeStatusForm" method="POST" action="{{ route('admin.managers.updateStatus') }}">
        @csrf
        @method('PUT')
        <div class="modal-header">
          <h5 class="modal-title" id="changeStatusModalLabel">{{__('Change Status')}}</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="id" id="managerId">
          <p>{{__('Are you sure you want to change the status of this manager?')}}</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{__('Close')}}</button>
          <button type="submit" class="btn btn-primary">{{__('Change Status')}}</button>
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
          <h5 class="modal-title" id="deleteModalLabel">{{__('Delete Manager')}}</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <p>{{__('Are you sure you want to delete this manager? This action cannot be undone.')}}</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{__('Close')}}</button>
          <button type="submit" class="btn btn-danger">{{__('Delete')}}</button>
        </div>
      </form>
    </div>
  </div>
</div>

@section('scripts')
<script>
  function changeStatus(id) {
    document.getElementById('managerId').value = id;
    $('#changeStatusModal').modal('show');
  }

  function deleteManager(id) {
    document.getElementById('deleteForm').action = `managers/${id}`;
    $('#deleteModal').modal('show');
  }
</script>
@endsection

@endsection
