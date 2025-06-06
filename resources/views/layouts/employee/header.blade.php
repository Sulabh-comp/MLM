<!-- Layout container -->
<div class="layout-page">
  <nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme" id="layout-navbar">
    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
      <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
        <i class="ti ti-menu-2 ti-sm"></i>
      </a>
    </div>

    <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
     
      <ul class="navbar-nav flex-row align-items-center ms-auto">
        <!-- User -->
        <li class="nav-item navbar-dropdown dropdown-user dropdown">
          <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
            <div class="avatar avatar-online">
              <img src="{{ auth('employee')->user()->picture ?: asset('placeholder.png') }}" alt class="h-auto rounded-circle">
            </div>
          </a>
          <ul class="dropdown-menu dropdown-menu-end">
            <li>
              <a class="dropdown-item" href="#">
                <div class="d-flex">
                  <div class="flex-shrink-0 me-3">
                    <div class="avatar avatar-online">
                      <img src="{{ auth('employee')->user()->picture ?: asset('placeholder.png') }}" alt class="h-auto rounded-circle">
                    </div>
                  </div>
                  <div class="flex-grow-1">
                    <span class="fw-medium d-block">{{ Str::limit(auth('employee')->user()->name, 10) }}</span>
                    <small class="text-muted">{{ Str::limit(auth('employee')->user()->email, 15) }}</small>
                  </div>
                </div>
              </a>
            </li>
            <li>
              <div class="dropdown-divider"></div>
            </li>
            <li>
              <a class="dropdown-item pointer" data-bs-toggle="modal" data-bs-target="#logout">
                <i class="ti ti-logout me-2 ti-sm"></i>
                <span class="align-middle">{{__('logout')}}</span>
              </a>
            </li>
          </ul>
        </li>
        <!--/ User -->
      </ul>
    </div>
  </nav>

  <!-- Enable OTP Modal -->
<div class="modal fade" id="logout" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-simple modal-enable-otp modal-dialog-centered">
    <div class="modal-content p-3 p-md-5">
      <div class="modal-body">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        <div class="text-center mb-4">
          <h3 class="mb-2">{{__('logout')}}?</h3>
        </div>
        <p class="text-center mb-4">{{__('Are you sure you want to logout')}}</p>
        <form class="row g-3" action="{{ route('employee.logout') }}">
          @csrf
          <div class="col-12 text-center">
            <button type="submit" class="btn btn-primary me-sm-3 me-1">{{__('yes')}}</button>
            <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal" aria-label="Close">{{__('no')}}</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<!--/ Enable OTP Modal -->