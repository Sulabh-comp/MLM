@extends('layouts.admin.master')

@section('title', __('Family Member of ') . $customer->first_name . ' ' . $customer->last_name)

@section('content-header', __('Customers'))

@section('breadcrumbs')
<li class="breadcrumb-item">
    <a href="{{route('admin.customers.index')}}">{{ __('Customers') }}</a>
</li>
<li class="breadcrumb-item">
    <a href="{{route('admin.customers.show', $customer)}}">{{ __('View Customer') }}</a>
</li>
<li class="breadcrumb-item active">
    {{ __('Edit Family Member') }}
</li>
@endsection

@section('content')
<!-- Multi Column with Form Separator -->
<div class="card mb-4">
        <h5 class="card-header">{{ __('Edit Customer') }}</h5>

        @include('admin.customers.family_members._form', [
            'customer' => $customer,
            'route' => route('admin.customers.family-members.update', $familyMember),
            'method' => 'PUT',
            'familyMember' => $familyMember
        ])
</div>

</div>
@endsection
