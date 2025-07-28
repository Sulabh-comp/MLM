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
          <th>{{__('S. No')}}</th>
          <th>{{__('Name')}}</th>
          <th>{{__('Code')}}</th>
          <th>{{__('Description')}}</th>
          <th>{{__('Managers')}}</th>
          <th>{{__('Agencies')}}</th>
          <th>{{__('Status')}}</th>
          <th>{{__('Action')}}</th>
      </tr>
    </thead>
    <tbody class="table-border-bottom-0">
      @forelse($regions as $key => $region)
      <tr>
        <td><span class="fw-medium">{{ $key + 1 }}</span></td>
        <td>
          <a href="{{ route('admin.regions.show', $region) }}" class="dropdown-item">
            {{ $region->name }}
          </a>
        </td>
        <td>{{ $region->code }}</td>
        <td>{{ Str::limit($region->description, 50) }}</td>
        <td>{{ $region->managers_count ?? 0 }}</td>
        <td>{{ $region->agencies_count ?? 0 }}</td>
        <td>
          <span class="badge bg-label-{{$region->status ? 'success' : 'danger'}} me-1">
            {{$region->status ? 'Active' : 'Inactive'}}
          </span>
          <button type="button" class="btn btn-sm btn-outline-secondary" onclick="toggleStatus({{ $region->id }})">
            <i class="fa-solid fa-toggle-{{$region->status ? 'on' : 'off'}}"></i>
          </button>
        </td>
        <td>
          <a href="{{ route('admin.regions.edit', $region) }}" class="btn btn-primary btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="tooltip-primary" title="{{__('Edit')}}"><i class="fa-solid fa-pen-to-square"></i></a>
          <a href="{{ route('admin.regions.show', $region) }}" class="btn btn-info btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="tooltip-info" title="{{__('View')}}"><i class="fa-solid fa-eye"></i></a>
          <a href="javascript:void(0)" class="btn btn-danger btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="tooltip-danger" title="{{__('Delete')}}" onclick="deleteData({{$region->id}})"><i class="fa-solid fa-trash"></i></a>
          <form id="delete-form-{{$region->id}}" action="{{ route('admin.regions.destroy', $region) }}" method="POST" style="display: none;">
            @csrf
            @method('DELETE')
          </form>
        </td>
      </tr>
      @empty
      <tr>
        <td colspan="8" class="text-center">
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

{{ $regions->links() }}
@endsection

@section('scripts')
<script>
function toggleStatus(regionId) {
    if (confirm('Are you sure you want to change the status?')) {
        fetch(`/admin/regions/${regionId}/toggle-status`, {
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

function deleteData(regionId) {
    if (confirm('Are you sure you want to delete this region?')) {
        document.getElementById('delete-form-' + regionId).submit();
    }
}
</script>
@endsection
