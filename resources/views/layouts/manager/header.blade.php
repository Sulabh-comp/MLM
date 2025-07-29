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
              <img src="{{ auth('manager')->user()->picture ?: asset('placeholder.png') }}" alt class="h-auto rounded-circle">
            </div>
          </a>
          <ul class="dropdown-menu dropdown-menu-end">
            <li>
              <a class="dropdown-item" href="#">
                <div class="d-flex">
                  <div class="flex-shrink-0 me-3">
                    <div class="avatar avatar-online">
                      <img src="{{ auth('manager')->user()->picture ?: asset('placeholder.png') }}" alt class="h-auto rounded-circle">
                    </div>
                  </div>
                  <div class="flex-grow-1">
                    <span class="fw-medium d-block">{{ Str::limit(auth('manager')->user()->name, 10) }}</span>
                    <small class="text-muted">{{ Str::limit(auth('manager')->user()->email, 15) }}</small>
                    <br><small class="text-primary">{{ auth('manager')->user()->region->name ?? 'No Region' }}</small>
                  </div>
                </div>
              </a>
            </li>
            <li>
              <div class="dropdown-divider"></div>
            </li>
            <li>
              <a class="dropdown-item pointer" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
                <i class="ti ti-key me-2 ti-sm"></i>
                <span class="align-middle">{{__('Change Password')}}</span>
              </a>
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

  <!-- Change Password Modal -->
  <div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <form method="POST" action="{{ route('manager.change.password') }}">
          @csrf
          <div class="modal-header">
            <h5 class="modal-title" id="changePasswordModalLabel">{{__('Change Password')}}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="mb-3">
              <label for="current_password" class="form-label">{{__('Current Password')}}</label>
              <input type="password" class="form-control" id="current_password" name="current_password" required>
            </div>
            <div class="mb-3">
              <label for="password" class="form-label">{{__('New Password')}}</label>
              <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <div class="mb-3">
              <label for="password_confirmation" class="form-label">{{__('Confirm New Password')}}</label>
              <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{__('Close')}}</button>
            <button type="submit" class="btn btn-primary">{{__('Change Password')}}</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Logout Modal -->
  <div class="modal fade" id="logout" tabindex="-1" aria-labelledby="logoutLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="logoutLabel">{{__('Logout')}}</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          {{__('Are you sure you want to logout?')}}
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{__('Close')}}</button>
          <a href="{{ route('manager.logout') }}" class="btn btn-primary">{{__('Logout')}}</a>
        </div>
      </div>
    </div>
  </div>
