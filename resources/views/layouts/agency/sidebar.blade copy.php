<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
  <div class="app-brand demo">
    <a href="{{route('admin.dashboard')}}" class="app-brand-link">
      <span class="app-brand-logo custome-admin-logo ">
        <img src="{{Setting::get('site_icon') ?: asset('logo.png')}}" class="w-75per">
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
        <div>{{ __('dashboard') }}</div>
      </a>
    </li>

    <li class="menu-item" id="users">
      <a href="{{route('admin.users.index')}}" class="menu-link">
        <i class="menu-icon tf-icons ti ti-users"></i>
        <div>{{ __('users') }}</div>
      </a>
    </li>
    <li class="menu-item" id="bank_accounts">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon tf-icons ti ti-building-bank"></i>
        <div data-i18n="{{__('bank_accounts')}}">{{__('bank_accounts')}}</div>
      </a>
      <ul class="menu-sub">
        <li class="menu-item" id="create-bank-account">
          <a href="{{route('admin.bank_accounts.create')}}" class="menu-link">
            <div data-i18n="{{__('add_user')}}">{{__('add_bank_account')}}</div>
          </a>
        </li>
        <li class="menu-item" id="view-bank-accounts">
          <a href="{{route('admin.bank_accounts.index')}}" class="menu-link">
            <div data-i18n="{{__('bank_accounts')}}">{{__('view_bank_accounts')}}</div>
          </a>
        </li>
      </ul>
    </li>

    <li class="menu-item" id="transactions">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon tf-icons ti ti-file"></i>
        <div data-i18n="{{__('transactions')}}">{{__('transactions')}}</div>
      </a>
      <ul class="menu-sub">
        <li class="menu-item" id="card_transactions">
          <a href="{{route('admin.transactions.card_transactions.index')}}" class="menu-link">
            <div data-i18n="{{__('add_user')}}">{{__('card_transactions')}}</div>
          </a>
        </li>
        <li class="menu-item" id="upi_transactions">
          <a href="{{route('admin.transactions.upi_transactions.index')}}" class="menu-link">
            <div data-i18n="{{__('add_user')}}">{{__('upi_transactions')}}</div>
          </a>
        </li>
        <li class="menu-item" id="unity_transactions">
          <a href="{{route('admin.transactions.unity_transactions.index')}}" class="menu-link">
            <div data-i18n="{{__('bank_accounts')}}">{{__('unity_transactions')}}</div>
          </a>
        </li>
        <li class="menu-item" id="bank_transactions">
          <a href="{{route('admin.bank_transactions.index')}}" class="menu-link">
            <div>{{ __('bank_transactions') }}</div>
          </a>
        </li>
      </ul>
    </li>
     <li class="menu-item" id="support_members">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon tf-icons ti ti-user-plus"></i>
        <div data-i18n="{{__('support_members')}}">{{__('support_members')}}</div>
      </a>
      <ul class="menu-sub">
        <li class="menu-item" id="support-members-create">
          <a href="{{route('admin.support_members.create')}}" class="menu-link">
            <div data-i18n="{{__('add_support_member')}}">{{__('add_support_member')}}</div>
          </a>
        </li>
        <li class="menu-item" id="view-support-members">
          <a href="{{route('admin.support_members.index')}}" class="menu-link">
            <div data-i18n="{{__('support_members')}}">{{__('view_support_members')}}</div>
          </a>
        </li>
      </ul>
    </li>
    <li class="menu-item" id="withdrawals">
        <a href="{{route('admin.withdrawals.index')}}" class="menu-link">
          <svg  xmlns="http://www.w3.org/2000/svg"  class="menu-icon"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="1.5"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-credit-card-pay"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 19h-6a3 3 0 0 1 -3 -3v-8a3 3 0 0 1 3 -3h12a3 3 0 0 1 3 3v4.5" /><path d="M3 10h18" /><path d="M16 19h6" /><path d="M19 16l3 3l-3 3" /><path d="M7.005 15h.005" /><path d="M11 15h2" /></svg>
          <div>{{__('withdrawals')}}</div>
        </a>
      </li>
    <li class="menu-item" id="static_pages">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon tf-icons ti ti-file"></i>
        <div data-i18n="{{__('static_pages')}}">{{__('static_pages')}}</div>
      </a>
      <ul class="menu-sub">
        <li class="menu-item" id="create-static-page">
          <a href="{{route('admin.static_pages.create')}}" class="menu-link">
            <div data-i18n="{{__('add_user')}}">{{__('add_static_pages')}}</div>
          </a>
        </li>
        <li class="menu-item" id="view-static-pages">
          <a href="{{route('admin.static_pages.index')}}" class="menu-link">
            <div data-i18n="{{__('static_pages')}}">{{__('view_static_pages')}}</div>
          </a>
        </li>
      </ul>
    </li>
    <li class="menu-item" id="profile">
      <a href="{{route('admin.profile')}}" class="menu-link">
        <i class="menu-icon tf-icons ti ti-user-check"></i>
        <div>{{ __('profile') }}</div>
      </a>
    </li>
    <li class="menu-item" id="settings">
      <a href="{{route('admin.settings')}}" class="menu-link">
        <i class="menu-icon tf-icons ti ti-settings"></i>
        <div>{{ __('settings') }}</div>
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
