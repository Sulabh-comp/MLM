@extends('layouts.manager.master')

@section('title', 'Add Agency')

@section('content-header')
    Agencies / <span class="text-primary">Add New Agency</span>
@endsection

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Agency Information</h5>
                <a href="{{ route('manager.agencies.index') }}" class="btn btn-outline-secondary">
                    <i class="ti ti-arrow-left me-1"></i>Back to List
                </a>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('manager.agencies.store') }}">
                    @csrf
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label">Agency Name *</label>
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
                            <label for="employee_id" class="form-label">Assign Employee *</label>
                            <select class="form-select @error('employee_id') is-invalid @enderror" id="employee_id" name="employee_id" required>
                                <option value="">Select Employee</option>
                                @foreach($employees as $employee)
                                    <option value="{{ $employee->id }}" {{ old('employee_id') == $employee->id ? 'selected' : '' }}>
                                        {{ $employee->name }} ({{ $employee->designation }})
                                    </option>
                                @endforeach
                            </select>
                            @error('employee_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="license_number" class="form-label">License Number</label>
                            <input type="text" class="form-control @error('license_number') is-invalid @enderror" 
                                   id="license_number" name="license_number" value="{{ old('license_number') }}">
                            @error('license_number')
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

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" name="description" rows="3">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('manager.agencies.index') }}" class="btn btn-outline-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="ti ti-plus me-1"></i>Create Agency
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0">Quick Info</h6>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="avatar flex-shrink-0 me-3">
                        <span class="avatar-initial rounded bg-label-info">
                            <i class="ti ti-map-pin"></i>
                        </span>
                    </div>
                    <div>
                        <p class="mb-0">Region</p>
                        <h6 class="mb-0">{{ auth('manager')->user()->region->name }}</h6>
                    </div>
                </div>
                
                <div class="d-flex align-items-center mb-3">
                    <div class="avatar flex-shrink-0 me-3">
                        <span class="avatar-initial rounded bg-label-success">
                            <i class="ti ti-building"></i>
                        </span>
                    </div>
                    <div>
                        <p class="mb-0">Current Agencies</p>
                        <h6 class="mb-0">{{ App\Models\Agency::whereHas('employee', function($q) { $q->where('region_id', auth('manager')->user()->region_id); })->count() }}</h6>
                    </div>
                </div>

                <div class="d-flex align-items-center mb-3">
                    <div class="avatar flex-shrink-0 me-3">
                        <span class="avatar-initial rounded bg-label-primary">
                            <i class="ti ti-users"></i>
                        </span>
                    </div>
                    <div>
                        <p class="mb-0">Available Employees</p>
                        <h6 class="mb-0">{{ $employees->count() }}</h6>
                    </div>
                </div>

                <hr>
                <small class="text-muted">* Required fields must be filled</small>
            </div>
        </div>
    </div>
</div>
@endsection
