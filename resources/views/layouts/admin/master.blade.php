<!DOCTYPE html>

<html lang="en" class="light-style layout-navbar-fixed layout-menu-fixed layout-compact" dir="ltr" data-theme="theme-default" data-assets-path="{{asset('vuexy-assets')}}/" data-template="vertical-menu-template">

<head>

    <meta charset="utf-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">

    <title>@yield('title') | {{env('SITE_NAME')}}</title>

    <meta name="description" content="{{env('SITE_NAME')}}">

    <meta name="keywords" content="{{env('SITE_NAME')}}">

    <link rel="canonical" href="#">

    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com/">

    <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&amp;ampdisplay=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('vuexy-assets/vendor/fonts/fontawesome.css') }}">

    <link rel="stylesheet" href="{{ asset('vuexy-assets/vendor/fonts/tabler-icons.css') }}">

    <link rel="stylesheet" href="{{ asset('vuexy-assets/vendor/fonts/flag-icons.css') }}">

    <link rel="stylesheet" href="{{ asset('vuexy-assets/vendor/css/rtl/core.css') }}" class="template-customizer-core-css">

    <link rel="stylesheet" href="{{ asset('vuexy-assets/vendor/css/rtl/theme-default.css') }}" class="template-customizer-theme-css">

    <link rel="stylesheet" href="{{ asset('vuexy-assets/css/demo.css') }}">

    <link rel="stylesheet" href="{{ asset('vuexy-assets/vendor/libs/node-waves/node-waves.css') }}">

    <link rel="stylesheet" href="{{ asset('vuexy-assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}">

    <link rel="stylesheet" href="{{ asset('vuexy-assets/vendor/libs/typeahead-js/typeahead.css') }}">

    <link rel="stylesheet" href="{{ asset('vuexy-assets/vendor/libs/apex-charts/apex-charts.css') }}">

    <link rel="stylesheet" href="{{ asset('vuexy-assets/vendor/libs/swiper/swiper.css') }}">

    <link rel="stylesheet" href="{{ asset('vuexy-assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}">

    <link rel="stylesheet" href="{{ asset('vuexy-assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css') }}">

    <link rel="stylesheet" href="{{ asset('vuexy-assets/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.css') }}">

    <link rel="stylesheet" href="{{ asset('vuexy-assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css') }}">

    <link rel="stylesheet" href="{{ asset('vuexy-assets/vendor/libs/flatpickr/flatpickr.css') }}">

    <link rel="stylesheet" href="{{ asset('vuexy-assets/vendor/libs/datatables-rowgroup-bs5/rowgroup.bootstrap5.css') }}">

    <link rel="stylesheet" href="{{ asset('vuexy-assets/vendor/libs/%40form-validation/umd/styles/index.min.css') }}">

    <link rel="stylesheet" href="{{ asset('vuexy-assets/vendor/css/pages/cards-advance.css') }}">

    <link rel="stylesheet" href="{{ asset('vuexy-assets/vendor/css/pages/page-auth.css')}}">

    <link rel="stylesheet" href="{{ asset('vuexy-assets/vendor/libs/bootstrap-select/bootstrap-select.css')}}">

    <link rel="stylesheet" href="{{ asset('vuexy-assets/vendor/libs/toastr/toastr.css')}}">

    <link rel="stylesheet" href="{{ asset('vuexy-assets/vendor/libs/bs-stepper/bs-stepper.css')}}" />

    <link rel="stylesheet" href="{{ asset('vuexy-assets/vendor/libs/select2/select2.css')}}" />

    <link rel="stylesheet" href="{{ asset('vuexy-assets/vendor/libs/quill/typography.css') }}" />
    <link rel="stylesheet" href="{{ asset('vuexy-assets/vendor/libs/quill/katex.css') }}" />
    <link rel="stylesheet" href="{{ asset('vuexy-assets/vendor/libs/quill/editor.css') }}" />
    <link rel="stylesheet" href="{{ asset('vuexy-assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.css') }}" />

    <script src="{{ asset('vuexy-assets/vendor/js/helpers.js') }}"></script>

    <script src="{{ asset('vuexy-assets/vendor/js/template-customizer.js') }}"></script>

    <script src="{{ asset('vuexy-assets/js/config.js') }}"></script>
</head>

<style>
#toast-container > .toast-success-background {
  background-image: url("{{asset('images/notification/success.png')}}") !important;
  background-color: #dff7e9;
  color: #111;
}

#toast-container > .toast-error-background {
  background-image: url("{{asset('images/notification/failed.png')}}") !important;
  background-color: #fce5e6;
  color: #111;
}
</style>

<body>

@guest('admin')

<div class="authentication-wrapper authentication-cover authentication-bg">

<div class="authentication-inner">

   @yield('content')

</div>

</div>

@endguest

 

@auth('admin')

<div class="layout-wrapper layout-content-navbar">

    <div class="layout-container">

        @include('layouts.admin.sidebar')

        @include('layouts.admin.header')

            <div class="content-wrapper">

                <div class="container-xxl flex-grow-1 container-p-y">

                    <div class="d-flex justify-content-between align-items-center">
                        <nav aria-label="breadcrumb" class="breadcrumb-padding">
                          <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                              <a href="{{ route('admin.dashboard') }}">{{__('Home')}}</a>
                            </li>
                            @yield('breadcrumbs')
                          </ol>
                        </nav>
                        @if(!request()->is('*create*') && !request()->is('*edit*'))
                        <form class="d-flex" method="GET" action="">
                          <input class="form-control me-2" type="search" name="q" placeholder="Search" value="{{ request('q') }}" aria-label="Search">
                          <button class="btn btn-outline-primary" type="submit">{{ __('Search') }}</button>
                        </form>
                        @endif
                        @yield('bulk_action')
                    </div>
                    @yield('content')
                </div>

            @include('layouts.admin.footer')

         <div class="content-backdrop fade"></div>

    </div>

  </div>

</div>

@endauth

@include('layouts.admin.script')

@yield('scripts')

<div class="layout-overlay layout-menu-toggle"></div>

<div class="drag-target"></div>

</body>

</html>
