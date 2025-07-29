<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Notification;

class NotificationController extends Controller
{
    public function index()
    {
        $manager = auth()->guard('manager')->user();
        $notifications = Notification::where('model', 'App\Models\Manager')
            ->where('user_id', $manager->id)
            ->latest()
            ->paginate(10);
            
        return view('manager.notifications.index', compact('notifications'));
    }

    public function show(Notification $notification)
    {
        $manager = auth()->guard('manager')->user();
        
        // Ensure this notification belongs to the current manager
        if ($notification->model !== 'App\Models\Manager' || $notification->user_id != $manager->id) {
            abort(404);
        }
        
        // Mark notification as read
        if ($notification->status == 0) {
            $notification->update(['status' => 1]);
        }
        
        return view('manager.notifications.show', compact('notification'));
    }

    public function destroy(Notification $notification)
    {
        $manager = auth()->guard('manager')->user();
        
        // Ensure this notification belongs to the current manager
        if ($notification->model !== 'App\Models\Manager' || $notification->user_id != $manager->id) {
            abort(404);
        }
        
        $notification->delete();
        
        return redirect()->route('manager.notifications.index')
            ->with('success', 'Notification deleted successfully');
    }
}
