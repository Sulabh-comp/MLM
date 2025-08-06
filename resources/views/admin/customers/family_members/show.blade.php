@extends('layouts.admin.master')

@section('title', 'Family Members')

@section('content-header', __('Family Members'))

@section('breadcrumbs')
<li class="breadcrumb-item">
  <a href="{{ route('admin.customers.index') }}">{{ __('Customers') }}</a>
</li>
<li class="breadcrumb-item">
  <a href="{{ route('admin.customers.show', $familyMember->customer) }}">{{ __('View Customer') }} {{ $familyMember->customer->first_name }} {{ $familyMember->customer->last_name }}</a>
</li>
<li class="breadcrumb-item active">
  {{ __('View Family Member') }}
</li>
@endsection

@section('content')
<div class="card">
  <div class="card-header header-elements d-flex justify-content-between align-items-center">
    <h5 class="m-0 me-2">{{ __('Family Member') }}</h5>
    <div class="form d-flex align-items-center">
      <a href="{{ route('admin.family-members.edit', $familyMember) }}" class="btn btn-primary" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="tooltip-primary" title="{{ __('Edit') }}">
        <i class="fa-solid fa-pen-to-square"></i>{{ __('Edit') }}
      </a>
      <a href="javascript:void(0)" class="btn btn-danger ml-3" onclick="deleteData({{ $familyMember->id }})" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="tooltip-danger" title="{{ __('Delete') }}">
        <i class="fa-solid fa-trash"></i>{{ __('Delete') }}
      </a>
    </div>
  </div>
  <div class="card-body">
    <div class="row">
      <div class="col-md-6">
        <h6>{{ __('Family Member Code') }}</h6>
        <p><span class="badge bg-info">{{ $familyMember->code ?? 'N/A' }}</span></p>
      </div>
      <div class="col-md-6">
        <h6>{{ __('Name') }}</h6>
        <p>{{ $familyMember->name }}</p>
      </div>
      <div class="col-md-6">
        <h6>{{ __('Position') }}</h6>
        <p>{{ $familyMember->position }}</p>
      </div>
      <div class="col-md-6">
        <h6>{{ __('Age') }}</h6>
        <p>{{ $familyMember->age }}</p>
      </div>
      <div class="col-md-6">
        <h6>{{ __('Gender') }}</h6>
        <p>{{ $familyMember->gender }}</p>
      </div>
      <div class="col-md-6">
        <h6>{{ __('Occupation') }}</h6>
        <p>{{ $familyMember->occupation }}</p>
      </div>
      <div class="col-md-6">
        <h6>{{ __('Contact Number') }}</h6>
        <p>{{ $familyMember->contact_number }}</p>
      </div>
      <div class="col-md-6">
        <h6>{{ __('Monthly Income') }}</h6>
        <p>{{ $familyMember->monthly_income }}</p>
      </div>
      <div class="col-md-6">
        <h6>{{ __('Health Status') }}</h6>
        <p>{{ $familyMember->health_status ? 'Good' : 'Poor' }}</p>
      </div>
      <div class="col-md-6">
        <h6>{{ __('Disease Name') }}</h6>
        <p>{{ $familyMember->disease_name ?? 'N/A' }}</p>
      </div>
      <div class="col-md-6">
        <h6>{{ __('Medicine Expenses') }}</h6>
        <p>{{ $familyMember->medicine_expenses ?? 'N/A' }}</p>
      </div>
      <div class="col-md-6">
        <h6>{{ __('Medicine Name') }}</h6>
        <p>{{ $familyMember->medicine_name ?? 'N/A' }}</p>
      </div>
      <div class="col-md-6">
        <h6>{{ __('Doctor Name') }}</h6>
        <p>{{ $familyMember->doctor_name ?? 'N/A' }}</p>
      </div>
      <div class="col-md-6">
        <h6>{{ __('Skill Knowledge') }}</h6>
        <p>{{ $familyMember->skill_knowledge ? 'Yes' : 'No' }}</p>
      </div>
      <div class="col-md-6">
        <h6>{{ __('Skill Name') }}</h6>
        <p>{{ $familyMember->skill_name ?? 'N/A' }}</p>
      </div>
      <div class="col-md-6">
        <h6>{{ __('Institute Certified') }}</h6>
        <p>{{ $familyMember->institute_certified ?? 'N/A' }}</p>
      </div>
      <div class="col-md-6">
        <h6>{{ __('Year of Passing') }}</h6>
        <p>{{ $familyMember->year_of_passing ?? 'N/A' }}</p>
      </div>
      <div class="col-md-6">
        <h6>{{ __('Degree/Course') }}</h6>
        <p>{{ $familyMember->degree_course ?? 'N/A' }}</p>
      </div>
      <div class="col-md-6">
        <h6>{{ __('Professional Courses') }}</h6>
        <p>{{ $familyMember->professional_courses ?? 'N/A' }}</p>
      </div>
      <div class="col-md-6">
        <h6>{{ __('Course Name') }}</h6>
        <p>{{ $familyMember->course_name ?? 'N/A' }}</p>
      </div>
      <div class="col-md-6">
        <h6>{{ __('Institute Name') }}</h6>
        <p>{{ $familyMember->institute_name ?? 'N/A' }}</p>
      </div>
      <div class="col-md-6">
        <h6>{{ __('Work City') }}</h6>
        <p>{{ $familyMember->work_city ?? 'N/A' }}</p>
      </div>
      <div class="col-md-6">
        <h6>{{ __('Looking for Opportunity') }}</h6>
        <p>{{ $familyMember->looking_for_opportunity ? 'Yes' : 'No' }}</p>
      </div>
      <div class="col-md-6">
        <h6>{{ __('MLM') }}</h6>
        <p>{{ $familyMember->mlm ? 'Yes' : 'No' }}</p>
      </div>
      <div class="col-md-6">
        <h6>{{ __('Sales & Marketing') }}</h6>
        <p>{{ $familyMember->sales_marketing ? 'Yes' : 'No' }}</p>
      </div>
      <div class="col-md-6">
        <h6>{{ __('Partner Commission Work') }}</h6>
        <p>{{ $familyMember->partner_commission_work ? 'Yes' : 'No' }}</p>
      </div>
      <div class="col-md-6">
        <h6>{{ __('Manufacturing Work') }}</h6>
        <p>{{ $familyMember->manufacturing_work ? 'Yes' : 'No' }}</p>
      </div>
      <div class="col-md-6">
        <h6>{{ __('Commission Work') }}</h6>
        <p>{{ $familyMember->commission_work ? 'Yes' : 'No' }}</p>
      </div>
      <div class="col-md-6">
        <h6>{{ __('Created At') }}</h6>
        <p>{{ $familyMember->created_at->format('d M, Y') }}</p>
      </div>
    </div>
  </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="deleteForm" method="POST" action="">
        @csrf
        @method('DELETE')
        <div class="modal-header">
          <h5 class="modal-title" id="deleteModalLabel">{{ __('Delete') }}</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <p>{{ __('Are you sure you want to delete?') }}</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
          <button type="submit" class="btn btn-danger">{{ __('Delete') }}</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
  function deleteData(id) {
    document.getElementById('deleteForm').action = `${id}`;
    $('#deleteModal').modal('show');
  }
</script>
@endsection
