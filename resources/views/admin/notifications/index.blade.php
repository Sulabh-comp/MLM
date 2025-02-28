@extends('layouts.admin.master')

@section('title', 'Notifications')

@section('content-header', __('Notifications'))

@section('breadcrumbs')
<li class="breadcrumb-item">
  <a href="{{ route('admin.notifications.index') }}">{{ __('Notifications') }}</a>
</li>
<li class="breadcrumb-item active">
  {{ __('View Notifications') }}
</li>
@endsection

@section('content')
<div class="card">
  <div class="card-header header-elements d-flex justify-content-between align-items-center">
    <h5 class="m-0 me-2">{{ __('Notifications') }}</h5>
    <form class="form d-flex align-items-center" method="GET" action="#">
      <a href="{{ route('admin.notifications.create') }}" class="btn btn-primary" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="tooltip-primary" title="{{ __('Add New') }}">
        <i class="fa-solid fa-plus"></i>{{ __('Add New') }}
      </a>
    </form>
  </div>
  <div class="table-responsive">
    <table class="table table-hover data-table">
      <thead class="border-top">
        <tr>
          <th>{{ __('S. No') }}</th>
          <th>{{ __('Title') }}</th>
          <th>{{ __('Message') }}</th>
          <th>{{ __('Created At') }}</th>
        </tr>
      </thead>
      <tbody class="table-border-bottom-0">
        @php
            $admin = auth('admin')->user();

            $lastReadNotification = auth()->guard('admin')->user()->last_read_notification ?? now()->subYears(10);
            $unreadNotifications = auth()->guard('admin')->user()->notifications()->where('created_at', '>', $lastReadNotification)->count();
            // Set per_page dynamically
            $per_page = max($unreadNotifications, 10);

            $data = auth('admin')->user()->notifications()
                ->latest()
                ->paginate($per_page);

            // Update last read timestamp
            $admin->update(['last_notification_read_at' => now()]);
        @endphp
        @forelse($data as $key => $datum)
        <tr @if($key < $unreadNotifications) class="bg-secondary" @endif>
          <td><span class="fw-medium">{{ $data->firstItem() + $key + 1 }}</span></td>
          <td>
            {{ $datum->title }}
          </td>
          <td>
            <a href="{{ $datum->url }}" class="text-primary">{{ $datum->message }}</a>
          </td>
          <td>{{ $datum->created_at->diffForHumans() }}</td>
        </tr>
        @empty
        <tr>
          <td colspan="12" class="text-center">
            <span class="no-data-frame">
              <img src="{{ asset('/images/no-data.png') }}" class="no-data-avatar" alt="No Data">
              <h2>{{ __('No Data Found') }}</h2>
            </span>
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
    <div class="d-flex justify-content-center mt-3">
        {{ $data->links() }}
    </div>
  </div>
</div>
@endsection
