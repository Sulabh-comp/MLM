@extends('layouts.manager.master')

@section('title', 'Notifications')

@section('content-header')
    Notifications / <span class="text-primary">All Notifications</span>
@endsection

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">My Notifications</h5>
        @if($notifications->count() > 0)
            <div class="dropdown">
                <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="ti ti-dots-vertical me-1"></i>Actions
                </button>
                <ul class="dropdown-menu">
                    <li>
                        <form action="{{ route('manager.notifications.destroy', 'all') }}" method="POST" style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="dropdown-item text-danger" onclick="return confirm('Clear all notifications?')">
                                <i class="ti ti-trash me-1"></i>Clear All
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        @endif
    </div>
    <div class="card-body">
        @if($notifications->count() > 0)
            <div class="list-group list-group-flush">
                @foreach($notifications as $notification)
                    <div class="list-group-item border-0 d-flex align-items-start {{ $notification->status == 1 ? '' : 'bg-light' }}">
                        <div class="avatar flex-shrink-0 me-3">
                            <span class="avatar-initial rounded bg-label-info">
                                <i class="ti ti-bell"></i>
                            </span>
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="mb-1">{{ $notification->title ?? 'Notification' }}</h6>
                                    <p class="mb-1 text-muted">{{ $notification->message }}</p>
                                    <small class="text-muted">
                                        <i class="ti ti-clock me-1"></i>
                                        {{ $notification->created_at->diffForHumans() }}
                                    </small>
                                </div>
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                        <i class="ti ti-dots-vertical"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="{{ route('manager.notifications.show', $notification) }}">
                                            <i class="ti ti-eye me-1"></i> View Details
                                        </a>
                                        @if($notification->url)
                                            <a class="dropdown-item" href="{{ $notification->url }}">
                                                <i class="ti ti-external-link me-1"></i> Go to Link
                                            </a>
                                        @endif
                                        <div class="dropdown-divider"></div>
                                        <form action="{{ route('manager.notifications.destroy', $notification) }}" method="POST" style="display: inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item text-danger" onclick="return confirm('Delete this notification?')">
                                                <i class="ti ti-trash me-1"></i> Delete
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @if($notification->status == 0)
                            <div class="flex-shrink-0">
                                <span class="badge bg-primary rounded-pill">New</span>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            @if($notifications->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $notifications->links() }}
                </div>
            @endif
        @else
            <div class="text-center py-5">
                <div class="avatar avatar-xl mx-auto mb-3">
                    <span class="avatar-initial rounded-circle bg-label-secondary">
                        <i class="ti ti-bell-off ti-lg"></i>
                    </span>
                </div>
                <h5 class="mb-1">No notifications</h5>
                <p class="text-muted">You don't have any notifications yet. When you get notifications, they'll show up here.</p>
            </div>
        @endif
    </div>
</div>
@endsection

@section('styles')
<style>
.list-group-item.bg-light {
    background-color: rgba(67, 89, 113, 0.05) !important;
}
</style>
@endsection
