<?php

namespace App\Http\Controllers\Notification;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    // Mark single notification as read and redirect to correct page
    public function markAsRead($id)
    {
        $notification = auth()->user()->notifications()->findOrFail($id);
        $notification->markAsRead();
        $data = $notification->data;

        if (isset($data['cutting_id'])) {
            return redirect()->route('cuttings.show', $data['cutting_id']);
        } elseif (isset($data['embroidery_id'])) {
            return redirect()->route('embroideries.show', $data['embroidery_id']);
        } elseif (isset($data['print_id'])) {
            return redirect()->route('prints.show', $data['print_id']);
        } elseif (isset($data['wash_id'])) {
            return redirect()->route('washes.show', $data['wash_id']);
        }
        return back();
    }

    // Mark all as read
    public function markAllAsRead()
    {
        auth()->user()->unreadNotifications->markAsRead();
        return back();
    }

    // Notification index page
    public function index()
    {
        $notifications = auth()->user()->notifications()->latest()->paginate(20);
        return view('notifications.index', compact('notifications'));
    }
}
