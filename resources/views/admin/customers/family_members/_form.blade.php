<form id="familyMemberForm" class="card-body" action="{{ $_route }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method($_method)
    <hr class="mt-0" />
    <div class="row">
        <div class="mb-3 col-6">
            <label for="customer_id" class="form-label">{{ __('Customer') }}</label>
            <select class="form-select" id="customer_id" name="customer_id" required>
                <option value="{{ $customer->id }}">{{ $customer->first_name }} {{ $customer->last_name }}</option>
            </select>
        </div>
        <div class="mb-3 col-6">
            <label for="name" class="form-label">{{ __('Name') }}</label>
            <input type="text" class="form-control" id="name" name="name" placeholder="{{ __('Name') }}" value="{{ old('name') ?? $familyMember->name }}">
        </div>
        <div class="mb-3 col-6">
            <label for="position" class="form-label">{{ __('Position') }}</label>
            <input type="text" class="form-control" id="position" name="position" placeholder="{{ __('Position') }}" value="{{ old('position') ?? $familyMember->position }}">
        </div>
        <div class="mb-3 col-6">
            <label for="age" class="form-label">{{ __('Age') }}</label>
            <input type="text" class="form-control" id="age" name="age" placeholder="{{ __('Age') }}" value="{{ old('age') ?? $familyMember->age }}">
        </div>
        <div class="mb-3 col-6">
            <label for="gender" class="form-label">{{ __('Gender') }}</label>
            <select class="form-select" id="gender" name="gender">
                <option value="">{{ __('Select Gender') }}</option>
                <option value="Male" @selected($familyMember->gender == 'Male')>{{ __('Male') }}</option>
                <option value="Female" @selected($familyMember->gender == 'Female')>{{ __('Female') }}</option>
                <option value="Other" @selected($familyMember->gender == 'Other')>{{ __('Other') }}</option>
            </select>
        </div>
        <div class="mb-3 col-6">
            <label for="occupation" class="form-label">{{ __('Occupation') }}</label>
            <input type="text" class="form-control" id="occupation" name="occupation" placeholder="{{ __('Occupation') }}" value="{{ old('occupation') ?? $familyMember->occupation }}">
        </div>
        <div class="mb-3 col-6">
            <label for="contact_number" class="form-label">{{ __('Contact Number') }}</label>
            <input type="text" class="form-control" id="contact_number" name="contact_number" placeholder="{{ __('Contact Number') }}" value="{{ old('contact_number') ?? $familyMember->contact_number }}">
        </div>
        <div class="mb-3 col-6">
            <label for="monthly_income" class="form-label">{{ __('Monthly Income') }}</label>
            <input type="text" class="form-control" id="monthly_income" name="monthly_income" placeholder="{{ __('Monthly Income') }}" value="{{ old('monthly_income') ?? $familyMember->monthly_income }}">
        </div>
        <div class="mb-3 col-6">
            <label for="health_status" class="form-label">{{ __('Health Status') }}</label>
            <select class="form-select" id="health_status" name="health_status">
                <option value="1" @selected($familyMember->health_status == 1)>{{ __('Good') }}</option>
                <option value="0" @selected($familyMember->health_status == 0)>{{ __('Poor') }}</option>
            </select>
        </div>
        <div class="mb-3 col-6">
            <label for="disease_name" class="form-label">{{ __('Disease Name') }}</label>
            <input type="text" class="form-control" id="disease_name" name="disease_name" placeholder="{{ __('Disease Name') }}" value="{{ old('disease_name') ?? $familyMember->disease_name }}">
        </div>
        <div class="mb-3 col-6">
            <label for="medicine_expenses" class="form-label">{{ __('Medicine Expenses') }}</label>
            <input type="text" class="form-control" id="medicine_expenses" name="medicine_expenses" placeholder="{{ __('Medicine Expenses') }}" value="{{ old('medicine_expenses') ?? $familyMember->medicine_expenses }}">
        </div>
        <div class="mb-3 col-6">
            <label for="medicine_name" class="form-label">{{ __('Medicine Name') }}</label>
            <input type="text" class="form-control" id="medicine_name" name="medicine_name" placeholder="{{ __('Medicine Name') }}" value="{{ old('medicine_name') ?? $familyMember->medicine_name }}">
        </div>
        <div class="mb-3 col-6">
            <label for="doctor_name" class="form-label">{{ __('Doctor Name') }}</label>
            <input type="text" class="form-control" id="doctor_name" name="doctor_name" placeholder="{{ __('Doctor Name') }}" value="{{ old('doctor_name') ?? $familyMember->doctor_name }}">
        </div>
        <div class="mb-3 col-6">
            <label for="skill_knowledge" class="form-label">{{ __('Skill Knowledge') }}</label>
            <select class="form-select" id="skill_knowledge" name="skill_knowledge">
                <option value="1" @selected($familyMember->skill_knowledge == 1)>{{ __('Yes') }}</option>
                <option value="0" @selected($familyMember->skill_knowledge == 0)>{{ __('No') }}</option>
            </select>
        </div>
        <div class="mb-3 col-6">
            <label for="skill_name" class="form-label">{{ __('Skill Name') }}</label>
            <input type="text" class="form-control" id="skill_name" name="skill_name" placeholder="{{ __('Skill Name') }}" value="{{ old('skill_name') ?? $familyMember->skill_name }}">
        </div>
        <div class="mb-3 col-6">
            <label for="institute_certified" class="form-label">{{ __('Institute Certified') }}</label>
            <input type="text" class="form-control" id="institute_certified" name="institute_certified" placeholder="{{ __('Institute Certified') }}" value="{{ old('institute_certified') ?? $familyMember->institute_certified }}">
        </div>
        <div class="mb-3 col-6">
            <label for="year_of_passing" class="form-label">{{ __('Year of Passing') }}</label>
            <input type="text" class="form-control" id="year_of_passing" name="year_of_passing" placeholder="{{ __('Year of Passing') }}" value="{{ old('year_of_passing') ?? $familyMember->year_of_passing }}">
        </div>
        <div class="mb-3 col-6">
            <label for="degree_course" class="form-label">{{ __('Degree/Course') }}</label>
            <input type="text" class="form-control" id="degree_course" name="degree_course" placeholder="{{ __('Degree/Course') }}" value="{{ old('degree_course') ?? $familyMember->degree_course }}">
        </div>
        <div class="mb-3 col-6">
            <label for="professional_courses" class="form-label">{{ __('Professional Courses') }}</label>
            <input type="text" class="form-control" id="professional_courses" name="professional_courses" placeholder="{{ __('Professional Courses') }}" value="{{ old('professional_courses') ?? $familyMember->professional_courses }}">
        </div>
        <div class="mb-3 col-6">
            <label for="course_name" class="form-label">{{ __('Course Name') }}</label>
            <input type="text" class="form-control" id="course_name" name="course_name" placeholder="{{ __('Course Name') }}" value="{{ old('course_name') ?? $familyMember->course_name }}">
        </div>
        <div class="mb-3 col-6">
            <label for="institute_name" class="form-label">{{ __('Institute Name') }}</label>
            <input type="text" class="form-control" id="institute_name" name="institute_name" placeholder="{{ __('Institute Name') }}" value="{{ old('institute_name') ?? $familyMember->institute_name }}">
        </div>
        <div class="mb-3 col-6">
            <label for="work_city" class="form-label">{{ __('Work City') }}</label>
            <input type="text" class="form-control" id="work_city" name="work_city" placeholder="{{ __('Work City') }}" value="{{ old('work_city') ?? $familyMember->work_city }}">
        </div>
        <div class="mb-3 col-6">
            <label for="looking_for_opportunity" class="form-label">{{ __('Looking for Opportunity') }}</label>
            <select class="form-select" id="looking_for_opportunity" name="looking_for_opportunity">
                <option value="1" @selected($familyMember->looking_for_opportunity == 1)>{{ __('Yes') }}</option>
                <option value="0" @selected($familyMember->looking_for_opportunity == 0)>{{ __('No') }}</option>
            </select>
        </div>
        <div class="mb-3 col-6">
            <label for="mlm" class="form-label">{{ __('MLM') }}</label>
            <select class="form-select" id="mlm" name="mlm">
                <option value="1" @selected($familyMember->mlm == 1)>{{ __('Yes') }}</option>
                <option value="0" @selected($familyMember->mlm == 0)>{{ __('No') }}</option>
            </select>
        </div>
        <div class="mb-3 col-6">
            <label for="sales_marketing" class="form-label">{{ __('Sales & Marketing') }}</label>
            <select class="form-select" id="sales_marketing" name="sales_marketing">
                <option value="1" @selected($familyMember->sales_marketing == 1)>{{ __('Yes') }}</option>
                <option value="0" @selected($familyMember->sales_marketing == 0)>{{ __('No') }}</option>
            </select>
        </div>


        <div class="mb-3 col-6">
            <label for="partner_commission_work" class="form-label">{{ __('Partner Commission Work') }}</label>
            <select class="form-select" id="partner_commission_work" name="partner_commission_work">
                <option value="1" @selected($familyMember->partner_commission_work == 1)>{{ __('Yes') }}</option>
                <option value="0" @selected($familyMember->partner_commission_work == 0)>{{ __('No') }}</option>
            </select>
        </div>
        <div class="mb-3 col-6">
            <label for="manufacturing_work" class="form-label">{{ __('Manufacturing Work') }}</label>
            <select class="form-select" id="manufacturing_work" name="manufacturing_work">
                <option value="1" @selected($familyMember->manufacturing_work == 1)>{{ __('Yes') }}</option>
                <option value="0" @selected($familyMember->manufacturing_work == 0)>{{ __('No') }}</option>
            </select>
        </div>
        <div class="mb-3 col-6">
            <label for="commission_work" class="form-label">{{ __('Commission Work') }}</label>
            <select class="form-select" id="commission_work" name="commission_work">
                <option value="1" @selected($familyMember->commission_work == 1)>{{ __('Yes') }}</option>
                <option value="0" @selected($familyMember->commission_work == 0)>{{ __('No') }}</option>
            </select>
        </div>
    </div>
    <div class="pt-4">
        <button type="submit" class="btn btn-primary me-sm-3 me-1">{{ __('Submit') }}</button>
        <a href="{{ route('admin.family-members.index') }}" class="btn btn-label-secondary">{{ __('Cancel') }}</a>
    </div>
</form>
