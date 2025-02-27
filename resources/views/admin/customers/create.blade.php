@extends('layouts.admin.master')

@section('title', __('Customers'))

@section('content-header', __('Customers'))

@section('breadcrumbs')
<li class="breadcrumb-item">
    <a href="{{route('admin.customers.index')}}">{{ __('Customers') }}</a>
</li>
<li class="breadcrumb-item active">
    {{ __('Add Customer') }}
</li>
@endsection

@section('content')
<!-- Multi Column with Form Separator -->
<div class="card mb-4">
        <h5 class="card-header">{{ __('Add Customer') }}</h5>

        @include('admin.customers._form', [
            'customer' => $customer,
            '_route' => route('admin.customers.store'),
            '_method' => 'POST',
            'agencies' => $agencies
        ])
</div>

</div>
@endsection
