<?php

namespace App\Http\Controllers\Candidate;

use App\Http\Controllers\Controller;
use App\Models\Notification;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = auth()->user()->notifications()->latest()->paginate(15);

        return view('pelamar.notifications', ['notifications' => $notifications]);
    }

    public function read(Notification $notification)
    {
        abort_unless($notification->user_id === auth()->id(), 403);
        $notification->update(['is_read' => true]);

        if (request()->wantsJson()) {
            return response()->json(['ok' => true]);
        }

        return back();
    }

    public function readAll()
    {
        auth()->user()->notifications()->unread()->update(['is_read' => true]);

        if (request()->wantsJson()) {
            return response()->json(['ok' => true]);
        }

        return back();
    }
}
