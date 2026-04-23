<?php

namespace App\Notifications;

use App\Models\Announcement;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AnnouncementPublishedNotification extends Notification
{
    use Queueable;

    public function __construct(protected Announcement $announcement)
    {
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'title' => $this->announcement->title,
            'message' => str($this->announcement->content)->limit(120)->toString(),
            'type' => 'announcement',
            'action' => 'published',
            'announcement_id' => $this->announcement->id,
            'announcement_type' => $this->announcement->type,
        ];
    }
}
