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
                    <div class="mb-3 col-6">
                            <label for="level_name" class="form-label">{{ __('Manager Level') }}</label>
                            <select class="form-select" id="level_name" name="level_name" required>
                                <option value="">{{ __('Select Manager Level') }}</option>
                                @foreach(\App\Models\ManagerLevel::active()->orderBy('hierarchy_level')->get() as $level)
                                    <option value="{{ $level->name }}" 
                                            data-hierarchy="{{ $level->hierarchy_level }}"
                                            @selected($level->name == old('level_name'))>
                                        {{ $level->name }} ({{ $level->code }}) - Level {{ $level->hierarchy_level }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text">
                                <small id="hierarchy-info" class="text-muted"></small>
                            </div>
                    </div>
                    <div class="mb-3 col-6">
                            <label for="parent_id" class="form-label">{{ __('Reports To (Parent Manager)') }}</label>
                            <select class="form-select" id="parent_id" name="parent_id">
                                <option value="">{{ __('No Parent (Top Level)') }}</option>
                                @foreach(\App\Models\Manager::with('managerLevel')->orderBy('hierarchy_path')->get() as $manager)
                                    <option value="{{ $manager->id }}" 
                                            data-level="{{ $manager->managerLevel ? $manager->managerLevel->hierarchy_level : 999 }}"
                                            data-status="{{ $manager->status }}"
                                            @selected($manager->id == old('parent_id'))>
                                        {{ str_repeat('—', $manager->depth) }} {{ $manager->name }} 
                                        @if($manager->level_name) ({{ $manager->level_name }}) @endif
                                        @if(!$manager->status) - INACTIVE @endif
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text">
                                <small id="parent-info" class="text-info"></small>
                            </div>
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
    document.addEventListener('DOMContentLoaded', function() {
        const levelSelect = document.getElementById('level_name');
        const parentSelect = document.getElementById('parent_id');
        const hierarchyInfo = document.getElementById('hierarchy-info');
        const parentInfo = document.getElementById('parent-info');

        // Auto-assign parent when level changes
        levelSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            if (!selectedOption.value) {
                hierarchyInfo.textContent = '';
                parentInfo.textContent = '';
                return;
            }

            const levelName = selectedOption.value;
            getRecommendedParent(levelName);
        });

        function getRecommendedParent(levelName) {
            // Show loading state
            parentInfo.textContent = 'Finding recommended parent...';
            parentInfo.className = 'text-muted';
            
            // Make AJAX request to get recommended parent
            fetch(`{{ route('admin.managers.recommended-parent') }}?level_name=${encodeURIComponent(levelName)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.auto_assigned && data.parent_id) {
                        parentSelect.value = data.parent_id;
                        parentInfo.textContent = data.message;
                        parentInfo.className = 'text-success';
                        
                        // Show hierarchy information
                        hierarchyInfo.textContent = `Level hierarchy rule applied: Parent assigned automatically`;
                    } else {
                        parentSelect.value = '';
                        parentInfo.textContent = data.message;
                        parentInfo.className = data.auto_assigned === false ? 'text-warning' : 'text-info';
                        
                        // Show hierarchy information based on level
                        if (levelName) {
                            const selectedOption = levelSelect.options[levelSelect.selectedIndex];
                            const selectedLevel = parseInt(selectedOption.dataset.hierarchy);
                            
                            if (selectedLevel === 1) {
                                hierarchyInfo.textContent = 'Level 1 managers are top-level (no parent required)';
                            } else {
                                const requiredParentLevel = selectedLevel - 1;
                                hierarchyInfo.textContent = `Level ${selectedLevel} requires a parent from Level ${requiredParentLevel} or above`;
                            }
                        }
                    }
                })
                .catch(error => {
                    console.error('Error fetching recommended parent:', error);
                    parentInfo.textContent = 'Error getting recommendation. Please select manually.';
                    parentInfo.className = 'text-danger';
                });
        }

        // Form validation
        document.getElementById("managerForm").addEventListener("submit", function(event) {
            const selectedLevel = levelSelect.options[levelSelect.selectedIndex];
            if (!selectedLevel.value) {
                alert('Please select a manager level');
                event.preventDefault();
                return;
            }

            const targetLevel = parseInt(selectedLevel.dataset.hierarchy);
            if (targetLevel > 1 && !parentSelect.value) {
                const confirmSubmit = confirm(
                    'No parent manager assigned for this level. ' +
                    'The manager will be created without a parent. Continue?'
                );
                if (!confirmSubmit) {
                    event.preventDefault();
                }
            }
        });

        // Trigger auto-assignment if level is pre-selected (e.g., from old input)
        if (levelSelect.value) {
            const levelName = levelSelect.value;
            getRecommendedParent(levelName);
        }
    });
    </script>
@endsection
@endsection
