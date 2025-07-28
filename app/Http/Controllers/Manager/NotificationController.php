<?php

namespace App\Http\Controllers\Manager;

use App\Models\Notification;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class NotificationController extends Controller
{
    public function index()
    {
        $manager = auth()->guard('manager')->user();
        $notifications = $manager->notifications()->latest()->paginate(15);

        return view('manager.notifications.index', compact('notifications', 'manager'));
    }

    public function markAsRead(Notification $notification)
    {
        $manager = auth()->guard('manager')->user();
        
        if ($notification->user_id !== $manager->id) {
            abort(403, 'Unauthorized access to this notification.');
        }

        $notification->update(['is_read' => true]);

        return back()->with('success', 'Notification marked as read.');
    }

    public function markAllAsRead()
    {
        $manager = auth()->guard('manager')->user();
        
        $manager->notifications()->where('is_read', false)->update(['is_read' => true]);
        $manager->update(['last_notification_read_at' => now()]);

        return back()->with('success', 'All notifications marked as read.');
    }
}
