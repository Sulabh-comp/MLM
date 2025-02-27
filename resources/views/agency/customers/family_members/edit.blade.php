@extends('layouts.agency.master')

@section('title', __('Family Member of ') . $familyMember->customer->first_name . ' ' . $familyMember->customer->last_name)

@section('content-header', __('Customers'))

@section('breadcrumbs')
<li class="breadcrumb-item">
    <a href="{{route('agency.customers.index')}}">{{ __('Customers') }}</a>
</li>
<li class="breadcrumb-item">
    <a href="{{route('agency.customers.show', $familyMember->customer)}}">{{ __('View Customer') }}</a>
</li>
<li class="breadcrumb-item active">
    {{ __('Edit Family Member') }}
</li>
@endsection

@section('content')
<!-- Multi Column with Form Separator -->
<div class="card mb-4">
        <h5 class="card-header">{{ __('Edit Customer') }}</h5>

        @include('agency.customers.family_members._form', [
            'customer' => $familyMember->customer,
            '_route' => route('agency.family-members.update', $familyMember),
            '_method' => 'PUT',
            'familyMember' => $familyMember
        ])
</div>

</div>
@endsection
