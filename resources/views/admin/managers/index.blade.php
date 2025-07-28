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
          <th>{{__('S. No')}}</th>
          <th>{{__('Name')}}</th>
          <th>{{__('Email')}}</th>
          <th>{{__('Phone')}}</th>
          <th>{{__('Region')}}</th>
          <th>{{__('Employees')}}</th>
          <th>{{__('Agencies')}}</th>
          <th>{{__('Status')}}</th>
          <th>{{__('Action')}}</th>
      </tr>
    </thead>
    <tbody class="table-border-bottom-0">
      @forelse($managers as $key => $manager)
      <tr>
        <td><span class="fw-medium">{{ $key + 1 }}</span></td>
        <td>
          <a href="{{ route('admin.managers.show', $manager) }}" class="dropdown-item">
            {{ $manager->name }}
          </a>
        </td>
        <td>{{ $manager->email }}</td>
        <td>{{ $manager->phone }}</td>
        <td>{{ $manager->region->name ?? 'N/A' }}</td>
        <td>{{ $manager->employees->count() }}</td>
        <td>{{ $manager->agencies->count() }}</td>
        <td>
          <span class="badge bg-label-{{$manager->status ? 'success' : 'danger'}} me-1">
            {{$manager->status ? 'Active' : 'Inactive'}}
          </span>
          <button type="button" class="btn btn-sm btn-outline-secondary" onclick="toggleStatus({{ $manager->id }})">
            <i class="fa-solid fa-toggle-{{$manager->status ? 'on' : 'off'}}"></i>
          </button>
        </td>
        <td>
          <a href="{{ route('admin.managers.edit', $manager) }}" class="btn btn-primary btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="tooltip-primary" title="{{__('Edit')}}"><i class="fa-solid fa-pen-to-square"></i></a>
          <a href="{{ route('admin.managers.show', $manager) }}" class="btn btn-info btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="tooltip-info" title="{{__('View')}}"><i class="fa-solid fa-eye"></i></a>
          <a href="javascript:void(0)" class="btn btn-danger btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="tooltip-danger" title="{{__('Delete')}}" onclick="deleteData({{$manager->id}})"><i class="fa-solid fa-trash"></i></a>
          <form id="delete-form-{{$manager->id}}" action="{{ route('admin.managers.destroy', $manager) }}" method="POST" style="display: none;">
            @csrf
            @method('DELETE')
          </form>
        </td>
      </tr>
      @empty
      <tr>
        <td colspan="9" class="text-center">
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

{{ $managers->links() }}
@endsection

@section('scripts')
<script>
function toggleStatus(managerId) {
    if (confirm('Are you sure you want to change the status?')) {
        fetch(`/admin/managers/${managerId}/toggle-status`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error updating status');
            }
        });
    }
}

function deleteData(managerId) {
    if (confirm('Are you sure you want to delete this manager?')) {
        document.getElementById('delete-form-' + managerId).submit();
    }
}
</script>
@endsection
