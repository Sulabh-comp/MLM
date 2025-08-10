@extends('layouts.admin.master')

@section('title', __('Regions'))

@section('content-header', __('Regions'))

@section('breadcrumbs')
<li class="breadcrumb-item">
    <a href="{{route('admin.regions.index')}}">{{ __('Regions') }}</a>
</li>
<li class="breadcrumb-item active">
    {{ __('View Region') }}
</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header header-elements d-flex justify-content-between align-items-center">
        <h5 class="m-0 me-2">{{ __('Region Details') }}</h5>
        <div class="form d-flex align-items-center">
            <a href="{{ route('admin.regions.edit', $region) }}" class="btn btn-primary" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="tooltip-primary" title="{{ __('Edit') }}">
                <i class="fa-solid fa-pen-to-square"></i>{{ __('Edit') }}
            </a>
            <a href="javascript:void(0)" class="btn btn-danger ml-3" onclick="deleteData({{ $region->id }})" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="tooltip-danger" title="{{ __('Delete') }}">
                <i class="fa-solid fa-trash"></i>{{ __('Delete') }}
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <h6>{{ __('Name') }}</h6>
                <p>{{ $region->name }}</p>
            </div>
            <div class="col-md-6">
                <h6>{{ __('Code') }}</h6>
                <p><span class="badge bg-primary">{{ $region->code }}</span></p>
            </div>
            <div class="col-md-6">
                <h6>{{ __('Description') }}</h6>
                <p>{{ $region->description ?? 'N/A' }}</p>
            </div>
            <div class="col-md-6">
                <h6>{{ __('Status') }}</h6>
                <p>
                    @if($region->status)
                        <span class="badge bg-success">{{ __('Active') }}</span>
                    @else
                        <span class="badge bg-danger">{{ __('Inactive') }}</span>
                    @endif
                </p>
            </div>
            <div class="col-md-12">
                <h6>{{ __('States') }}</h6>
                <p>
                    @if(is_array($region->states) && count($region->states) > 0)
                        @foreach($region->states as $state)
                            <span class="badge bg-info me-1">{{ trim($state) }}</span>
                        @endforeach
                    @else
                        {{ __('No states assigned') }}
                    @endif
                </p>
            </div>
            <div class="col-md-6">
                <h6>{{ __('Managers Count') }}</h6>
                <p><span class="badge bg-warning">{{ $region->managers->count() }}</span></p>
            </div>
            <div class="col-md-6">
                <h6>{{ __('Employees Count') }}</h6>
                <p><span class="badge bg-secondary">{{ $region->employees->count() }}</span></p>
            </div>
            <div class="col-md-6">
                <h6>{{ __('Created At') }}</h6>
                <p>{{ $region->created_at->format('M d, Y H:i') }}</p>
            </div>
            <div class="col-md-6">
                <h6>{{ __('Updated At') }}</h6>
                <p>{{ $region->updated_at->format('M d, Y H:i') }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Managers Section -->
<div class="card mt-4">
    <div class="card-header">
        <h5>{{ __('Managers in this Region') }}</h5>
    </div>
    <div class="card-body">
        @if($managers->isEmpty())
            <p>{{ __('No managers found in this region.') }}</p>
        @else
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>{{ __('Name') }}</th>
                            <th>{{ __('Email') }}</th>
                            <th>{{ __('Phone') }}</th>
                            <th>{{ __('Designation') }}</th>
                            <th>{{ __('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($managers as $manager)
                            <tr>
                                <td>{{ $manager->name }}</td>
                                <td>{{ $manager->email }}</td>
                                <td>{{ $manager->phone }}</td>
                                <td>{{ $manager->designation }}</td>
                                <td>
                                    <a href="{{ route('admin.managers.show', $manager) }}" class="btn btn-info btn-sm">
                                        <i class="fa-solid fa-eye"></i> {{ __('View') }}
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center">
                {{ $managers->appends(request()->query())->render() }}
            </div>
        @endif
    </div>
</div>

<!-- Employees Section -->
<div class="card mt-4">
    <div class="card-header">
        <h5>{{ __('Employees in this Region') }}</h5>
    </div>
    <div class="card-body">
        @if($employees->isEmpty())
            <p>{{ __('No employees found in this region.') }}</p>
        @else
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>{{ __('Name') }}</th>
                            <th>{{ __('Email') }}</th>
                            <th>{{ __('Phone') }}</th>
                            <th>{{ __('Agencies Count') }}</th>
                            <th>{{ __('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($employees as $employee)
                            <tr>
                                <td>{{ $employee->name }}</td>
                                <td>{{ $employee->email }}</td>
                                <td>{{ $employee->phone }}</td>
                                <td><span class="badge bg-success">{{ $employee->agencies->count() }}</span></td>
                                <td>
                                    <a href="{{ route('admin.employees.show', $employee) }}" class="btn btn-info btn-sm">
                                        <i class="fa-solid fa-eye"></i> {{ __('View') }}
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center">
                {{ $employees->appends(request()->query())->render() }}
            </div>
        @endif
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">{{ __('Delete Region') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                {{ __('Are you sure you want to delete this region?') }}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">{{ __('Delete') }}</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function deleteData(id) {
    document.getElementById('deleteForm').action = `/admin/regions/${id}`;
    $('#deleteModal').modal('show');
}
</script>

@endsection
