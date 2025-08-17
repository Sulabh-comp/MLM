@extends('layouts.admin.master')

@section('title', __('Edit Manager'))

@section('content-header', __('Edit Manager'))

@section('breadcrumbs')
<li class="breadcrumb-item">
    <a href="{{route('admin.managers.index')}}">{{ __('Managers') }}</a>
</li>
<li class="breadcrumb-item active">
    {{ __('Edit Manager') }}
</li>
@endsection

@section('content')
<!-- Multi Column with Form Separator -->
<div class="card mb-4">
        <h5 class="card-header">{{ __('Edit Manager') }}: {{ $manager->name }}</h5>
        <form id="managerForm" class="card-body" action="{{ route('admin.managers.update', $manager) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <hr class="mt-0" />
                <div class="row">
                    <div class="mb-3 col-6">
                            <label for="name" class="form-label">{{ __('Name') }}</label>
                            <input type="text" class="form-control" id="name" name="name" placeholder="{{ __('Name') }}" required value="{{ old('name', $manager->name) }}">
                    </div>
                    <div class="mb-3 col-6">
                            <label for="email" class="form-label">{{ __('Email') }}</label>
                            <input type="email" class="form-control" id="email" name="email" placeholder="{{ __('Email') }}" required value="{{ old('email', $manager->email) }}">
                    </div>
                    <div class="mb-3 col-6">
                            <label for="phone" class="form-label">{{ __('Phone') }}</label>
                            <input type="text" class="form-control" id="phone" name="phone" placeholder="{{ __('Phone') }}" required value="{{ old('phone', $manager->phone) }}">
                    </div>
                    <div class="mb-3 col-6">
                            <label for="designation" class="form-label">{{ __('Designation') }}</label>
                            <input type="text" class="form-control" id="designation" name="designation" placeholder="{{ __('Designation') }}" required value="{{ old('designation', $manager->designation) }}">
                    </div>
                    <div class="mb-3 col-6">
                            <label for="level_name" class="form-label">{{ __('Manager Level') }}</label>
                            <select class="form-select" id="level_name" name="level_name" required>
                                <option value="">{{ __('Select Manager Level') }}</option>
                                @foreach($managerLevels as $level)
                                    <option value="{{ $level->name }}" @selected($level->name == old('level_name', $manager->level_name))>
                                        {{ $level->name }} ({{ $level->code }}) - Level {{ $level->hierarchy_level }}
                                    </option>
                                @endforeach
                            </select>
                    </div>
                    <div class="mb-3 col-6">
                            <label for="parent_id" class="form-label">{{ __('Reports To (Parent Manager)') }}</label>
                            <select class="form-select" id="parent_id" name="parent_id">
                                <option value="">{{ __('No Parent (Top Level)') }}</option>
                                @foreach($potentialParents as $parentManager)
                                    <option value="{{ $parentManager->id }}" @selected($parentManager->id == old('parent_id', $manager->parent_id))>
                                        {{ str_repeat('—', $parentManager->depth) }} {{ $parentManager->name }} 
                                        @if($parentManager->level_name) ({{ $parentManager->level_name }}) @endif
                                    </option>
                                @endforeach
                            </select>
                    </div>
                </div>

                @if($manager->children()->count() > 0)
                    <div class="alert alert-info">
                        <i class="bx bx-info-circle me-2"></i>
                        {{ __('This manager has') }} <strong>{{ $manager->children()->count() }}</strong> {{ __('direct subordinate(s)') }}.
                        {{ __('Changing the parent will affect the hierarchy structure.') }}
                        
                        <div class="mt-2">
                            <h6>{{ __('Direct Subordinates:') }}</h6>
                            <ul class="mb-0">
                                @foreach($manager->children as $child)
                                    <li>{{ $child->name }} ({{ $child->level_name ?? 'No Level' }})</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif

                @if($manager->depth > 0)
                    <div class="alert alert-secondary">
                        <i class="bx bx-sitemap me-2"></i>
                        <strong>{{ __('Current Hierarchy Path:') }}</strong>
                        <div class="mt-2">
                            {{ $manager->hierarchy_path ? str_replace(',', ' → ', $manager->hierarchy_path) : __('No path defined') }}
                        </div>
                    </div>
                @endif

                <div class="pt-4">
                        <button type="submit" class="btn btn-primary me-sm-3 me-1">{{ __('Update') }}</button>
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
