@extends('layouts.admin.master')

@section('title', 'Regions')

@section('content-header', __('Regions'))

@section('breadcrumbs')
<li class="breadcrumb-item">
  <a href="{{route('admin.regions.index')}}">{{ __('Regions') }}</a>
</li>
<li class="breadcrumb-item active">
  {{ __('View Regions') }}
</li>
@endsection

@section('content')
<div class="card">
<div class="card-header header-elements d-flex justify-content-between align-items-center">
  <h5 class="m-0 me-2">{{ __('Regions')}}</h5>
  <form class="form d-flex align-items-center" method="GET" action="#">
      <a href="{{route('admin.regions.create')}}" class="btn btn-primary" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="tooltip-primary" title="{{__('Add New')}}"><i class="fa-solid fa-plus"></i>{{__('Add New')}}</a>
    </div>
  </form>
<div class="table-responsive">
  <table class="table table-hover data-table">
    <thead class="border-top">
      <tr>
        <th>{{ __('Name') }}</th>
        <th>{{ __('Code') }}</th>
        <th>{{ __('Managers') }}</th>
        <th>{{ __('Employees') }}</th>
        <th>{{ __('Status') }}</th>
        <th>{{ __('Created At') }}</th>
        <th>{{ __('Actions') }}</th>
      </tr>
    </thead>
    <tbody class="table-border-bottom-0">
      @forelse ($data as $region)
        <tr>
          <td><i class="fab fa-angular fa-lg text-danger me-3"></i> <strong>{{ $region->name }}</strong></td>
          <td>{{ $region->code }}</td>
          <td>{{ $region->managers_count }}</td>
          <td>{{ $region->employees_count }}</td>
          <td>
            @if($region->status)
              <span class="badge bg-label-success me-1">Active</span>
            @else
              <span class="badge bg-label-danger me-1">Inactive</span>
            @endif
          </td>
          <td>{{ $region->created_at->format('d M, Y') }}</td>
          <td>
            <a href="{{ route('admin.regions.show', $region) }}" class="btn btn-info btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="tooltip-info" title="{{__('View')}}"><i class="fa-solid fa-eye"></i></a>
            <a href="{{ route('admin.regions.edit', $region) }}" class="btn btn-primary btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="tooltip-primary" title="{{__('Edit')}}"><i class="fa-solid fa-pen-to-square"></i></a>
            <a href="javascript:void(0)" class="btn btn-warning btn-sm" onclick="changeStatus({{ $region->id }})" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="tooltip-warning" title="{{__('Change Status')}}"><i class="fa-solid fa-power-off"></i></a>
            <a href="javascript:void(0)" class="btn btn-danger btn-sm" onclick="deleteRegion({{ $region->id }})" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="tooltip-danger" title="{{__('Delete')}}"><i class="fa-solid fa-trash"></i></a>
          </td>
        </tr>
      @empty
        <tr>
          <td colspan="7" class="text-center">{{ __('No regions found') }}</td>
        </tr>
      @endforelse
    </tbody>
  </table>
  <div class="d-flex justify-content-center mt-3">
    {{ $data->links() }}
  </div>
</div>

<div class="modal fade" id="changeStatusModal" tabindex="-1" aria-labelledby="changeStatusModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="changeStatusForm" method="POST" action="{{ route('admin.regions.updateStatus') }}">
        @csrf
        @method('PUT')
        <div class="modal-header">
          <h5 class="modal-title" id="changeStatusModalLabel">{{__('Change Status')}}</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="id" id="regionId">
          <p>{{__('Are you sure you want to change the status of this region?')}}</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{__('Close')}}</button>
          <button type="submit" class="btn btn-primary">{{__('Change Status')}}</button>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="deleteForm" method="POST" action="">
        @csrf
        @method('DELETE')
        <div class="modal-header">
          <h5 class="modal-title" id="deleteModalLabel">{{__('Delete Region')}}</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <p>{{__('Are you sure you want to delete this region? This action cannot be undone.')}}</p>
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
    document.getElementById('regionId').value = id;
    $('#changeStatusModal').modal('show');
  }

  function deleteRegion(id) {
    document.getElementById('deleteForm').action = `regions/${id}`;
    $('#deleteModal').modal('show');
  }
</script>
@endsection

@endsection
