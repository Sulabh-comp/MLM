@extends('layouts.agency.master')

@section('title', 'Profile Management')

@section('content-header', 'Profile Management')

@section('breadcrumbs')
<li class="breadcrumb-item active">
    Profile
</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-8">
        <!-- Basic Information Card -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Basic Information</h5>
                <span class="badge bg-label-{{ $agency->status ? 'success' : 'warning' }}">
                    {{ $agency->status ? 'Active' : 'Pending Approval' }}
                </span>
            </div>
            <form action="{{ route('agency.profile.update') }}" method="POST">
                @csrf
                <div class="card-body">
                    <div class="row">
                        <div class="mb-3 col-md-6">
                            <label for="name" class="form-label">Agency Name</label>
                            <input type="text" class="form-control" id="name" name="name" 
                                   value="{{ old('name', $agency->name) }}" required>
                            @error('name')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3 col-md-6">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control" id="email" 
                                   value="{{ $agency->email }}" disabled>
                            <small class="text-muted">Email cannot be changed</small>
                        </div>
                        <div class="mb-3 col-md-6">
                            <label for="phone" class="form-label">Phone Number</label>
                            <input type="text" class="form-control" id="phone" name="phone" 
                                   value="{{ old('phone', $agency->phone) }}" required>
                            @error('phone')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3 col-md-12">
                            <label for="address" class="form-label">Address</label>
                            <textarea class="form-control" id="address" name="address" rows="3" required>{{ old('address', $agency->address) }}</textarea>
                            @error('address')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
        </div>

        <!-- Bank Details Card -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Bank Details</h5>
                @if($agency->documents_verified)
                    <span class="badge bg-success">
                        <i class="ti ti-check me-1"></i>Verified
                    </span>
                @elseif($agency->documents_submitted_at)
                    <span class="badge bg-warning">
                        <i class="ti ti-clock me-1"></i>Under Review
                    </span>
                @else
                    <span class="badge bg-secondary">
                        <i class="ti ti-alert-circle me-1"></i>Not Submitted
                    </span>
                @endif
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="mb-3 col-md-6">
                        <label for="bank_name" class="form-label">Bank Name</label>
                        <input type="text" class="form-control" id="bank_name" name="bank_name" 
                               value="{{ old('bank_name', $agency->bank_name) }}" 
                               placeholder="e.g., State Bank of India">
                        @error('bank_name')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3 col-md-6">
                        <label for="account_holder_name" class="form-label">Account Holder Name</label>
                        <input type="text" class="form-control" id="account_holder_name" name="account_holder_name" 
                               value="{{ old('account_holder_name', $agency->account_holder_name) }}" 
                               placeholder="As per bank records">
                        @error('account_holder_name')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3 col-md-6">
                        <label for="account_number" class="form-label">Account Number</label>
                        <input type="text" class="form-control" id="account_number" name="account_number" 
                               value="{{ old('account_number', $agency->account_number) }}" 
                               placeholder="Enter account number">
                        @error('account_number')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3 col-md-6">
                        <label for="ifsc_code" class="form-label">IFSC Code</label>
                        <input type="text" class="form-control" id="ifsc_code" name="ifsc_code" 
                               value="{{ old('ifsc_code', $agency->ifsc_code) }}" 
                               placeholder="e.g., SBIN0001234" style="text-transform: uppercase;">
                        @error('ifsc_code')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3 col-md-12">
                        <label for="branch_name" class="form-label">Branch Name</label>
                        <input type="text" class="form-control" id="branch_name" name="branch_name" 
                               value="{{ old('branch_name', $agency->branch_name) }}" 
                               placeholder="Enter branch name">
                        @error('branch_name')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Identity Documents Card -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Identity Documents</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="mb-3 col-md-6">
                        <label for="aadhar_number" class="form-label">Aadhar Number</label>
                        <input type="text" class="form-control" id="aadhar_number" name="aadhar_number" 
                               value="{{ old('aadhar_number', $agency->aadhar_number) }}" 
                               placeholder="Enter 12-digit Aadhar number" maxlength="12">
                        <small class="text-muted">12-digit number only</small>
                        @error('aadhar_number')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3 col-md-6">
                        <label for="pan_number" class="form-label">PAN Number</label>
                        <input type="text" class="form-control" id="pan_number" name="pan_number" 
                               value="{{ old('pan_number', $agency->pan_number) }}" 
                               placeholder="e.g., ABCDE1234F" maxlength="10" style="text-transform: uppercase;">
                        <small class="text-muted">Format: ABCDE1234F</small>
                        @error('pan_number')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="card-footer">
            <button type="submit" class="btn btn-primary me-2">
                <i class="ti ti-device-floppy me-1"></i>Update Profile
            </button>
            <a href="{{ route('agency.dashboard') }}" class="btn btn-outline-secondary">
                <i class="ti ti-arrow-left me-1"></i>Back to Dashboard
            </a>
        </div>
        </form>
    </div>

    <div class="col-md-4">
        <!-- Verification Status Card -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="card-title mb-0">Verification Status</h6>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="avatar flex-shrink-0 me-3">
                        <span class="avatar-initial rounded bg-label-{{ $agency->status ? 'success' : 'warning' }}">
                            <i class="ti ti-{{ $agency->status ? 'check' : 'clock' }}"></i>
                        </span>
                    </div>
                    <div>
                        <p class="mb-0">Account Status</p>
                        <h6 class="mb-0">{{ $agency->status ? 'Active' : 'Pending Approval' }}</h6>
                    </div>
                </div>

                <div class="d-flex align-items-center mb-3">
                    <div class="avatar flex-shrink-0 me-3">
                        <span class="avatar-initial rounded bg-label-{{ $agency->documents_verified ? 'success' : ($agency->documents_submitted_at ? 'warning' : 'secondary') }}">
                            <i class="ti ti-{{ $agency->documents_verified ? 'shield-check' : ($agency->documents_submitted_at ? 'clock' : 'shield-x') }}"></i>
                        </span>
                    </div>
                    <div>
                        <p class="mb-0">Document Status</p>
                        <h6 class="mb-0">
                            @if($agency->documents_verified)
                                Verified
                            @elseif($agency->documents_submitted_at)
                                Under Review
                            @else
                                Not Submitted
                            @endif
                        </h6>
                    </div>
                </div>

                @if($agency->documents_submitted_at)
                    <div class="d-flex align-items-center">
                        <div class="avatar flex-shrink-0 me-3">
                            <span class="avatar-initial rounded bg-label-info">
                                <i class="ti ti-calendar"></i>
                            </span>
                        </div>
                        <div>
                            <p class="mb-0">Submitted On</p>
                            <h6 class="mb-0">{{ $agency->documents_submitted_at ? $agency->documents_submitted_at->format('M d, Y') : 'Not submitted' }}</h6>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Required Documents Info -->
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="ti ti-info-circle me-2"></i>Required Information
                </h6>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <h6 class="alert-heading">Document Requirements:</h6>
                    <ul class="mb-0">
                        <li>Valid Bank Account Details</li>
                        <li>12-digit Aadhar Number</li>
                        <li>Valid PAN Number</li>
                        <li>All information must be accurate</li>
                    </ul>
                </div>
                
                <div class="alert alert-warning">
                    <h6 class="alert-heading">Note:</h6>
                    <p class="mb-0">Documents will be verified by admin. Any incorrect information may delay the verification process.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Format Aadhar number input
    document.getElementById('aadhar_number').addEventListener('input', function(e) {
        this.value = this.value.replace(/\D/g, '');
    });

    // Format PAN number input
    document.getElementById('pan_number').addEventListener('input', function(e) {
        this.value = this.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
    });

    // Format IFSC code input
    document.getElementById('ifsc_code').addEventListener('input', function(e) {
        this.value = this.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
    });
</script>
@endsection
