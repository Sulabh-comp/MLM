@extends('layouts.admin.master')

@section('title', __('Customers'))

@section('content-header', __('Customers'))

@section('breadcrumbs')
<li class="breadcrumb-item">
    <a href="{{route('admin.customers.index')}}">{{ __('Customers') }}</a>
</li>
<li class="breadcrumb-item active">
    {{ __('Edit Customer') }}
</li>
@endsection

@section('content')
<!-- Multi Column with Form Separator -->
<div class="card mb-4">
        <h5 class="card-header">{{ __('Edit Customer') }}</h5>

        @include('admin.customers._form', [
            'customer' => $customer,
            '_route' => route('admin.customers.update', $customer),
            '_method' => 'PUT',
            'agencies' => $agencies
        ])
</div>

</div>
@endsection
