@extends('layouts.manager.master')

@section('title', 'Edit Employee')

@section('content-header')
    Employees / <span class="text-primary">Edit {{ $employee->name }}</span>
@endsection

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Edit Employee Information</h5>
                <div class="d-flex gap-2">
                    <a href="{{ route('manager.employees.show', $employee) }}" class="btn btn-outline-info">
                        <i class="ti ti-eye me-1"></i>View Details
                    </a>
                    <a href="{{ route('manager.employees.index') }}" class="btn btn-outline-secondary">
                        <i class="ti ti-arrow-left me-1"></i>Back to List
                    </a>
                </div>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('manager.employees.update', $employee) }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label">Full Name *</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name', $employee->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label">Email Address *</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                   id="email" name="email" value="{{ old('email', $employee->email) }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="phone" class="form-label">Phone Number *</label>
                            <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                   id="phone" name="phone" value="{{ old('phone', $employee->phone) }}" required>
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="designation" class="form-label">Designation *</label>
                            <input type="text" class="form-control @error('designation') is-invalid @enderror" 
                                   id="designation" name="designation" value="{{ old('designation', $employee->designation) }}" required>
                            @error('designation')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="manager_id" class="form-label">Assign to Manager *</label>
                            <select class="form-select @error('manager_id') is-invalid @enderror" id="manager_id" name="manager_id" required>
                                <option value="">Choose Manager...</option>
                                @foreach($availableManagers as $availableManager)
                                    <option value="{{ $availableManager->id }}" 
                                            {{ old('manager_id', $employee->manager_id) == $availableManager->id ? 'selected' : '' }}
                                            data-level="{{ $availableManager->level_name }}">
                                        {{ str_repeat('—', ($availableManager->depth ?? 0) - ($manager->depth ?? 0)) }}
                                        {{ $availableManager->name }}
                                        @if($availableManager->level_name)
                                            ({{ $availableManager->level_name }})
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text">
                                Current: {{ $employee->manager ? $employee->manager->name : 'Not Assigned' }}
                                @if($employee->manager && $employee->manager->level_name)
                                    ({{ $employee->manager->level_name }})
                                @endif
                            </div>
                            @error('manager_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status">
                                <option value="1" {{ old('status', $employee->status) == '1' ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ old('status', $employee->status) == '0' ? 'selected' : '' }}>Inactive</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="address" class="form-label">Address</label>
                        <textarea class="form-control @error('address') is-invalid @enderror" 
                                  id="address" name="address" rows="3">{{ old('address', $employee->address) }}</textarea>
                        @error('address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('manager.employees.index') }}" class="btn btn-outline-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="ti ti-check me-1"></i>Update Employee
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0">Employee Summary</h6>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="avatar flex-shrink-0 me-3">
                        <span class="avatar-initial rounded bg-label-primary">
                            {{ substr($employee->name, 0, 1) }}
                        </span>
                    </div>
                    <div>
                        <h6 class="mb-0">{{ $employee->name }}</h6>
                        <small class="text-muted">{{ $employee->designation }}</small>
                    </div>
                </div>
                
                <div class="d-flex align-items-center mb-3">
                    <div class="avatar flex-shrink-0 me-3">
                        <span class="avatar-initial rounded bg-label-info">
                            <i class="ti ti-user-check"></i>
                        </span>
                    </div>
                    <div>
                        <p class="mb-0">Current Manager</p>
                        <h6 class="mb-0">
                            {{ $employee->manager ? $employee->manager->name : 'Not Assigned' }}
                        </h6>
                        @if($employee->manager && $employee->manager->level_name)
                            <small class="text-muted">{{ $employee->manager->level_name }}</small>
                        @endif
                    </div>
                </div>

                <div class="d-flex align-items-center mb-3">
                    <div class="avatar flex-shrink-0 me-3">
                        <span class="avatar-initial rounded bg-label-success">
                            <i class="ti ti-building"></i>
                        </span>
                    </div>
                    <div>
                        <p class="mb-0">Assigned Agencies</p>
                        <h6 class="mb-0">{{ $employee->agencies()->count() }}</h6>
                    </div>
                </div>

                <div class="d-flex align-items-center mb-3">
                    <div class="avatar flex-shrink-0 me-3">
                        <span class="avatar-initial rounded bg-label-warning">
                            <i class="ti ti-calendar"></i>
                        </span>
                    </div>
                    <div>
                        <p class="mb-0">Joined</p>
                        <h6 class="mb-0">{{ $employee->created_at->format('M d, Y') }}</h6>
                    </div>
                </div>

                <hr>
                
                @if($employee->agencies()->count() > 0)
                    <div class="alert alert-warning p-3">
                        <i class="ti ti-alert-triangle me-2"></i>
                        <small>This employee has {{ $employee->agencies()->count() }} assigned agencies. Changing manager may affect agency access.</small>
                    </div>
                @endif
                
                <small class="text-muted">* Required fields must be filled</small>
            </div>
        </div>

        <!-- Manager Reassignment Warning -->
        @if($employee->manager_id !== $manager->id)
        <div class="card mt-3">
            <div class="card-header">
                <h6 class="card-title text-warning mb-0">
                    <i class="ti ti-alert-triangle me-1"></i>Manager Reassignment
                </h6>
            </div>
            <div class="card-body">
                <p class="text-muted small mb-2">
                    This employee is currently managed by <strong>{{ $employee->manager->name }}</strong>. 
                    You can reassign them to any manager in your accessible hierarchy.
                </p>
                <div class="alert alert-info p-2">
                    <small>Available managers: {{ count($availableManagers) }}</small>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
