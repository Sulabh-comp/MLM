<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
  <div class="app-brand demo">
    <a href="{{route('admin.dashboard')}}" class="app-brand-link">
      <span class="app-brand-logo custome-admin-logo ">
        <img src="{{ asset('logo.png')}}" class="w-75per">
      </span>
      <span class="app-brand-text demo light-text fw-bold">{{ env('SITE_NAME') }}</span>
    </a>

    <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
      <i class="ti menu-toggle-icon d-none d-xl-block ti-sm align-middle"></i>
      <i class="ti ti-x d-block d-xl-none ti-sm align-middle"></i>
    </a>
  </div>

  <div class="menu-inner-shadow"></div>
  <ul class="menu-inner py-1 admin-nav">
    <!-- Apps & Pages -->
    <li class="menu-item" id="dashboard">
      <a href="{{route('admin.dashboard')}}" class="menu-link">
        <i class="menu-icon tf-icons ti ti-home"></i>
        <div>{{ __('Dashboard') }}</div>
      </a>
    </li>

    <li class="menu-item" id="users">
      <a href="{{route('admin.agencies.index')}}" class="menu-link">
        <i class="menu-icon tf-icons ti ti-users"></i>
        <div>{{ __('Agencies') }}</div>
      </a>
    </li>
    <li class="menu-item" id="users">
      <a href="{{route('admin.customers.index')}}" class="menu-link">
        <i class="menu-icon tf-icons ti ti-users"></i>
        <div>{{ __('Customers') }}</div>
      </a>
    </li>
    <li class="menu-item" id="users">
      <a href="{{route('admin.employees.index')}}" class="menu-link">
        <i class="menu-icon tf-icons ti ti-users"></i>
        <div>{{ __('Employees') }}</div>
      </a>
    </li>
    
    <li class="menu-item" id="managers">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon tf-icons ti ti-user-star"></i>
        <div>{{ __('Managers') }}</div>
      </a>
      <ul class="menu-sub">
        <li class="menu-item">
          <a href="{{route('admin.managers.index')}}" class="menu-link">
            <div>{{ __('Manager List') }}</div>
          </a>
        </li>
        <li class="menu-item">
          <a href="{{route('admin.managers.hierarchy')}}" class="menu-link">
            <div>{{ __('Hierarchy View') }}</div>
          </a>
        </li>
        <li class="menu-item">
          <a href="{{route('admin.managers.create')}}" class="menu-link">
            <div>{{ __('Add Manager') }}</div>
          </a>
        </li>
      </ul>
    </li>
    
    <li class="menu-item" id="manager-levels">
      <a href="{{route('admin.manager-levels.index')}}" class="menu-link">
        <i class="menu-icon tf-icons ti ti-hierarchy"></i>
        <div>{{ __('Manager Levels') }}</div>
      </a>
    </li>
    
    <li class="menu-item" id="regions">
      <a href="{{route('admin.regions.index')}}" class="menu-link">
        <i class="menu-icon tf-icons ti ti-map-2"></i>
        <div>{{ __('Regions') }}</div>
      </a>
    </li>
    
    <li class="menu-item" id="notifications">
      <a href="{{route('admin.notifications.index')}}" class="menu-link">
        <i class="menu-icon tf-icons ti ti-bell"></i>
        @php
            $unreadNotifications = auth()->guard('admin')->user()->notifications()->where('status', 0)->count();
        @endphp
        <div>{{ __('Notifications') }} @if($unreadNotifications > 0) <span class="badge bg-danger">{{ $unreadNotifications }}</span> @endif</div>
      </a>
    </li>
     <li class="menu-item">
      <a data-bs-toggle="modal" data-bs-target="#logout" href="{{ route('admin.logout') }}" class="menu-link">
        <i class="ti ti-logout me-2 ti-sm"></i>
        <span>{{ __('logout') }}</span>
      </a>
    </li>
  </ul>
</aside>
<style>
  .admin-nav li{
  margin: 0.5rem 0!important;
}
</style>
