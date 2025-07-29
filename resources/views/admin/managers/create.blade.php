@extends('layouts.admin.master')

@section('title', __('Managers'))

@section('content-header', __('Managers'))

@section('breadcrumbs')
<li class="breadcrumb-item">
    <a href="{{route('admin.managers.index')}}">{{ __('Managers') }}</a>
</li>
<li class="breadcrumb-item active">
    {{ __('Add Manager') }}
</li>
@endsection

@section('content')
<!-- Multi Column with Form Separator -->
<div class="card mb-4">
        <h5 class="card-header">{{ __('Add Manager') }}</h5>
        <form id="managerForm" class="card-body" action="{{ route('admin.managers.store') }}" method="POST" enctype="multipart/form-data">
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
                            <label for="designation" class="form-label">{{ __('Designation') }}</label>
                            <input type="text" class="form-control" id="designation" name="designation" placeholder="{{ __('Designation') }}" required value="{{ old('designation') }}">
                    </div>
                    <div class="mb-3 col-6">
                            <label for="region_id" class="form-label">{{ __('Region') }}</label>
                            <select class="form-select" id="region_id" name="region_id" required>
                                <option value="">{{ __('Select Region') }}</option>
                                @foreach($regions as $region)
                                    <option value="{{ $region->id }}" @selected($region->id == old('region_id'))>{{ $region->name }} ({{ $region->code }})</option>
                                @endforeach
                            </select>
                    </div>
                </div>
                <div class="pt-4">
                        <button type="submit" class="btn btn-primary me-sm-3 me-1">{{ __('Submit') }}</button>
                        <a href="{{route('admin.managers.index')}}" class="btn btn-label-secondary">{{ __('Cancel') }}</a>
                </div>
        </form>
</div>

@section('scripts')
    <script>
    document.getElementById("managerForm").addEventListener("submit", function(event) {
            // Add any additional validation if needed
    });
    </script>
@endsection
@endsection
