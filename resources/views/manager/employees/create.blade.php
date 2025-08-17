@extends('layouts.manager.master')

@section('title', 'Add Employee')

@section('content-header')
    Employees / <span class="text-primary">Add New Employee</span>
@endsection

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Employee Information</h5>
                <a href="{{ route('manager.employees.index') }}" class="btn btn-outline-secondary">
                    <i class="ti ti-arrow-left me-1"></i>Back to List
                </a>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('manager.employees.store') }}">
                    @csrf
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label">Full Name *</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label">Email Address *</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                   id="email" name="email" value="{{ old('email') }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="phone" class="form-label">Phone Number *</label>
                            <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                   id="phone" name="phone" value="{{ old('phone') }}" required>
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="designation" class="form-label">Designation *</label>
                            <input type="text" class="form-control @error('designation') is-invalid @enderror" 
                                   id="designation" name="designation" value="{{ old('designation') }}" required>
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
                                            {{ old('manager_id', $manager->id) == $availableManager->id ? 'selected' : '' }}
                                            data-level="{{ $availableManager->level_name }}">
                                        {{ str_repeat('—', ($availableManager->depth ?? 0) - ($manager->depth ?? 0)) }}
                                        {{ $availableManager->name }}
                                        @if($availableManager->level_name)
                                            ({{ $availableManager->level_name }})
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text">Employee will be assigned to the selected manager in the hierarchy</div>
                            @error('manager_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status">
                                <option value="1" {{ old('status', '1') == '1' ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>Inactive</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="address" class="form-label">Address</label>
                        <textarea class="form-control @error('address') is-invalid @enderror" 
                                  id="address" name="address" rows="3">{{ old('address') }}</textarea>
                        @error('address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('manager.employees.index') }}" class="btn btn-outline-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="ti ti-plus me-1"></i>Create Employee
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0">Hierarchy Context</h6>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="avatar flex-shrink-0 me-3">
                        <span class="avatar-initial rounded bg-label-primary">
                            <i class="ti ti-user-check"></i>
                        </span>
                    </div>
                    <div>
                        <p class="mb-0">Your Level</p>
                        <h6 class="mb-0">{{ auth('manager')->user()->level_name ?? 'Manager' }}</h6>
                    </div>
                </div>
                
                <div class="d-flex align-items-center mb-3">
                    <div class="avatar flex-shrink-0 me-3">
                        <span class="avatar-initial rounded bg-label-info">
                            <i class="ti ti-users"></i>
                        </span>
                    </div>
                    <div>
                        <p class="mb-0">Direct Employees</p>
                        <h6 class="mb-0">{{ auth('manager')->user()->employees()->count() }}</h6>
                    </div>
                </div>

                <div class="d-flex align-items-center mb-3">
                    <div class="avatar flex-shrink-0 me-3">
                        <span class="avatar-initial rounded bg-label-success">
                            <i class="ti ti-sitemap"></i>
                        </span>
                    </div>
                    <div>
                        <p class="mb-0">Available Managers</p>
                        <h6 class="mb-0">{{ count($availableManagers) }}</h6>
                    </div>
                </div>

                <hr>
                <div class="alert alert-info p-3">
                    <i class="ti ti-info-circle me-2"></i>
                    <small>You can assign employees to yourself or any manager in your hierarchy.</small>
                </div>
                
                <small class="text-muted">* Required fields must be filled</small>
            </div>
        </div>
    </div>
</div>
@endsection
