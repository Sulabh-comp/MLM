@extends('layouts.manager.master')

@section('title', __('Add Manager'))

@section('content-header', __('Add Manager'))

@section('breadcrumbs')
<li class="breadcrumb-item">
    <a href="{{route('manager.dashboard')}}">{{ __('Dashboard') }}</a>
</li>
<li class="breadcrumb-item">
    <a href="{{route('manager.managers.index')}}">{{ __('Team Management') }}</a>
</li>
<li class="breadcrumb-item active">
    {{ __('Add Manager') }}
</li>
@endsection

@section('content')
<div class="card">
    <h5 class="card-header">Add New Manager to Your Team</h5>
    <form id="managerForm" class="card-body" action="{{ route('manager.managers.store') }}" method="POST">
        @csrf
        <hr class="mt-0" />
        
        <!-- Manager Information -->
        <div class="row">
            <div class="mb-3 col-6">
                <label for="name" class="form-label">{{ __('Full Name') }}</label>
                <input type="text" class="form-control" id="name" name="name" placeholder="{{ __('Enter full name') }}" required value="{{ old('name') }}">
                @error('name')
                    <div class="text-danger small">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3 col-6">
                <label for="email" class="form-label">{{ __('Email Address') }}</label>
                <input type="email" class="form-control" id="email" name="email" placeholder="{{ __('Enter email address') }}" required value="{{ old('email') }}">
                @error('email')
                    <div class="text-danger small">{{ $message }}</div>
                @enderror
            </div>
        </div>
        
        <div class="row">
            <div class="mb-3 col-6">
                <label for="phone" class="form-label">{{ __('Phone Number') }}</label>
                <input type="text" class="form-control" id="phone" name="phone" placeholder="{{ __('Enter phone number') }}" required value="{{ old('phone') }}">
                @error('phone')
                    <div class="text-danger small">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3 col-6">
                <label for="designation" class="form-label">{{ __('Designation') }}</label>
                <input type="text" class="form-control" id="designation" name="designation" placeholder="{{ __('Enter designation') }}" required value="{{ old('designation') }}">
                @error('designation')
                    <div class="text-danger small">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- Hierarchy Settings -->
        <hr />
        <h6 class="mb-3">Hierarchy Settings</h6>
        
        <div class="row">
            <div class="mb-3 col-6">
                <label for="level_name" class="form-label">{{ __('Manager Level') }}</label>
                <select class="form-select" id="level_name" name="level_name" required>
                    <option value="">{{ __('Select Manager Level') }}</option>
                    @foreach($availableLevels as $level)
                        <option value="{{ $level->name }}" @selected($level->name == old('level_name'))>
                            {{ $level->name }} ({{ $level->code }}) - Level {{ $level->hierarchy_level }}
                        </option>
                    @endforeach
                </select>
                @error('level_name')
                    <div class="text-danger small">{{ $message }}</div>
                @enderror
                <div class="form-text">You can only assign levels lower than your current level.</div>
            </div>
            <div class="mb-3 col-6">
                <label for="parent_id" class="form-label">{{ __('Reports To (Parent Manager)') }}</label>
                <select class="form-select" id="parent_id" name="parent_id">
                    <option value="">{{ __('Select Parent (defaults to you)') }}</option>
                    @foreach($potentialParents as $parent)
                        <option value="{{ $parent->id }}" @selected($parent->id == old('parent_id'))>
                            {{ str_repeat('—', ($parent->depth ?? 0) - ($currentManager->depth ?? 0)) }} {{ $parent->name }}
                            @if($parent->level_name) ({{ $parent->level_name }}) @endif
                        </option>
                    @endforeach
                </select>
                @error('parent_id')
                    <div class="text-danger small">{{ $message }}</div>
                @enderror
                <div class="form-text">If not selected, the manager will report directly to you.</div>
            </div>
        </div>

        <!-- Security Settings -->
        <hr />
        {{-- <h6 class="mb-3">Security Settings</h6>
        
        <div class="row">
            <div class="mb-3 col-6">
                <label for="password" class="form-label">{{ __('Initial Password') }}</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="{{ __('Leave empty for default password') }}" value="{{ old('password') }}">
                @error('password')
                    <div class="text-danger small">{{ $message }}</div>
                @enderror
                <div class="form-text">If left empty, default password "password123" will be used.</div>
            </div>
            <div class="mb-3 col-6">
                <label for="password_confirmation" class="form-label">{{ __('Confirm Password') }}</label>
                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="{{ __('Confirm password') }}">
            </div>
        </div> --}}
        @if($errors->any())
            <div class="alert alert-danger">
                <strong>{{ __('Please fix the following errors:') }}</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

        <!-- Information Panel -->
        <div class="alert alert-info">
            <h6 class="mb-2"><i class="ti ti-info-circle me-2"></i>Information</h6>
            <ul class="mb-0">
                <li>The new manager will be added to your team hierarchy</li>
                <li>They will inherit your territorial permissions</li>
                <li>You will be able to view and manage their performance</li>
                <li>The manager can create their own subordinates (if level permits)</li>
            </ul>
        </div>

        <div class="pt-4">
            <button type="submit" class="btn btn-primary me-sm-3 me-1">{{ __('Create Manager') }}</button>
            <a href="{{route('manager.managers.index')}}" class="btn btn-label-secondary">{{ __('Cancel') }}</a>
        </div>
    </form>
</div>

@section('scripts')
<script>
    document.getElementById("managerForm").addEventListener("submit", function(event) {
        const levelSelect = document.getElementById('level_name');
        const parentSelect = document.getElementById('parent_id');
        
        if (!levelSelect.value) {
            event.preventDefault();
            alert('Please select a manager level');
            levelSelect.focus();
            return false;
        }
        
        // Additional validation can be added here
    });

    // Dynamic parent selection based on level
    document.getElementById('level_name').addEventListener('change', function() {
        // Future enhancement: filter parent options based on selected level
    });
</script>
@endsection

@endsection
