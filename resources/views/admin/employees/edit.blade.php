@extends('layouts.admin.master')

@section('title', __('Employees'))

@section('content-header', __('Employees'))

@section('breadcrumbs')
<li class="breadcrumb-item">
    <a href="{{route('admin.employees.index')}}">{{ __('Employees') }}</a>
</li>
<li class="breadcrumb-item active">
    {{ __('Edit Employee') }}
</li>
@endsection

@section('content')
<!-- Multi Column with Form Separator -->
<div class="card mb-4">
        <h5 class="card-header">{{ __('Add employee') }}</h5>
        <form id="employeeForm" class="card-body" action="{{ route('admin.employees.update', $employee) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <hr class="mt-0" />
                <div class="row">
                    <div class="mb-3 col-6">
                            <label for="name" class="form-label">{{ __('Name') }}</label>
                            <input type="text" class="form-control" id="name" name="name" placeholder="{{ __('Name') }}" required value="{{ old('name') ?? $employee->name }}">
                    </div>
                    <div class="mb-3 col-6">
                            <label for="email" class="form-label">{{ __('Email') }}</label>
                            <input type="email" class="form-control" id="email" name="email" placeholder="{{ __('Email') }}" required value="{{ old('email') ?? $employee->email }}">
                    </div>
                    <div class="mb-3 col-6">
                            <label for="phone" class="form-label">{{ __('Phone') }}</label>
                            <input type="text" class="form-control" id="phone" name="phone" placeholder="{{ __('Phone') }}" required value="{{ old('phone') ?? $employee->phone }}">
                    </div>
                    <div class="mb-3 col-6">
                            <label for="address" class="form-label">{{ __('Address') }}</label>
                            <input type="text" class="form-control" id="address" name="address" placeholder="{{ __('Address') }}" required value="{{ old('address') ?? $employee->address }}">
                    </div>
                        <div class="mb-3 col-6">
                                <label for="designation" class="form-label">{{ __('Designation') }}</label>
                                <input type="text" class="form-control" id="designation" name="designation" placeholder="{{ __('Designation') }}" required value="{{ old('designation') ?? $employee->designation }}">
                        </div>

                </div>
                <div class="pt-4">
                        <button type="submit" class="btn btn-primary me-sm-3 me-1">{{ __('Submit') }}</button>
                        <a href="{{route('admin.employees.index')}}" class="btn btn-label-secondary">{{ __('Cancel') }}</a>
                </div>
        </form>
</div>

</div>
@endsection
@section('scripts')
    <script>
    document.getElementById("employeeForm").addEventListener("submit", function(event) {
            // Add any additional validation if needed
    });
    </script>
@endsection