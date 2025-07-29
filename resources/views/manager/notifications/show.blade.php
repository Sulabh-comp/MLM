@extends('layouts.manager.master')

@section('title', 'Notification Details')

@section('content-header')
    Notifications / <span class="text-primary">Notification Details</span>
@endsection

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Notification Details</h5>
                <div class="d-flex gap-2">
                    <form action="{{ route('manager.notifications.destroy', $notification) }}" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('Delete this notification?')">
                            <i class="ti ti-trash me-1"></i>Delete
                        </button>
                    </form>
                    <a href="{{ route('manager.notifications.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="ti ti-arrow-left me-1"></i>Back
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-start mb-4">
                    <div class="avatar flex-shrink-0 me-3">
                        <span class="avatar-initial rounded bg-label-info">
                            <i class="ti ti-bell ti-sm"></i>
                        </span>
                    </div>
                    <div class="flex-grow-1">
                        <h4 class="mb-1">{{ $notification->title ?? 'Notification' }}</h4>
                        <p class="text-muted mb-0">
                            <i class="ti ti-clock me-1"></i>
                            {{ $notification->created_at->format('M d, Y \a\t H:i') }}
                        </p>
                    </div>
                    @if($notification->status == 0)
                        <span class="badge bg-primary">New</span>
                    @endif
                </div>

                <div class="notification-content">
                    <h6 class="mb-3">Message:</h6>
                    <div class="alert alert-info border-0">
                        {{ $notification->message }}
                    </div>
                </div>

                @if($notification->url)
                    <div class="additional-info mt-4">
                        <h6 class="mb-3">Related Link:</h6>
                        <div class="d-flex align-items-center p-3 bg-light rounded">
                            <i class="ti ti-external-link me-2"></i>
                            <a href="{{ $notification->url }}" target="_blank" class="text-decoration-none">
                                {{ $notification->url }}
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0">Notification Info</h6>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="avatar flex-shrink-0 me-3">
                        <span class="avatar-initial rounded bg-label-info">
                            <i class="ti ti-bell"></i>
                        </span>
                    </div>
                    <div>
                        <p class="mb-0">Type</p>
                        <h6 class="mb-0 text-capitalize">Notification</h6>
                    </div>
                </div>
                
                <div class="d-flex align-items-center mb-3">
                    <div class="avatar flex-shrink-0 me-3">
                        <span class="avatar-initial rounded bg-label-info">
                            <i class="ti ti-calendar"></i>
                        </span>
                    </div>
                    <div>
                        <p class="mb-0">Received</p>
                        <h6 class="mb-0">{{ $notification->created_at->format('M d, Y') }}</h6>
                    </div>
                </div>

                <div class="d-flex align-items-center mb-3">
                    <div class="avatar flex-shrink-0 me-3">
                        <span class="avatar-initial rounded bg-label-{{ $notification->status == 1 ? 'success' : 'warning' }}">
                            <i class="ti ti-{{ $notification->status == 1 ? 'eye' : 'eye-off' }}"></i>
                        </span>
                    </div>
                    <div>
                        <p class="mb-0">Status</p>
                        <h6 class="mb-0">{{ $notification->status == 1 ? 'Read' : 'Unread' }}</h6>
                    </div>
                </div>

                @if($notification->status == 1)
                    <div class="d-flex align-items-center">
                        <div class="avatar flex-shrink-0 me-3">
                            <span class="avatar-initial rounded bg-label-success">
                                <i class="ti ti-check"></i>
                            </span>
                        </div>
                        <div>
                            <p class="mb-0">Read At</p>
                            <h6 class="mb-0">{{ $notification->updated_at->format('M d, Y H:i') }}</h6>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
