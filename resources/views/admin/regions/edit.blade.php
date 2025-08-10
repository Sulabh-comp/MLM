@extends('layouts.admin.master')

@section('title', __('Regions'))

@section('content-header', __('Regions'))

@section('breadcrumbs')
<li class="breadcrumb-item">
    <a href="{{route('admin.regions.index')}}">{{ __('Regions') }}</a>
</li>
<li class="breadcrumb-item active">
    {{ __('Edit Region') }}
</li>
@endsection

@section('content')
<!-- Multi Column with Form Separator -->
<div class="card mb-4">
        <h5 class="card-header">{{ __('Edit Region') }}</h5>
        <form id="regionForm" class="card-body" action="{{ route('admin.regions.update', $region) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <hr class="mt-0" />
                <div class="row">
                    <div class="mb-3 col-6">
                            <label for="name" class="form-label">{{ __('Name') }}</label>
                            <input type="text" class="form-control" id="name" name="name" placeholder="{{ __('Region Name') }}" required value="{{ old('name', $region->name) }}">
                    </div>
                    <div class="mb-3 col-6">
                            <label for="code" class="form-label">{{ __('Code') }}</label>
                            <input type="text" class="form-control" id="code" name="code" placeholder="{{ __('Region Code') }}" required value="{{ old('code', $region->code) }}">
                    </div>
                    <div class="mb-3 col-12">
                            <label for="description" class="form-label">{{ __('Description') }}</label>
                            <textarea class="form-control" id="description" name="description" placeholder="{{ __('Region Description') }}">{{ old('description', $region->description) }}</textarea>
                    </div>
                    <div class="mb-3 col-12">
                            <label for="states" class="form-label">{{ __('States') }}</label>
                            <input type="text" class="form-control" id="states" name="states" placeholder="{{ __('Enter states separated by commas') }}" value="{{ old('states', is_array($region->states) ? implode(', ', $region->states) : $region->states) }}">
                            <div class="form-text">{{ __('Enter states separated by commas (e.g., Delhi, Punjab, Haryana)') }}</div>
                    </div>
                </div>
                <div class="pt-4">
                        <button type="submit" class="btn btn-primary me-sm-3 me-1">{{ __('Update') }}</button>
                        <a href="{{route('admin.regions.index')}}" class="btn btn-label-secondary">{{ __('Cancel') }}</a>
                </div>
        </form>
</div>

@endsection
