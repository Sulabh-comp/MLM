@extends('layouts.admin.master')

@section('title', __('agencies'))

@section('content-header', __('agencies'))

@section('breadcrumbs')
<li class="breadcrumb-item">
    <a href="{{route('admin.agencies.index')}}">{{ __('agencies') }}</a>
</li>
<li class="breadcrumb-item active">
    {{ __('add_agency') }}
</li>
@endsection

@section('content')
<!-- Multi Column with Form Separator -->
<div class="card mb-4">
        <h5 class="card-header">{{ __('Add Agency') }}</h5>
        <form id="agencyForm" class="card-body" action="{{ route('admin.agencies.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <hr class="mt-0" />
                <div class="row">
                    <div class="mb-3 col-6">
                            <label for="name" class="form-label">{{ __('Name') }}</label>
                            <input type="text" class="form-control" id="name" name="name" placeholder="{{ __('Name') }}" required value="{{ old('name') }}">
                    </div>
                    <div class="mb-3 col-6">
                            <label for="email" class="form-label">{{ __('Email') }}</label>
                            <input type="email" class="form-control" id="email" name="email" placeholder="{{ __('Email') }}" required value="{{ old('email') }}">
                    </div>
                    <div class="mb-3 col-6">
                            <label for="phone" class="form-label">{{ __('Phone') }}</label>
                            <input type="text" class="form-control" id="phone" name="phone" placeholder="{{ __('Phone') }}" required value="{{ old('phone') }}">
                    </div>
                    <div class="mb-3 col-6">
                            <label for="address" class="form-label">{{ __('Address') }}</label>
                            <input type="text" class="form-control" id="address" name="address" placeholder="{{ __('Address') }}" required value="{{ old('address') }}">
                    </div>
                        <div class="mb-3 col-6">
                                <label for="employee_id" class="form-label">{{ __('Employee') }}</label>
                                <select class="form-select" id="employee_id" name="employee_id"                                 >
                                    <option value="">{{ __('Select Employee') }}</option>
                                    @foreach($employees as $employee)
                                        <option value="{{ $employee->id }}" @selected($employee->id == old('employee_id'))>{{ $employee->name }}</option>
                                    @endforeach
                                </select>
                        </div>
                </div>
                <div class="pt-4">
                        <button type="submit" class="btn btn-primary me-sm-3 me-1">{{ __('Submit') }}</button>
                        <a href="{{route('admin.agencies.index')}}" class="btn btn-label-secondary">{{ __('Cancel') }}</a>
                </div>
        </form>
</div>

</div>
@endsection
@section('scripts')
    <script>
    document.getElementById("agencyForm").addEventListener("submit", function(event) {
            // Add any additional validation if needed
    });
    </script>
@endsection