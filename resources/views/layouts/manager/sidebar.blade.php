<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
  <div class="app-brand demo">
    <a href="{{route('manager.dashboard')}}" class="app-brand-link">
      <span class="app-brand-logo custome-admin-logo ">
        <img src="{{ asset('logo.png')}}" class="w-75per">
      </span>
      <span class="app-brand-text demo light-text fw-bold">{{ env('SITE_NAME') }} Manager</span>
    </a>

    <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
      <i class="ti menu-toggle-icon d-none d-xl-block ti-sm align-middle"></i>
      <i class="ti ti-x d-block d-xl-none ti-sm align-middle"></i>
    </a>
  </div>

  <div class="menu-inner-shadow"></div>
  <ul class="menu-inner py-1 manager-nav">
    <!-- Hierarchy Info -->
    <li class="menu-header small text-uppercase">
      <span class="menu-header-text">{{ auth('manager')->user()->level_name ?? 'Manager' }} - Level {{ auth('manager')->user()->depth ?? 0 }}</span>
    </li>
    
    <!-- Apps & Pages -->
    <li class="menu-item" id="dashboard">
      <a href="{{route('manager.dashboard')}}" class="menu-link">
        <i class="menu-icon tf-icons ti ti-home"></i>
        <div>{{ __('Dashboard') }}</div>
      </a>
    </li>

    <li class="menu-item" id="managers">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon tf-icons ti ti-user-star"></i>
        <div>{{ __('Team Management') }}</div>
      </a>
      <ul class="menu-sub">
        <li class="menu-item">
          <a href="{{route('manager.managers.index')}}" class="menu-link">
            <div>{{ __('My Team') }}</div>
          </a>
        </li>
        <li class="menu-item">
          <a href="{{route('manager.managers.create')}}" class="menu-link">
            <div>{{ __('Add Manager') }}</div>
          </a>
        </li>
      </ul>
    </li>

    <li class="menu-item" id="employees">
      <a href="{{route('manager.employees.index')}}" class="menu-link">
        <i class="menu-icon tf-icons ti ti-users"></i>
        <div>{{ __('Employees') }}</div>
      </a>
    </li>
    
    <li class="menu-item" id="agencies">
      <a href="{{route('manager.agencies.index')}}" class="menu-link">
        <i class="menu-icon tf-icons ti ti-building"></i>
        <div>{{ __('Agencies') }}</div>
      </a>
    </li>
    
    <li class="menu-item" id="customers">
      <a href="{{route('manager.customers.index')}}" class="menu-link">
        <i class="menu-icon tf-icons ti ti-user-friends"></i>
        <div>{{ __('Customers') }}</div>
      </a>
    </li>
    
    <li class="menu-item" id="notifications">
      <a href="{{route('manager.notifications.index')}}" class="menu-link">
        <i class="menu-icon tf-icons ti ti-bell"></i>
        @php
            $unreadNotifications = auth('manager')->user()->notifications()->where('status', 0)->count();
        @endphp
        <div>{{ __('Notifications') }} @if($unreadNotifications > 0) <span class="badge bg-danger">{{ $unreadNotifications }}</span> @endif</div>
      </a>
    </li>
    
     <li class="menu-item">
      <a data-bs-toggle="modal" data-bs-target="#logout" href="{{ route('manager.logout') }}" class="menu-link">
        <i class="ti ti-logout me-2 ti-sm"></i>
        <span>{{ __('logout') }}</span>
      </a>
    </li>
  </ul>
</aside>
<style>
  .manager-nav li{
  margin: 0.5rem 0!important;
}
</style>
