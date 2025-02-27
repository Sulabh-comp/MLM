<form id="customerForm" class="card-body" action="{{ $_route }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method($_method)
    <hr class="mt-0" />
    <div class="row">
        <div class="mb-3 col-12">
            <label for="sponcer_code" class="form-label">{{ __('Sponsor Code') }}</label>
            <input type="text" class="form-control" id="sponcer_code" name="sponcer_code" placeholder="{{ __('Sponsor Code') }}" value="{{ old('sponcer_code') ?? $customer->sponcer_code }}">
        </div>
        <div class="mb-3 col-6">
            <label for="first_name" class="form-label">{{ __('First Name') }}</label>
            <input type="text" class="form-control" id="first_name" name="first_name" placeholder="{{ __('First Name') }}" value="{{ old('first_name') ?? $customer->first_name }}">
        </div>
        <div class="mb-3 col-6">
            <label for="last_name" class="form-label">{{ __('Last Name') }}</label>
            <input type="text" class="form-control" id="last_name" name="last_name" placeholder="{{ __('Last Name') }}" value="{{ old('last_name') ?? $customer->last_name }}">
        </div>
        <div class="mb-3 col-6">
            <label for="address_1" class="form-label">{{ __('Address Line 1') }}</label>
            <input type="text" class="form-control" id="address_1" name="address_1" placeholder="{{ __('Address Line 1') }}" value="{{ old('address_1') ?? $customer->address_1 }}">
        </div>
        <div class="mb-3 col-6">
            <label for="address_2" class="form-label">{{ __('Address Line 2') }}</label>
            <input type="text" class="form-control" id="address_2" name="address_2" placeholder="{{ __('Address Line 2') }}" value="{{ old('address_2') ?? $customer->address_2 }}">
        </div>
        <div class="mb-3 col-6">
            <label for="city" class="form-label">{{ __('City') }}</label>
            <input type="text" class="form-control" id="city" name="city" placeholder="{{ __('City') }}" value="{{ old('city') ?? $customer->city }}">
        </div>
        <div class="mb-3 col-6">
            <label for="state" class="form-label">{{ __('State') }}</label>
            <input type="text" class="form-control" id="state" name="state" placeholder="{{ __('State') }}" value="{{ old('state') ?? $customer->state }}">
        </div>
        <div class="mb-3 col-6">
            <label for="pin" class="form-label">{{ __('PIN Code') }}</label>
            <input type="text" class="form-control" id="pin" name="pin" placeholder="{{ __('PIN Code') }}" value="{{ old('pin') ?? $customer->pin }}">
        </div>
        <div class="mb-3 col-6">
            <label for="country" class="form-label">{{ __('Country') }}</label>
            <input type="text" class="form-control" id="country" name="country" placeholder="{{ __('Country') }}" value="{{ old('country') ?? $customer->country }}">
        </div>
        <div class="mb-3 col-6">
            <label for="phone" class="form-label">{{ __('Phone') }}</label>
            <input type="text" class="form-control" id="phone" name="phone" placeholder="{{ __('Phone') }}" value="{{ old('phone') ?? $customer->phone }}">
        </div>
        <div class="mb-3 col-6">
            <label for="mobile" class="form-label">{{ __('Mobile') }}</label>
            <input type="text" class="form-control" id="mobile" name="mobile" placeholder="{{ __('Mobile') }}" value="{{ old('mobile') ?? $customer->mobile }}">
        </div>
        <div class="mb-3 col-6">
            <label for="email" class="form-label">{{ __('Email') }}</label>
            <input type="email" class="form-control" id="email" name="email" placeholder="{{ __('Email') }}" value="{{ old('email') ?? $customer->email }}">
        </div>
        <div class="mb-3 col-6">
            <label for="religion" class="form-label">{{ __('Religion') }}</label>
            <input type="text" class="form-control" id="religion" name="religion" placeholder="{{ __('Religion') }}" value="{{ old('religion') ?? $customer->religion }}">
        </div>
        <div class="mb-3 col-6">
            <label for="dob" class="form-label">{{ __('Date of Birth') }}</label>
            <input type="date" class="form-control" id="dob" name="dob" placeholder="{{ __('Date of Birth') }}" value="{{ old('dob') ?? $customer->dob }}">
        </div>
        <div class="mb-3 col-6">
            <label for="gender" class="form-label">{{ __('Gender') }}</label>
            <select class="form-select" id="gender" name="gender">
                <option value="">{{ __('Select Gender') }}</option>
                <option value="Male" @selected($customer->gender == 'Male')>{{ __('Male') }}</option>
                <option value="Female" @selected($customer->gender == 'Female')>{{ __('Female') }}</option>
                <option value="Other" @selected($customer->gender == 'Other')>{{ __('Other') }}</option>
            </select>
        </div>
        <div class="mb-3 col-6">
            <label for="adhar_number" class="form-label">{{ __('Aadhar Number') }}</label>
            <input type="text" class="form-control" id="adhar_number" name="adhar_number" placeholder="{{ __('Aadhar Number') }}" value="{{ old('adhar_number') ?? $customer->adhar_number }}">
        </div>
        <div class="mb-3 col-6">
            <label for="status" class="form-label">{{ __('Status') }}</label>
            <select class="form-select" id="status" name="status">
                <option value="1" @selected($customer->status == 1)>{{ __('Active') }}</option>
                <option value="0" @selected($customer->status == 0)>{{ __('Inactive') }}</option>
            </select>
        </div>
    </div>
    <div class="pt-4">
        <button type="submit" class="btn btn-primary me-sm-3 me-1">{{ __('Submit') }}</button>
        <a href="{{route('agency.customers.index')}}" class="btn btn-label-secondary">{{ __('Cancel') }}</a>
    </div>
</form>
