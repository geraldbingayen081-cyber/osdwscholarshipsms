<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Display student notifications with optional category filtering.
     */
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'all');
        $query = Auth::user()->notifications();

        if ($tab === 'unread') {
            $query = Auth::user()->unreadNotifications();
        } elseif ($tab === 'welfare') {
            $query->where(function ($q) {
                $q->where('type', 'like', '%Welfare%')
                  ->orWhere('data->type', 'welfare_case')
                  ->orWhere('data->type', 'welfare_referral');
            });
        } elseif ($tab === 'scholarships') {
            $query->where(function ($q) {
                $q->where('type', 'like', '%Application%')
                  ->orWhere('type', 'like', '%Document%')
                  ->orWhere('type', 'like', '%Scholar%')
                  ->orWhere('type', 'like', '%Compliance%')
                  ->orWhere('data->type', 'scholarship_application')
                  ->orWhere('data->type', 'application_submitted')
                  ->orWhere('data->type', 'document_status');
            });
        }

        $notifications = $query->paginate(15)->appends(['tab' => $tab]);
        $unreadCount = Auth::user()->unreadNotifications()->count();

        // Specific category counters
        $welfareCount = Auth::user()->notifications()
            ->where(function ($q) {
                $q->where('type', 'like', '%Welfare%')
                  ->orWhere('data->type', 'welfare_case')
                  ->orWhere('data->type', 'welfare_referral');
            })->count();

        $scholarshipCount = Auth::user()->notifications()
            ->where(function ($q) {
                $q->where('type', 'like', '%Application%')
                  ->orWhere('type', 'like', '%Document%')
                  ->orWhere('type', 'like', '%Scholar%')
                  ->orWhere('type', 'like', '%Compliance%')
                  ->orWhere('data->type', 'scholarship_application')
                  ->orWhere('data->type', 'application_submitted')
                  ->orWhere('data->type', 'document_status');
            })->count();

        $totalCount = Auth::user()->notifications()->count();

        return view('student.notifications.index', compact(
            'notifications',
            'unreadCount',
            'tab',
            'welfareCount',
            'scholarshipCount',
            'totalCount'
        ));
    }

    /**
     * Mark single notification as read and redirect to target URL.
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
