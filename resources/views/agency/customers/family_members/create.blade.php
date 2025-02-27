@extends('layouts.agency.master')

@section('title', __('Family Member of ') . $customer->first_name . ' ' . $customer->last_name)

@section('content-header', __('Customers'))

@section('breadcrumbs')
<li class="breadcrumb-item">
    <a href="{{route('agency.customers.index')}}">{{ __('Customers') }}</a>
</li>
<li class="breadcrumb-item">
    <a href="{{route('agency.customers.show', $customer)}}">{{ __('View Customer') }}</a>
</li>
<li class="breadcrumb-item active">
    {{ __('Add Family Member') }}
</li>
@endsection

@section('content')
<!-- Multi Column with Form Separator -->
<div class="card mb-4">
        <h5 class="card-header">{{ __('Add Customer') }}</h5>

        @include('agency.customers.family_members._form', [
            'customer' => $customer,
            '_route' => route('agency.family-members.store'),
            '_method' => 'POST',
            'familyMember' => $familyMember
        ])
</div>

</div>
@endsection
