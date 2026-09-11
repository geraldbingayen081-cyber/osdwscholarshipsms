<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Display student notifications.
     */
    public function index()
    {
        $notifications = Auth::user()->notifications()->paginate(15);
        $unreadCount = Auth::user()->unreadNotifications()->count();

        return view('student.notifications.index', compact('notifications', 'unreadCount'));
    }

    /**
     * Mark single notification as read.
     */
    public function markAsRead(string $id)
    {
        $notification = Auth::user()->notifications()->where('id', $id)->firstOrFail();
        $notification->markAsRead();

        $url = $notification->data['url'] ?? route('student.notifications.index');
        return redirect($url);
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();

        return redirect()->back()->with('success', 'All notifications marked as read.');
    }
}
