<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $notifications = $request->user()
            ->notifications()
            ->latest()
            ->paginate(15)
            ->through(fn ($notification) => [
                'id' => $notification->id,
                'title' => data_get($notification->data, 'title', 'Notifikasi'),
                'message' => data_get($notification->data, 'message', ''),
                'type' => data_get($notification->data, 'type', 'general'),
                'action' => data_get($notification->data, 'action'),
                'read_at' => $notification->read_at,
                'created_at' => optional($notification->created_at)->toIso8601String(),
            ]);

        return Inertia::render('Notifications/Index', [
            'notifications' => $notifications,
        ]);
    }

    public function markAsRead(Request $request, string $notification)
    {
        $record = $request->user()->notifications()->whereKey($notification)->firstOrFail();

        if (is_null($record->read_at)) {
            $record->markAsRead();
        }

        return back()->with('success', 'Notifikasi ditandai sudah dibaca.');
    }

    public function readAll(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();

        return back()->with('success', 'Semua notifikasi sudah ditandai dibaca.');
    }
}
