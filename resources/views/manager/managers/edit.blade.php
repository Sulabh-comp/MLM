@extends('layouts.manager.master')

@section('title', __('Edit Manager'))

@section('content-header', __('Edit Manager'))

@section('breadcrumbs')
<li class="breadcrumb-item">
    <a href="{{route('manager.dashboard')}}">{{ __('Dashboard') }}</a>
</li>
<li class="breadcrumb-item">
    <a href="{{route('manager.managers.index')}}">{{ __('Team Management') }}</a>
</li>
<li class="breadcrumb-item">
    <a href="{{route('manager.managers.show', $manager)}}">{{ $manager->name }}</a>
</li>
<li class="breadcrumb-item active">
    {{ __('Edit') }}
</li>
@endsection

@section('content')
<div class="card">
    <h5 class="card-header">Edit Manager: {{ $manager->name }}</h5>
    <form id="managerForm" class="card-body" action="{{ route('manager.managers.update', $manager) }}" method="POST">
        @csrf
        @method('PUT')
        <hr class="mt-0" />
        
        <!-- Manager Information -->
        <div class="row">
            <div class="mb-3 col-6">
                <label for="name" class="form-label">{{ __('Full Name') }}</label>
                <input type="text" class="form-control" id="name" name="name" placeholder="{{ __('Enter full name') }}" required value="{{ old('name', $manager->name) }}">
                @error('name')
                    <div class="text-danger small">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3 col-6">
                <label for="email" class="form-label">{{ __('Email Address') }}</label>
                <input type="email" class="form-control" id="email" name="email" placeholder="{{ __('Enter email address') }}" required value="{{ old('email', $manager->email) }}">
                @error('email')
                    <div class="text-danger small">{{ $message }}</div>
                @enderror
            </div>
        </div>
        
        <div class="row">
            <div class="mb-3 col-6">
                <label for="phone" class="form-label">{{ __('Phone Number') }}</label>
                <input type="text" class="form-control" id="phone" name="phone" placeholder="{{ __('Enter phone number') }}" required value="{{ old('phone', $manager->phone) }}">
                @error('phone')
                    <div class="text-danger small">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3 col-6">
                <label for="designation" class="form-label">{{ __('Designation') }}</label>
                <input type="text" class="form-control" id="designation" name="designation" placeholder="{{ __('Enter designation') }}" required value="{{ old('designation', $manager->designation) }}">
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
                        <option value="{{ $level->name }}" @selected($level->name == old('level_name', $manager->level_name))>
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
                    <option value="">{{ __('Select Parent') }}</option>
                    @foreach($potentialParents as $parent)
                        <option value="{{ $parent->id }}" @selected($parent->id == old('parent_id', $manager->parent_id))>
                            {{ str_repeat('—', ($parent->depth ?? 0) - ($currentManager->depth ?? 0)) }} {{ $parent->name }}
                            @if($parent->level_name) ({{ $parent->level_name }}) @endif
                        </option>
                    @endforeach
                </select>
                @error('parent_id')
                    <div class="text-danger small">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- Current Hierarchy Information -->
        <div class="alert alert-info">
            <h6 class="mb-2"><i class="ti ti-info-circle me-2"></i>Current Hierarchy Information</h6>
            <div class="row">
                <div class="col-md-6">
                    <strong>Current Level:</strong> {{ $manager->level_name ?? 'Not Set' }}<br>
                    <strong>Current Parent:</strong> {{ $manager->parent ? $manager->parent->name : 'Top Level' }}<br>
                    <strong>Hierarchy Depth:</strong> {{ $manager->depth ?? 0 }}
                </div>
                <div class="col-md-6">
                    <strong>Direct Subordinates:</strong> {{ $manager->children()->count() }}<br>
                    <strong>Total Team:</strong> {{ $manager->allSubordinates()->count() }}<br>
                    <strong>Employees:</strong> {{ $manager->employees()->count() }}
                </div>
            </div>
        </div>

        @if($manager->children()->count() > 0)
            <div class="alert alert-warning">
                <h6 class="mb-2"><i class="ti ti-alert-triangle me-2"></i>Warning</h6>
                <p class="mb-2">This manager has <strong>{{ $manager->children()->count() }}</strong> direct subordinate(s):</p>
                <ul class="mb-2">
                    @foreach($manager->children as $child)
                        <li>{{ $child->name }} ({{ $child->level_name ?? 'No Level' }})</li>
                    @endforeach
                </ul>
                <p class="mb-0">Changing the parent or level may affect the hierarchy structure and permissions.</p>
            </div>
        @endif

        <div class="pt-4">
            <button type="submit" class="btn btn-primary me-sm-3 me-1">{{ __('Update Manager') }}</button>
            <a href="{{route('manager.managers.show', $manager)}}" class="btn btn-label-secondary">{{ __('Cancel') }}</a>
            <a href="{{route('manager.managers.index')}}" class="btn btn-outline-secondary">{{ __('Back to List') }}</a>
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
        
        // Confirm hierarchy changes if manager has subordinates
        @if($manager->children()->count() > 0)
        const currentLevel = "{{ $manager->level_name }}";
        const currentParent = "{{ $manager->parent_id }}";
        
        if (levelSelect.value !== currentLevel || parentSelect.value !== currentParent) {
            if (!confirm('This manager has subordinates. Changing hierarchy settings may affect their permissions. Are you sure you want to continue?')) {
                event.preventDefault();
                return false;
            }
        }
        @endif
    });

    // Dynamic validation based on level selection
    document.getElementById('level_name').addEventListener('change', function() {
        // Future enhancement: validate parent-child relationship based on level
    });
</script>
@endsection

@endsection
