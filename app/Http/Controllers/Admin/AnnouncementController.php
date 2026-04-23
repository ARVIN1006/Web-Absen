<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\User;
use App\Notifications\AnnouncementPublishedNotification;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function index()
    {
        $announcements = Announcement::latest()->get();
        return \Inertia\Inertia::render('Admin/Announcements', [
            'announcements' => $announcements
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'type' => 'nullable|in:info,warning,urgent,event',
        ]);

        $announcement = Announcement::create([
            ...$validated,
            'is_active' => true,
        ]);

        app(\App\Services\ActivityLogService::class)->log(
            auth()->id(),
            $announcement,
            'announcement.created',
            'Menerbitkan pengumuman ' . $announcement->title,
            ['type' => $announcement->type]
        );

        User::query()
            ->where('is_active', true)
            ->get()
            ->each(fn ($user) => $user->notify(new AnnouncementPublishedNotification($announcement)));

        return redirect()->route('admin.announcements.index')->with('success', 'Pengumuman berhasil diterbitkan.');
    }

    public function update(Request $request, Announcement $announcement)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'type' => 'nullable|in:info,warning,urgent,event',
        ]);

        $announcement->update($validated);

        app(\App\Services\ActivityLogService::class)->log(
            auth()->id(),
            $announcement,
            'announcement.updated',
            'Memperbarui pengumuman ' . $announcement->title,
            ['type' => $announcement->type]
        );

        return redirect()->route('admin.announcements.index')->with('success', 'Pengumuman berhasil diupdate.');
    }

    public function toggle(Announcement $announcement)
    {
        $announcement->update(['is_active' => !$announcement->is_active]);

        app(\App\Services\ActivityLogService::class)->log(
            auth()->id(),
            $announcement,
            'announcement.toggled',
            ($announcement->is_active ? 'Mengaktifkan' : 'Menonaktifkan') . ' pengumuman ' . $announcement->title,
            ['is_active' => $announcement->is_active]
        );

        return redirect()->back()->with('success', 'Status pengumuman berhasil diubah.');
    }

    public function destroy(Announcement $announcement)
    {
        app(\App\Services\ActivityLogService::class)->log(
            auth()->id(),
            $announcement,
            'announcement.deleted',
            'Menghapus pengumuman ' . $announcement->title,
            ['type' => $announcement->type]
        );

        $announcement->delete();
        return redirect()->route('admin.announcements.index')->with('success', 'Pengumuman berhasil dihapus.');
    }
}
